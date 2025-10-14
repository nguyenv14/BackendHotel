<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Roles;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Admin::truncate(); /* Xóa Hết Cơ Sở Dữ Liệu Cũ */
     
        $admin_roles = Roles::where('roles_name','admin')->first();
        $manager_roles = Roles::where('roles_name','manager')->first();
        $employee_roles = Roles::where('roles_name','employee')->first();

        $admin = Admin::create([
            'admin_name' => 'Nhân Admin',
            'admin_email' => 'nhanadmin@gmail.com',
            'admin_phone' => '0987654321',
            'admin_password' => md5('123456'),
        ]);

        $manager = Admin::create([
            'admin_name' => 'Nhân Manager',
            'admin_email' => 'nhanmanager@gmail.com',
            'admin_phone' => '0987654321',
            'admin_password' => md5('123456'),
        ]);

        $employee = Admin::create([
            'admin_name' => 'Nhân Employee',
            'admin_email' => 'nhanemployee@gmail.com',
            'admin_phone' => '0987654321',
            'admin_password' => md5('123456'),
        ]);

        $admin->roles()->attach($admin_roles);
        $manager->roles()->attach($manager_roles);
        $employee->roles()->attach($employee_roles);

    }
}
