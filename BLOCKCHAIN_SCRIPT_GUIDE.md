# Hướng dẫn sửa Script Blockchain

## Vấn đề
Hardhat không hỗ trợ truyền tham số trực tiếp sau `--` trong command line. Laravel backend đã được sửa để truyền tham số qua biến môi trường.

## Scripts cần sửa

### 1. `scripts/store-hash.js`
Script này cần đọc tham số từ biến môi trường thay vì `process.argv`:

**Trước (SAI):**
```javascript
const args = process.argv.slice(2);
const orderCode = args[0];
const hash = args[1];
const contractAddr = args[2];
```

**Sau (ĐÚNG):**
```javascript
const orderCode = process.env.ORDER_CODE;
const hash = process.env.HASH;
const contractAddr = process.env.CONTRACT_ADDR;

if (!orderCode || !hash || !contractAddr) {
    console.error(JSON.stringify({
        status: 'error',
        message: 'Missing required environment variables: ORDER_CODE, HASH, CONTRACT_ADDR'
    }));
    process.exit(1);
}
```

### 2. `scripts/get-hash.js`
Script này cũng cần đọc từ biến môi trường:

**Trước (SAI):**
```javascript
const args = process.argv.slice(2);
const orderCode = args[0];
const contractAddr = args[1];
```

**Sau (ĐÚNG):**
```javascript
const orderCode = process.env.ORDER_CODE;
const contractAddr = process.env.CONTRACT_ADDR;

if (!orderCode || !contractAddr) {
    console.error(JSON.stringify({
        status: 'error',
        message: 'Missing required environment variables: ORDER_CODE, CONTRACT_ADDR'
    }));
    process.exit(1);
}
```

## Format Output

Cả hai script cần output JSON để Laravel có thể parse:

**store-hash.js output:**
```javascript
console.log(JSON.stringify({
    status: 'success',
    tx_hash: '0x...', // Transaction hash từ blockchain
    order_code: orderCode
}));
```

**get-hash.js output:**
```javascript
console.log(JSON.stringify({
    status: 'success',
    hash: '...', // Hash từ blockchain
    order_code: orderCode
}));
```

## Testing

Sau khi sửa script, test bằng cách:

**Windows:**
```powershell
cd <BLOCKCHAIN_PATH>
$env:ORDER_CODE="TEST123"
$env:HASH="abc123"
$env:CONTRACT_ADDR="0x..."
npx hardhat run scripts/store-hash.js --network localhost
```

**Linux/Mac:**
```bash
cd <BLOCKCHAIN_PATH>
ORDER_CODE="TEST123" HASH="abc123" CONTRACT_ADDR="0x..." npx hardhat run scripts/store-hash.js --network localhost
```

## Lưu ý

- Đảm bảo script output JSON để Laravel có thể parse
- Xử lý lỗi và output JSON với `status: 'error'`
- Kiểm tra biến môi trường trước khi sử dụng

