<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Hotel;
use App\Models\Room;
use App\Models\FacilitiesHotel;
use App\Models\FacilitiesRoom;
use Illuminate\Support\Facades\DB;

class FacilitiesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Seed facilities for hotels
        $this->seedHotelFacilities();
        
        // Seed facilities for rooms
        $this->seedRoomFacilities();
        
        echo "Facilities seeded successfully!\n";
    }

    /**
     * Seed random facilities for hotels
     */
    private function seedHotelFacilities()
    {
        $hotels = Hotel::all();
        $facilities = FacilitiesHotel::where('facilitieshotel_status', 1)->get();
        
        if ($facilities->isEmpty()) {
            echo "No hotel facilities found. Please create facilities first.\n";
            return;
        }
        
        $facilityIds = $facilities->pluck('facilitieshotel_id')->toArray();
        
        foreach ($hotels as $hotel) {
            // Random số lượng facilities từ 3 đến 8 (hoặc tối đa số facilities có)
            $minFacilities = min(8, count($facilityIds));
            $maxFacilities = min(16, count($facilityIds));
            $numFacilities = rand($minFacilities, $maxFacilities);
            
            // Random chọn facilities
            $selectedFacilities = [];
            if ($numFacilities > 0) {
                $randomKeys = array_rand($facilityIds, $numFacilities);
                // array_rand trả về int nếu chỉ chọn 1, array nếu chọn nhiều
                if (!is_array($randomKeys)) {
                    $randomKeys = [$randomKeys];
                }
                $selectedFacilities = array_values(
                    array_intersect_key(
                        $facilityIds,
                        array_flip($randomKeys)
                    )
                );
            }
            
            // Cập nhật facilities dưới dạng JSON
            DB::table('tbl_hotel')
                ->where('hotel_id', $hotel->hotel_id)
                ->update([
                    'facilities' => json_encode($selectedFacilities)
                ]);
        }
        
        echo "Seeded facilities for " . $hotels->count() . " hotels.\n";
    }

    /**
     * Seed random facilities for rooms
     */
    private function seedRoomFacilities()
    {
        $rooms = Room::all();
        $facilities = FacilitiesRoom::where('facilitiesroom_status', 1)->get();
        
        if ($facilities->isEmpty()) {
            echo "No room facilities found. Please create facilities first.\n";
            return;
        }
        
        $facilityIds = $facilities->pluck('facilitiesroom_id')->toArray();
        
        foreach ($rooms as $room) {
            // Random số lượng facilities từ 2 đến 6 (hoặc tối đa số facilities có)
            $minFacilities = min(2, count($facilityIds));
            $maxFacilities = min(6, count($facilityIds));
            $numFacilities = rand($minFacilities, $maxFacilities);
            
            // Random chọn facilities
            $selectedFacilities = [];
            if ($numFacilities > 0) {
                $randomKeys = array_rand($facilityIds, $numFacilities);
                // array_rand trả về int nếu chỉ chọn 1, array nếu chọn nhiều
                if (!is_array($randomKeys)) {
                    $randomKeys = [$randomKeys];
                }
                $selectedFacilities = array_values(
                    array_intersect_key(
                        $facilityIds,
                        array_flip($randomKeys)
                    )
                );
            }
            
            // Cập nhật facilities dưới dạng JSON
            DB::table('tbl_room')
                ->where('room_id', $room->room_id)
                ->update([
                    'facilities' => json_encode($selectedFacilities)
                ]);
        }
        
        echo "Seeded facilities for " . $rooms->count() . " rooms.\n";
    }
}

