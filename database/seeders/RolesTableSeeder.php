<?php

namespace Database\Seeders;
use App\Models\Roles;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Roles::truncate(); /* Xóa Hết Cơ Sở Dữ Liệu Cũ */

        Roles::create(['roles_name' => 'admin']);
        Roles::create(['roles_name' => 'manager']);
        Roles::create(['roles_name' => 'employee']);
        Roles::create(['roles_name' => 'hotel_manager']);
    }
}
