<?php

namespace Database\Seeders;

use App\Models\Hotel;
use App\Models\Room;
use App\Models\TypeRoom;
use App\Models\Area;
use App\Models\Brand;
use App\Models\Admin;
use App\Models\Roles;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HotelRoomSeeder extends Seeder
{
    public function run()
    {
        $brand = Brand::first();
        if (!$brand) {
            $brand = Brand::create(['brand_name' => 'Premium Collection']);
        }

        // Kiểm tra role hotel_manager tồn tại
        $hotelManagerRole = Roles::where('roles_name', 'hotel_manager')->first();
        if (!$hotelManagerRole) {
            $this->command->error("LỖI: Không tìm thấy role 'hotel_manager' trong database!");
            $this->command->info("Vui lòng chạy RolesTableSeeder trước: php artisan db:seed --class=RolesTableSeeder");
            return;
        }

        $existingHotels = Hotel::pluck('hotel_name')->toArray();
        $allHotels = $this->getAll50Hotels();

        $createdCount = 0;
        $failedCount = 0;
        $skippedCount = 0;

        foreach ($allHotels as $hotelData) {
            // Kiểm tra hotel đã tồn tại
            if (in_array($hotelData['hotel_name'], $existingHotels)) {
                $this->command->warn("Khách sạn {$hotelData['hotel_name']} đã tồn tại, bỏ qua.");
                $skippedCount++;
                continue;
            }

            // Transaction cho từng hotel riêng biệt
            DB::beginTransaction();
            try {
                $this->command->info("Đang tạo: {$hotelData['hotel_name']}");

                // Tạo hotel
                $hotel = Hotel::create([
                    'hotel_name' => $hotelData['hotel_name'],
                    'hotel_rank' => $hotelData['rank'],
                    'hotel_type' => $hotelData['type'],
                    'brand_id' => $brand->brand_id,
                    'area_id' => $hotelData['area_id'],
                    'hotel_image' => '', // Chuỗi rỗng thay vì null
                    'hotel_price_average' => $this->generateAveragePrice($hotelData['rank']),
                    'hotel_placedetails' => $hotelData['address'],
                    'hotel_linkplace' => 'https://maps.google.com/?q=' . urlencode($hotelData['hotel_name'] . ' ' . $hotelData['address']),
                    'hotel_jfameplace' => $this->getAreaNameById($hotelData['area_id']),
                    'hotel_desc' => $hotelData['desc'],
                    'hotel_tag_keyword' => $this->generateTags($hotelData['type'], $hotelData['area_id']),
                    'hotel_view' => rand(150, 8000),
                    'hotel_status' => 1,
                ]);

                // Tạo rooms và type rooms
                $roomsCreated = 0;
                foreach ($hotelData['rooms'] as $roomName) {
                    $room = Room::create([
                        'hotel_id' => $hotel->hotel_id,
                        'room_name' => $roomName,
                        'room_amount_of_people' => rand(2, 4),
                        'room_acreage' => rand(28, 85), // Chỉ lưu số, không có đơn vị
                        'room_view' => $this->getRandomView(),
                        'room_status' => 1,
                    ]);

                    // Tạo 3 type rooms cho mỗi room
                    $this->createTypeRoomsForRoom($room->room_id);
                    $roomsCreated++;
                }

                // Tạo hotel_manager cho khách sạn (bắt buộc)
                $this->createHotelManager($hotel, $hotelManagerRole);

                DB::commit();
                $existingHotels[] = $hotel->hotel_name;
                $createdCount++;
                $this->command->info("  ✓ Đã tạo thành công: {$hotel->hotel_name} ({$roomsCreated} phòng)");
                
            } catch (\Exception $e) {
                DB::rollBack();
                $failedCount++;
                $this->command->error("  ✗ Lỗi khi tạo {$hotelData['hotel_name']}: " . $e->getMessage());
                $this->command->error("    Chi tiết: " . $e->getFile() . " dòng " . $e->getLine());
            }
        }

        // Tổng kết
        $this->command->info("\n" . str_repeat("=", 60));
        $this->command->info("KẾT QUẢ SEEDER:");
        $this->command->info("  ✓ Thành công: {$createdCount} khách sạn");
        $this->command->info("  ✗ Thất bại: {$failedCount} khách sạn");
        $this->command->info("  ⊘ Đã bỏ qua: {$skippedCount} khách sạn (trùng tên)");
        $this->command->info(str_repeat("=", 60));
    }

    private function getAll50Hotels()
    {
        return [
            // ==================== ĐÀ NẴNG - SƠN TRÀ (15 khách sạn) ====================
            [
                'hotel_name' => 'A La Carte Đà Nẵng Beach',
                'area_id' => 8, 'type' => 3, 'rank' => 5,
                'address' => '200 Võ Nguyên Giáp, Phường Phước Mỹ, Quận Sơn Trà',
                'desc' => 'Khu nghỉ dưỡng cao cấp với phòng view biển tuyệt đẹp, nhiều hồ bơi và nhà hàng đa dạng.',
                'rooms' => ['Deluxe Ocean View', 'Suite Pool Access', 'Family Connecting Room', 'Premium Bay View']
            ],
            [
                'hotel_name' => 'Risemount Resort Đà Nẵng',
                'area_id' => 8, 'type' => 3, 'rank' => 4,
                'address' => 'Lô A1 Lê Văn Duyệt, Phường Nại Hiên Đông, Quận Sơn Trà',
                'desc' => 'Resort với kiến trúc hiện đại, hồ bơi vô cực view biển, gần biển Mỹ Khê.',
                'rooms' => ['Deluxe Ocean View', 'Superior City View', 'Suite với Ban Công', 'Family Room']
            ],
            [
                'hotel_name' => 'Citadines Blue Cove Đà Nẵng',
                'area_id' => 8, 'type' => 2, 'rank' => 4,
                'address' => 'Nghiêm Sỹ Tăng, Phường Nại Hiên Đông, Quận Sơn Trà',
                'desc' => 'Căn hộ dịch vụ hiện đại, đầy đủ tiện nghi bếp, view vịnh biển xanh, phù hợp gia đình.',
                'rooms' => ['Studio Deluxe', '1-Bedroom Apartment', '2-Bedroom Apartment Sea View']
            ],
            [
                'hotel_name' => 'Stella Maris Beach Đà Nẵng',
                'area_id' => 8, 'type' => 1, 'rank' => 4,
                'address' => 'Võ Nguyên Giáp, Phường Phước Mỹ, Quận Sơn Trà',
                'desc' => 'Khách sạn beachfront mới, thiết kế Bắc Âu tinh tế, hồ bơi rooftop view biển hoàng hôn.',
                'rooms' => ['Superior Ocean View', 'Deluxe Balcony', 'Corner Suite', 'Family Room với Giường Tầng']
            ],
            [
                'hotel_name' => 'Mỹ Khê Beach Hotel',
                'area_id' => 8, 'type' => 1, 'rank' => 3,
                'address' => '241 Nguyễn Văn Thoại, Phường Mỹ An, Quận Ngũ Hành Sơn',
                'desc' => 'Khách sạn giá tốt cách biển Mỹ Khê vài bước chân, phòng sạch sẽ, nhân viên thân thiện.',
                'rooms' => ['Standard Double', 'Superior Ocean View', 'Triple Room']
            ],
            [
                'hotel_name' => 'Sơn Trà Hotel & Resort',
                'area_id' => 8, 'type' => 3, 'rank' => 4,
                'address' => 'Phước Mỹ, Quận Sơn Trà',
                'desc' => 'Khu nghỉ dưỡng yên tĩnh trên bán đảo Sơn Trà, view vịnh biển và rừng nguyên sinh.',
                'rooms' => ['Bungalow Garden View', 'Villa Ocean View', 'Deluxe Sea View Room']
            ],
            [
                'hotel_name' => 'Danang Seashore Hotel & Apartment',
                'area_id' => 8, 'type' => 2, 'rank' => 4,
                'address' => '225 Võ Nguyên Giáp, Phường Phước Mỹ, Quận Sơn Trà',
                'desc' => 'Tổ hợp căn hộ dịch vụ và khách sạn view biển Mỹ Khê, thiết kế hiện đại, đầy đủ tiện nghi.',
                'rooms' => ['Ocean Studio', 'One-Bedroom Seaview', 'Two-Bedroom Family Suite']
            ],
            [
                'hotel_name' => 'The Shells Resort & Spa',
                'area_id' => 8, 'type' => 3, 'rank' => 5,
                'address' => '215 Võ Nguyên Giáp, Phường Phước Mỹ, Quận Sơn Trà',
                'desc' => 'Resort cao cấp với kiến trúc hình vỏ sò độc đáo, hồ bơi vô cực lớn nhất Đà Nẵng.',
                'rooms' => ['Shell Deluxe Ocean', 'Pool Access Suite', 'Family Bungalow', 'Presidential Villa']
            ],
            [
                'hotel_name' => 'Ocean Breeze Hotel',
                'area_id' => 8, 'type' => 1, 'rank' => 3,
                'address' => '178 Võ Nguyên Giáp, Phường Phước Mỹ, Quận Sơn Trà',
                'desc' => 'Khách sạn nhỏ view biển, giá cả phải chăng, không gian ấm cúng, phù hợp cho cặp đôi.',
                'rooms' => ['Standard Sea View', 'Deluxe Balcony', 'Ocean Breeze Suite']
            ],
            [
                'hotel_name' => 'Sơn Trà Bay Resort',
                'area_id' => 8, 'type' => 3, 'rank' => 4,
                'address' => 'Khu vực Đảo Xanh, Bán đảo Sơn Trà',
                'desc' => 'Resort eco-friendly với bãi biển riêng tư, view vịnh Tiên Sa, hoạt động lặn biển.',
                'rooms' => ['Bay View Bungalow', 'Beach Front Villa', 'Garden Suite', 'Family Room']
            ],
            [
                'hotel_name' => 'Mango Bay Hotel',
                'area_id' => 8, 'type' => 1, 'rank' => 3,
                'address' => '123 Nguyễn Văn Thoại, Phường Mỹ An, Quận Ngũ Hành Sơn',
                'desc' => 'Khách sạn boutique với khu vườn nhiệt đới, hồ bơi nhỏ, không khí thư giãn.',
                'rooms' => ['Mango Deluxe', 'Superior Garden', 'Family Room với Kitchenette']
            ],
            [
                'hotel_name' => 'Seaside Apartments',
                'area_id' => 8, 'type' => 2, 'rank' => 4,
                'address' => '189 Võ Nguyên Giáp, Phường Phước Mỹ, Quận Sơn Trà',
                'desc' => 'Căn hộ dịch vụ full tiện nghi, view biển toàn cảnh, có bếp và máy giặt riêng.',
                'rooms' => ['Studio Ocean Front', '1-Bedroom Seaview Apartment', '2-Bedroom Penthouse']
            ],
            [
                'hotel_name' => 'Azure Hotel & Spa',
                'area_id' => 8, 'type' => 1, 'rank' => 4,
                'address' => '205 Võ Nguyên Giáp, Phường Phước Mỹ, Quận Sơn Trà',
                'desc' => 'Khách sạn 4 sao với spa trị liệu đẳng cấp, hồ bơi rooftop, phòng thiết kế tối giản.',
                'rooms' => ['Azure Deluxe', 'Executive Ocean Suite', 'Spa Room với Jacuzzi']
            ],
            [
                'hotel_name' => 'Monkey Island Resort',
                'area_id' => 8, 'type' => 3, 'rank' => 4,
                'address' => 'Khu vực Bãi Bắc, Bán đảo Sơn Trà',
                'desc' => 'Khu nghỉ dưỡng gần rừng nguyên sinh, có thể ngắm khỉ tự nhiên, không khí trong lành.',
                'rooms' => ['Jungle View Room', 'Monkey Suite', 'Family Bungalow', 'Deluxe Forest View']
            ],
            [
                'hotel_name' => 'Horizon Beach Hotel',
                'area_id' => 8, 'type' => 1, 'rank' => 3,
                'address' => '167 Võ Nguyên Giáp, Phường Phước Mỹ, Quận Sơn Trà',
                'desc' => 'Khách sạn tầm trung với ban công view biển trực diện, gần các nhà hàng hải sản.',
                'rooms' => ['Horizon Standard', 'Superior Sea View', 'Family Connecting']
            ],

            // ==================== ĐÀ NẴNG - NGŨ HÀNH SƠN (12 khách sạn) ====================
            [
                'hotel_name' => 'Danang Marriott Resort & Spa, Non Nuoc Beach Villas',
                'area_id' => 7, 'type' => 3, 'rank' => 5,
                'address' => '23 Trường Sa, Phường Hòa Hải, Quận Ngũ Hành Sơn',
                'desc' => 'Khu nghỉ dưỡng villa sang trọng bậc nhất bãi biển Non Nước, view biển trực diện.',
                'rooms' => ['Villa 1 Phòng Ngủ Private Pool', 'Villa 2 Phòng Ngủ', 'Deluxe Ocean Front', 'Suite Garden View']
            ],
            [
                'hotel_name' => 'The Ocean Villas',
                'area_id' => 7, 'type' => 2, 'rank' => 5,
                'address' => 'Trường Sa, Phường Hòa Hải, Quận Ngũ Hành Sơn',
                'desc' => 'Căn hộ villa cao cấp với hồ bơi riêng, view biển Non Nước, thiết kế tối giản sang trọng.',
                'rooms' => ['2-Bedroom Villa with Pool', '3-Bedroom Ocean View Villa', '1-Bedroom Garden Villa']
            ],
            [
                'hotel_name' => 'Pullman Danang Beach Resort',
                'area_id' => 7, 'type' => 3, 'rank' => 5,
                'address' => '101 Võ Nguyên Giáp, Phường Hòa Hải, Quận Ngũ Hành Sơn',
                'desc' => 'Resort 5 sao quốc tế trên bãi biển Phạm Văn Đồng, có sân golf và spa lớn.',
                'rooms' => ['Superior Garden View', 'Deluxe Ocean View', 'Lagoon Access Room', 'Beach Front Suite']
            ],
            [
                'hotel_name' => 'Hyatt Regency Danang Resort and Spa',
                'area_id' => 7, 'type' => 3, 'rank' => 5,
                'address' => 'Trường Sa, Phường Hòa Hải, Quận Ngũ Hành Sơn',
                'desc' => 'Khu nghỉ dưỡng cao cấp với khuôn viên rộng lớn, nhiều hồ bơi, nhà hàng và sân golf.',
                'rooms' => ['Regency Club Room', 'Two-Bedroom Villa', 'Ocean View Suite', 'Family Regency Room']
            ],
            [
                'hotel_name' => 'Brilliant Hotel & Condo',
                'area_id' => 7, 'type' => 2, 'rank' => 4,
                'address' => 'Bãi biển Non Nước, Phường Hòa Hải, Quận Ngũ Hành Sơn',
                'desc' => 'Tổ hợp khách sạn và căn hộ dịch vụ view biển Non Nước, có hồ bơi vô cực trên cao.',
                'rooms' => ['Condo Studio Ocean View', '1-Bedroom Condo', 'Hotel Deluxe Room', 'Two-Bedroom Suite']
            ],
            [
                'hotel_name' => 'The Bay View Hotel & Apartment',
                'area_id' => 7, 'type' => 2, 'rank' => 4,
                'address' => '25 Nguyễn Văn Thoại, Phường Mỹ An, Quận Ngũ Hành Sơn',
                'desc' => 'Căn hộ dịch vụ và khách sạn hiện đại, tầm nhìn toàn cảnh vịnh Đà Nẵng.',
                'rooms' => ['Bay View Studio', 'One-Bedroom Apartment', 'Superior Hotel Room']
            ],
            [
                'hotel_name' => 'Vinpearl Luxury Đà Nẵng',
                'area_id' => 7, 'type' => 3, 'rank' => 5,
                'address' => '07 Trường Sa, Phường Hòa Hải, Quận Ngũ Hành Sơn',
                'desc' => 'Khu nghỉ dưỡng villa và condotel cao cấp của tập đoàn Vinpearl, tiện nghi 5 sao.',
                'rooms' => ['Luxury Ocean Villa', 'Two-Bedroom Condotel', 'Premium Suite', 'Garden Pool Villa']
            ],
            [
                'hotel_name' => 'Mường Thanh Luxury Đà Nẵng',
                'area_id' => 7, 'type' => 1, 'rank' => 5,
                'address' => 'Bãi biển Non Nước, Phường Hòa Hải, Quận Ngũ Hành Sơn',
                'desc' => 'Khách sạn 5 sao thuộc tập đoàn Mường Thanh, nằm trên bãi biển Non Nước.',
                'rooms' => ['Superior Ocean', 'Deluxe Balcony', 'Executive Suite', 'Family Suite với Kitchenette']
            ],
            [
                'hotel_name' => 'Mikazuki Japanese Resorts & Spa Đà Nẵng',
                'area_id' => 7, 'type' => 3, 'rank' => 5,
                'address' => 'Ngũ Hành Sơn',
                'desc' => 'Khu nghỉ dưỡng Nhật Bản đầu tiên tại Việt Nam, mang đậm văn hóa Onsen và kiến trúc Nhật.',
                'rooms' => ['Japanese Style Room', 'Suite với Onsen Riêng', 'Family Tatami Room', 'Garden View Villa']
            ],
            [
                'hotel_name' => 'Non Nuoc Beach Hotel',
                'area_id' => 7, 'type' => 1, 'rank' => 4,
                'address' => '89 Trường Sa, Phường Hòa Hải, Quận Ngũ Hành Sơn',
                'desc' => 'Khách sạn 4 sao ngay bãi biển Non Nước, view núi Ngũ Hành Sơn và biển.',
                'rooms' => ['Non Nuoc Deluxe', 'Ocean Front Suite', 'Superior Garden View']
            ],
            [
                'hotel_name' => 'Marble Mountains View Hotel',
                'area_id' => 7, 'type' => 1, 'rank' => 3,
                'address' => '45 Huyền Trân Công Chúa, Phường Hòa Hải, Quận Ngũ Hành Sơn',
                'desc' => 'Khách sạn view trực diện Ngũ Hành Sơn, gần làng đá mỹ nghệ Non Nước.',
                'rooms' => ['Mountain View Room', 'Deluxe Balcony', 'Standard Double']
            ],
            [
                'hotel_name' => 'Hải Âu Resort',
                'area_id' => 7, 'type' => 3, 'rank' => 4,
                'address' => 'Trường Sa, Phường Hòa Hải, Quận Ngũ Hành Sơn',
                'desc' => 'Khu nghỉ dưỡng gia đình với hồ bơi trẻ em, khu vui chơi, gần biển và chợ hải sản.',
                'rooms' => ['Family Bungalow', 'Seagull Suite', 'Garden Room', 'Connecting Room']
            ],

            // ==================== ĐÀ NẴNG - HẢI CHÂU (10 khách sạn) ====================
            [
                'hotel_name' => 'Novotel Danang Premier Han River',
                'area_id' => 4, 'type' => 1, 'rank' => 5,
                'address' => '36 Bạch Đằng, Phường Thạch Thang, Quận Hải Châu',
                'desc' => 'Khách sạn 5 sao bên bờ sông Hàn, có tầm nhìn đẹp ra cầu Rồng và cầu Sông Hàn.',
                'rooms' => ['Superior River View', 'Deluxe Balcony', 'Executive Suite', 'Novotel Room']
            ],
            [
                'hotel_name' => 'Grand Mercure Danang',
                'area_id' => 4, 'type' => 1, 'rank' => 4,
                'address' => '01 đường 2/9, Phường Bình Hiên, Quận Hải Châu',
                'desc' => 'Khách sạn 4 sao tiêu chuẩn quốc tế tại trung tâm, gần trung tâm mua sắm và bến du thuyền.',
                'rooms' => ['Standard Room', 'Grand Mercure Room', 'Suite với Phòng Khách']
            ],
            [
                'hotel_name' => 'River Green City Hotel',
                'area_id' => 4, 'type' => 1, 'rank' => 3,
                'address' => '177 Trần Phú, Phường Thạch Thang, Quận Hải Châu',
                'desc' => 'Khách sạn 3 sao view sông Hàn, vị trí thuận tiện để khám phá ẩm thực và đời sống đêm.',
                'rooms' => ['Superior City', 'Deluxe River', 'Family Room']
            ],
            [
                'hotel_name' => 'Melia Vinpearl Riverfront Đà Nẵng',
                'area_id' => 4, 'type' => 1, 'rank' => 5,
                'address' => '341 Trần Hưng Đạo, Phường An Hải Trung, Quận Sơn Trà',
                'desc' => 'Khách sạn 5 sao cao tầng bên bờ sông, có hồ bơi vô cực và view toàn cảnh thành phố.',
                'rooms' => ['Deluxe River View', 'Executive Suite', 'Melia Room', 'Family Connecting']
            ],
            [
                'hotel_name' => 'Grand Tourane Đà Nẵng Hotel',
                'area_id' => 4, 'type' => 1, 'rank' => 4,
                'address' => '252 Trần Phú, Phường Thạch Thang, Quận Hải Châu',
                'desc' => 'Khách sạn 4 sao tại trung tâm thành phố, kiến trúc Pháp cổ điển, gần bảo tàng.',
                'rooms' => ['Superior Room', 'Deluxe City View', 'Grand Suite', 'Executive Room']
            ],
            [
                'hotel_name' => 'Golden Bay Hotel Đà Nẵng',
                'area_id' => 4, 'type' => 1, 'rank' => 4,
                'address' => '01 Lê Duẩn, Phường Thạch Thang, Quận Hải Châu',
                'desc' => 'Khách sạn cao tầng với view toàn cảnh vịnh Đà Nẵng, có nhà hàng xoay trên tầng cao.',
                'rooms' => ['Golden Bay Room', 'Deluxe Panorama', 'Executive Bay View Suite']
            ],
            [
                'hotel_name' => 'Century Riverside Hotel',
                'area_id' => 4, 'type' => 1, 'rank' => 4,
                'address' => '05 Bạch Đằng, Phường Thạch Thang, Quận Hải Châu',
                'desc' => 'Khách sạn ven sông với kiến trúc hiện đại, tầm nhìn đẹp ra cầu quay Sông Hàn.',
                'rooms' => ['Riverside Deluxe', 'Century Suite', 'Superior City', 'Family Room']
            ],
            [
                'hotel_name' => 'Danang Downtown Hotel',
                'area_id' => 4, 'type' => 1, 'rank' => 3,
                'address' => '78 Trần Phú, Phường Thạch Thang, Quận Hải Châu',
                'desc' => 'Khách sạn giá tốt tại trung tâm mua sắm, gần chợ Hàn, phù hợp khách du lịch bụi.',
                'rooms' => ['Standard Downtown', 'Superior Double', 'Triple Room']
            ],
            [
                'hotel_name' => 'Dragon Bridge Hotel',
                'area_id' => 4, 'type' => 1, 'rank' => 3,
                'address' => '112 Bạch Đằng, Phường Thạch Thang, Quận Hải Châu',
                'desc' => 'Khách sạn view cầu Rồng, đặc biệt đẹp vào cuối tuần khi cầu phun lửa và nước.',
                'rooms' => ['Bridge View Room', 'Deluxe Dragon View', 'Standard City']
            ],
            [
                'hotel_name' => 'Han River Boutique Hotel',
                'area_id' => 4, 'type' => 1, 'rank' => 4,
                'address' => '45 Bạch Đằng, Phường Thạch Thang, Quận Hải Châu',
                'desc' => 'Khách sạn boutique nhỏ xinh với thiết kế tinh tế, view sông Hàn, dịch vụ cá nhân hóa.',
                'rooms' => ['Boutique Deluxe', 'River Suite', 'Han River Room']
            ],

            // ==================== ĐÀ NẴNG - CÁC QUẬN KHÁC (8 khách sạn) ====================
            [
                'hotel_name' => 'Mercure Danang French Village Bana Hills',
                'area_id' => 5, 'type' => 1, 'rank' => 4,
                'address' => 'Bà Nà Hills, Hòa Ninh, Huyện Hòa Vang',
                'desc' => 'Khách sạn theo phong cách làng Pháp cổ tích nằm trên đỉnh Bà Nà Hills.',
                'rooms' => ['Superior Village View', 'Deluxe Balcony', 'Family Room', 'Suite']
            ],
            [
                'hotel_name' => 'Ixora Hotel by Fusion',
                'area_id' => 6, 'type' => 1, 'rank' => 4,
                'address' => 'Lô 1, Khu đô thị mới Phú Bài, Phường Hòa Hiệp Bắc, Quận Liên Chiểu',
                'desc' => 'Khách sạn thiết kế độc đáo với dịch vụ spa đặc trưng, nằm gần sân bay.',
                'rooms' => ['Ixora Cozy Room', 'Deluxe với Bồn Tắm', 'Fusion Suite', 'Room with Terrace']
            ],
            [
                'hotel_name' => 'The Luxe Hotel',
                'area_id' => 9, 'type' => 1, 'rank' => 3,
                'address' => '04 Nguyễn Văn Linh, Phường Thanh Khê Tây, Quận Thanh Khê',
                'desc' => 'Khách sạn hiện đại tại quận trung tâm, gần ga Đà Nẵng và chợ, thuận tiện công tác.',
                'rooms' => ['Standard Room', 'Luxe Deluxe', 'Executive Room']
            ],
            [
                'hotel_name' => 'Cẩm Lệ Hotel',
                'area_id' => 3, 'type' => 1, 'rank' => 3,
                'address' => 'Đường Nguyễn Hữu Thọ, Phường Hòa Xuân, Quận Cẩm Lệ',
                'desc' => 'Khách sạn mới tại quận Cẩm Lệ, gần sân vận động và khu thể thao, phòng rộng rãi.',
                'rooms' => ['Superior Double', 'Deluxe Twin', 'Suite']
            ],
            [
                'hotel_name' => 'Iris Hotel & Condo',
                'area_id' => 6, 'type' => 2, 'rank' => 4,
                'address' => 'Khu đô thị Đại Phú Gia, Phường Hòa Hiệp Bắc, Quận Liên Chiểu',
                'desc' => 'Tổ hợp khách sạn và căn hộ dịch vụ gần sân bay, phù hợp khách công tác dài ngày.',
                'rooms' => ['Studio Apartment', 'One-Bedroom Condo', 'Hotel Deluxe', 'Two-Bedroom Suite']
            ],
            [
                'hotel_name' => 'Airport View Hotel',
                'area_id' => 6, 'type' => 1, 'rank' => 3,
                'address' => '15 Nguyễn Văn Linh, Phường Hòa Thuận Tây, Quận Hải Châu',
                'desc' => 'Khách sạn gần sân bay Đà Nẵng, có shuttel bus miễn phí, phòng cách âm tốt.',
                'rooms' => ['Standard Airport View', 'Deluxe Soundproof', 'Executive Room']
            ],
            [
                'hotel_name' => 'Thanh Khê Central Hotel',
                'area_id' => 9, 'type' => 1, 'rank' => 3,
                'address' => '89 Hùng Vương, Phường Thanh Khê Tây, Quận Thanh Khê',
                'desc' => 'Khách sạn tại trung tâm quận Thanh Khê, gần bệnh viện và trung tâm thương mại.',
                'rooms' => ['Standard Room', 'Superior Double', 'Family Triple']
            ],
            [
                'hotel_name' => 'Cẩm Lệ Riverside Hotel',
                'area_id' => 3, 'type' => 1, 'rank' => 3,
                'address' => 'Bờ sông Cẩm Lệ, Phường Hòa Thọ Đông, Quận Cẩm Lệ',
                'desc' => 'Khách sạn view sông Cẩm Lệ, không khí trong lành, gần công viên và khu thể thao.',
                'rooms' => ['River View Room', 'Deluxe Balcony', 'Standard Garden View']
            ],

            // ==================== HỘI AN (5 khách sạn) ====================
            [
                'hotel_name' => 'Allegro Hoi An . A Little Luxury Hotel & Spa',
                'area_id' => 2, 'type' => 1, 'rank' => 4,
                'address' => '286 Lý Thường Kiệt, Phường Cẩm Phổ, Hội An',
                'desc' => 'Khách sạn boutique nhỏ xinh, phong cách kiến trúc Pháp cổ điển, gần phố cổ Hội An.',
                'rooms' => ['Little Luxury Room', 'Balcony Room', 'Allegro Suite', 'Junior Suite']
            ],
            [
                'hotel_name' => 'La Siesta Hoi An Resort & Spa',
                'area_id' => 2, 'type' => 3, 'rank' => 5,
                'address' => '132 Lý Thái Tổ, Phường Cẩm Châu, Hội An',
                'desc' => 'Khu nghỉ dưỡng 5 sao với hồ bơi lớn và khu vườn nhiệt đới, cách phố cổ 5 phút đi bộ.',
                'rooms' => ['Deluxe Balcony', 'Family Suite với Bể Sục', 'Garden View Bungalow', 'Pool Access Room']
            ],
            [
                'hotel_name' => 'Victoria Hoi An Beach Resort & Spa',
                'area_id' => 2, 'type' => 3, 'rank' => 4,
                'address' => 'Cửa Đại Beach, Hội An',
                'desc' => 'Khu nghỉ dưỡng mang phong cách thuộc địa Pháp, có bãi biển riêng, vườn nhiệt đới.',
                'rooms' => ['Superior Garden Bungalow', 'Deluxe Sea View', 'Victoria Suite', 'Family Room']
            ],
            [
                'hotel_name' => 'Hoi An Historic Hotel',
                'area_id' => 2, 'type' => 1, 'rank' => 4,
                'address' => '10 Trần Hưng Đạo, Phường Minh An, Hội An',
                'desc' => 'Khách sạn lịch sử được cải tạo từ biệt thự cổ, nằm ngay trong khu phố cổ.',
                'rooms' => ['Heritage Deluxe', 'Historic Suite', 'Garden View Room', 'Family Room với Courtyard']
            ],
            [
                'hotel_name' => 'Anio Hotel & Spa Hoi An',
                'area_id' => 2, 'type' => 1, 'rank' => 4,
                'address' => 'An Bàng Beach, Cẩm An, Hội An',
                'desc' => 'Khách sạn boutique trên bãi biển An Bàng yên tĩnh, thiết kế tối giản.',
                'rooms' => ['Anio Deluxe Sea', 'Pool Access Room', 'Suite với Outdoor Bath', 'Family Loft']
            ],
        ];
    }

    private function createTypeRoomsForRoom($roomId)
    {
        // Type room 1: 1 giường đơn
        $condition1 = rand(0, 1);
        TypeRoom::create([
            'room_id' => $roomId,
            'type_room_bed' => 1,
            'type_room_price' => rand(800000, 1800000),
            'type_room_condition' => $condition1,
            'type_room_price_sale' => $condition1 == 1 ? rand(5, 20) : 0,
            'type_room_status' => 1,
            'type_room_quantity' => rand(5, 25),
        ]);

        // Type room 2: 2 giường đơn
        $condition2 = rand(0, 1);
        TypeRoom::create([
            'room_id' => $roomId,
            'type_room_bed' => 2,
            'type_room_price' => rand(1200000, 2800000),
            'type_room_condition' => $condition2,
            'type_room_price_sale' => $condition2 == 1 ? rand(5, 25) : 0,
            'type_room_status' => 1,
            'type_room_quantity' => rand(5, 20),
        ]);

        // Type room 3: 1 giường đơn hoặc 2 giường đơn
        $condition3 = rand(0, 1);
        TypeRoom::create([
            'room_id' => $roomId,
            'type_room_bed' => 3,
            'type_room_price' => rand(1500000, 3500000),
            'type_room_condition' => $condition3,
            'type_room_price_sale' => $condition3 == 1 ? rand(10, 30) : 0,
            'type_room_status' => 1,
            'type_room_quantity' => rand(3, 15),
        ]);
    }

    private function generateAveragePrice($rank)
    {
        return match ($rank) {
            5 => rand(3500000, 8500000),
            4 => rand(1800000, 4500000),
            3 => rand(700000, 2200000),
            default => rand(1000000, 3000000),
        };
    }

    private function getAreaNameById($areaId)
    {
        $areas = [
            2 => 'Hội An', 3 => 'Cẩm Lệ', 4 => 'Hải Châu',
            5 => 'Hòa Vang', 6 => 'Liên Chiểu', 7 => 'Ngũ Hành Sơn',
            8 => 'Sơn Trà', 9 => 'Thanh Khê'
        ];
        return $areas[$areaId] ?? 'Đà Nẵng';
    }

    private function generateTags($type, $areaId)
    {
        $typeNames = [1 => 'khách sạn', 2 => 'căn hộ dịch vụ', 3 => 'resort'];
        $areaName = strtolower($this->getAreaNameById($areaId));
        $keywords = $typeNames[$type] . ', ' . $areaName . ', nghỉ dưỡng, du lịch';
        
        if ($areaId == 8) $keywords .= ', biển mỹ khê';
        if ($areaId == 7) $keywords .= ', non nước, ngũ hành sơn';
        if ($areaId == 4) $keywords .= ', sông hàn, trung tâm';
        if ($areaId == 2) $keywords .= ', phố cổ, cửa đại';
        
        return $keywords;
    }

    private function getRandomView()
    {
        $views = ['Ocean View', 'City View', 'Garden View', 'Mountain View', 
                 'River View', 'Sea View', 'Pool View', 'Bay View'];
        return $views[rand(0, count($views)-1)];
    }

    /**
     * Tạo hotel_manager cho khách sạn
     * @throws \Exception nếu không thể tạo manager
     */
    private function createHotelManager($hotel, $hotelManagerRole)
    {
        // Chuyển tên khách sạn thành slug cho email
        $hotelSlug = $this->convertHotelNameToSlug($hotel->hotel_name, $hotel->hotel_id);
        $baseEmail = 'nguyennvv@' . $hotelSlug . '.com';
        $managerEmail = $baseEmail;
        
        // Kiểm tra và xử lý email trùng lặp
        $counter = 1;
        while (Admin::where('admin_email', $managerEmail)->exists()) {
            $managerEmail = 'nguyennvv@' . $hotelSlug . $hotel->hotel_id . '.com';
            $counter++;
            if ($counter > 10) {
                // Nếu vẫn trùng sau 10 lần thử, dùng hotel_id
                $managerEmail = 'nguyennvv@hotel' . $hotel->hotel_id . '.com';
                break;
            }
        }

        // Tạo Admin (Hotel Manager)
        // Rút ngắn tên nếu quá dài (giới hạn 30 ký tự để an toàn với tiếng Việt)
        $adminName = 'QL ' . $hotel->hotel_name;
        $maxLength = 30;
        if (mb_strlen($adminName, 'UTF-8') > $maxLength) {
            // Nếu tên quá dài, dùng hotel_id
            $adminName = 'QL Hotel #' . $hotel->hotel_id;
        }
        
        $admin = Admin::create([
            'admin_name' => $adminName,
            'admin_email' => $managerEmail,
            'admin_phone' => '0' . rand(900000000, 999999999), // Số điện thoại ngẫu nhiên
            'hotel_id' => $hotel->hotel_id,
        ]);

        // Gán role hotel_manager
        $admin->roles()->attach($hotelManagerRole->roles_id);
    }

    /**
     * Chuyển tên khách sạn thành slug cho email
     * Ví dụ: "A La Carte Đà Nẵng Beach" -> "alacartedanangbeach"
     */
    private function convertHotelNameToSlug($hotelName, $hotelId = null)
    {
        // Chuyển sang chữ thường
        $slug = mb_strtolower($hotelName, 'UTF-8');
        
        // Bảng chuyển đổi tiếng Việt có dấu sang không dấu
        $vietnamese = [
            'à' => 'a', 'á' => 'a', 'ạ' => 'a', 'ả' => 'a', 'ã' => 'a',
            'â' => 'a', 'ầ' => 'a', 'ấ' => 'a', 'ậ' => 'a', 'ẩ' => 'a', 'ẫ' => 'a',
            'ă' => 'a', 'ằ' => 'a', 'ắ' => 'a', 'ặ' => 'a', 'ẳ' => 'a', 'ẵ' => 'a',
            'è' => 'e', 'é' => 'e', 'ẹ' => 'e', 'ẻ' => 'e', 'ẽ' => 'e',
            'ê' => 'e', 'ề' => 'e', 'ế' => 'e', 'ệ' => 'e', 'ể' => 'e', 'ễ' => 'e',
            'ì' => 'i', 'í' => 'i', 'ị' => 'i', 'ỉ' => 'i', 'ĩ' => 'i',
            'ò' => 'o', 'ó' => 'o', 'ọ' => 'o', 'ỏ' => 'o', 'õ' => 'o',
            'ô' => 'o', 'ồ' => 'o', 'ố' => 'o', 'ộ' => 'o', 'ổ' => 'o', 'ỗ' => 'o',
            'ơ' => 'o', 'ờ' => 'o', 'ớ' => 'o', 'ợ' => 'o', 'ở' => 'o', 'ỡ' => 'o',
            'ù' => 'u', 'ú' => 'u', 'ụ' => 'u', 'ủ' => 'u', 'ũ' => 'u',
            'ư' => 'u', 'ừ' => 'u', 'ứ' => 'u', 'ự' => 'u', 'ử' => 'u', 'ữ' => 'u',
            'ỳ' => 'y', 'ý' => 'y', 'ỵ' => 'y', 'ỷ' => 'y', 'ỹ' => 'y',
            'đ' => 'd',
        ];
        
        $slug = strtr($slug, $vietnamese);
        
        // Loại bỏ các ký tự đặc biệt, chỉ giữ chữ cái và số
        $slug = preg_replace('/[^a-z0-9]/', '', $slug);
        
        // Nếu slug rỗng, dùng tên mặc định
        if (empty($slug)) {
            $slug = $hotelId ? 'hotel' . $hotelId : 'hotel' . time();
        }
        
        return $slug;
    }
}