<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Order;
// use App\Models\Booking; // Nhớ import model của bạn nếu có

class BookingController extends Controller
{
    // API 1: Tạo đơn hàng & Ghi Blockchain
    public function createBooking(Request $request)
    {
        // 1. Giả lập lưu vào DB
        $orderId = 'ORD-' . time();
        $amount = $request->input('amount', 500000);
        $customer = $request->input('customer', 'Khach Hang A');
        
        // Tạo chuỗi để băm (Thông tin quan trọng không được sửa đổi)
        $dataToHash = "{$orderId}|{$amount}|{$customer}";
        $hash = hash('sha256', $dataToHash);

        // 2. Lưu tạm vào DB (Giả lập)
        // Booking::create(['order_id' => $orderId, 'hash' => $hash, 'status' => 'pending']);

        // 3. Gọi script Node.js để ghi lên Blockchain
        $nodeScript = "scripts/store-hash.ts";
        $workingDir = env('BLOCKCHAIN_PATH'); 
        $contractAddr = env('SMART_CONTRACT_ADDRESS');

        if (!$workingDir || !$contractAddr) {
            Log::error('Missing BLOCKCHAIN_PATH or SMART_CONTRACT_ADDRESS in .env');
            return response()->json(['success' => false, 'message' => 'Blockchain configuration missing'], 500);
        }

        // Hardhat không hỗ trợ truyền tham số trực tiếp sau --, nên dùng biến môi trường
        // Script store-hash.ts cần đọc từ process.env.ORDER_CODE, process.env.HASH, process.env.CONTRACT_ADDR, process.env.PRIVATE_KEY
        $privateKey = env('BLOCKCHAIN_PRIVATE_KEY', '');
        
        if (empty($privateKey)) {
            Log::error('BLOCKCHAIN_PRIVATE_KEY not configured in .env');
            return response()->json(['success' => false, 'message' => 'Blockchain private key not configured'], 500);
        }
        
        $isWindows = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
        if ($isWindows) {
            // Windows: Dùng PowerShell để set biến môi trường và chạy command
            $command = sprintf(
                'cd /d %s && powershell -Command "$env:ORDER_CODE=\'%s\'; $env:HASH=\'%s\'; $env:CONTRACT_ADDR=\'%s\'; $env:PRIVATE_KEY=\'%s\'; npx hardhat run %s --network localhost"',
                escapeshellarg($workingDir),
                addslashes($orderId),
                addslashes($hash),
                addslashes($contractAddr),
                addslashes($privateKey),
                $nodeScript
            );
        } else {
            // Linux/Mac: Dùng biến môi trường trực tiếp
            $envVars = sprintf(
                'ORDER_CODE=%s HASH=%s CONTRACT_ADDR=%s PRIVATE_KEY=%s',
                escapeshellarg($orderId),
                escapeshellarg($hash),
                escapeshellarg($contractAddr),
                escapeshellarg($privateKey)
            );
            $command = "cd " . escapeshellarg($workingDir) . " && {$envVars} npx hardhat run {$nodeScript} --network localhost";
        }
        
        // Thực thi lệnh và lấy output
        $output = [];
        $returnVar = 0;
        exec($command . ' 2>&1', $output, $returnVar);

        if ($returnVar === 0) {
            // Lấy output JSON từ script JS
            // Dùng regex để lấy dòng JSON cuối cùng (tránh các log rác của hardhat)
            $outputString = implode("\n", $output);
            preg_match('/\{.*\}/s', $outputString, $matches);
            $jsonOutput = json_decode($matches[0] ?? '{}', true);

            if (($jsonOutput['status'] ?? '') === 'success') {
                // Update TX_HASH vào DB
                // Booking::where('order_id', $orderId)->update(['tx_hash' => $jsonOutput['tx_hash']]);

                return response()->json([
                    'success' => true,
                    'order_id' => $orderId,
                    'tx_hash' => $jsonOutput['tx_hash'],
                    'qr_url' => "http://localhost:3000/verify/{$orderId}" // Link sang Nuxt
                ]);
            }
        }

        // Xử lý lỗi
        Log::error('Blockchain write failed', [
            'output' => $output,
            'return_var' => $returnVar
        ]);
        return response()->json(['success' => false, 'message' => 'Blockchain write failed'], 500);
    }

    // API 2: Xác thực (Dùng cho trang Verify của Nuxt)
    public function verifyBooking($orderCode)
    {
        // 1. Lấy thông tin đơn hàng từ DB
        $order = Order::with(['orderer', 'orderdetails'])->where('order_code', $orderCode)->first();

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy đơn hàng'
            ], 404);
        }

        $orderer = $order->orderer;
        $dbHash = $order->invoice_hash; // Hash cũ đã lưu trong DB

        if (!$dbHash) {
            return response()->json([
                'success' => false,
                'message' => 'Đơn hàng chưa có hash để xác thực'
            ], 400);
        }

        // 2. TÍNH LẠI HASH từ dữ liệu hiện tại trong database
        // Để phát hiện nếu dữ liệu đã bị thay đổi sau khi tạo đơn hàng
        // Công thức hash phải giống với khi tạo đơn hàng
        $currentDataToHash = sprintf(
            "%s|%s|%s|%s|%.2f",
            $order->order_code,
            $orderer->orderer_name ?? '',      // Dữ liệu hiện tại
            $orderer->orderer_email ?? '',     // Dữ liệu hiện tại
            $orderer->orderer_phone ?? '',     // Dữ liệu hiện tại
            (float) $order->total_price        // Dữ liệu hiện tại
        );
        $currentHash = hash('sha256', $currentDataToHash);

        $nodeScript = "scripts/get-hash.ts";
        $workingDir = env('BLOCKCHAIN_PATH'); 
        $contractAddr = env('SMART_CONTRACT_ADDRESS');

        if (!$workingDir || !$contractAddr) {
            return response()->json([
                'order_code' => $orderCode,
                'order_id' => $order->order_id,
                'customer' => $orderer->orderer_name ?? 'N/A',
                'amount' => $order->total_price,
                'db_hash' => $dbHash,
                'contract_address' => $contractAddr,
                'blockchain_hash' => '',
                'error' => 'Blockchain configuration missing'
            ]);
        }

        // Hardhat không hỗ trợ truyền tham số trực tiếp sau --, nên dùng biến môi trường
        // Script get-hash.js cần đọc từ process.env.ORDER_CODE, process.env.CONTRACT_ADDR
        $isWindows = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
        if ($isWindows) {
            // Windows: Dùng PowerShell để set biến môi trường và chạy command
            $command = sprintf(
                'cd /d %s && powershell -Command "$env:ORDER_CODE=\'%s\'; $env:CONTRACT_ADDR=\'%s\'; npx hardhat run %s --network localhost"',
                escapeshellarg($workingDir),
                addslashes($orderCode),
                addslashes($contractAddr),
                $nodeScript
            );
        } else {
            // Linux/Mac: Dùng biến môi trường trực tiếp
            $envVars = sprintf(
                'ORDER_CODE=%s CONTRACT_ADDR=%s',
                escapeshellarg($orderCode),
                escapeshellarg($contractAddr)
            );
            $command = "cd " . escapeshellarg($workingDir) . " && {$envVars} npx hardhat run {$nodeScript} --network localhost";
        }
        $output = [];
        $returnVar = 0;
        exec($command . ' 2>&1', $output, $returnVar);

        $blockchainHash = '';
        if ($returnVar === 0) {
            $outputString = implode("\n", $output);
            preg_match('/\{.*\}/s', $outputString, $matches);
            $jsonOutput = json_decode($matches[0] ?? '{}', true);
            $blockchainHash = $jsonOutput['hash'] ?? '';
        }
        
        // 4. SO SÁNH: Hash hiện tại (từ dữ liệu DB hiện tại) với Hash trên blockchain
        // Nếu khác nhau → dữ liệu đã bị thay đổi → HÓA ĐƠN GIẢ MẠO
        $isValid = ($currentHash === $blockchainHash && !empty($blockchainHash));
        $dataChanged = ($currentHash !== $dbHash); // Hash hiện tại khác hash cũ → dữ liệu đã thay đổi
        
        return response()->json([
            'order_code' => $orderCode,
            'order_id' => $order->order_id,
            'customer' => $orderer->orderer_name ?? 'N/A',
            'customer_email' => $orderer->orderer_email ?? 'N/A',
            'customer_phone' => $orderer->orderer_phone ?? 'N/A',
            'amount' => $order->total_price,
            'db_hash' => $dbHash,              // Hash cũ đã lưu trong DB khi tạo đơn
            'current_hash' => $currentHash,    // Hash mới tính từ dữ liệu hiện tại
            'blockchain_hash' => $blockchainHash, // Hash trên blockchain (bất biến)
            'blockchain_tx_hash' => $order->blockchain_tx_hash,
            'contract_address' => $contractAddr,
            'is_valid' => $isValid,            // So sánh current_hash với blockchain_hash
            'data_changed' => $dataChanged     // Nếu true → dữ liệu đã bị thay đổi sau khi tạo đơn
        ]);
    }
}

