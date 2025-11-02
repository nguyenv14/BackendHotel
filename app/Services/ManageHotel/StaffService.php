<?php
namespace App\Services\ManageHotel;

use App\Models\Admin;
use App\Models\Hotel;
use App\Models\Roles;
use Illuminate\Http\Request;

class StaffService
{
    public function goToManageStaff($requestData)
    {
        $hotel  = Hotel::query()->where('hotel_id', $requestData->hotel_id)->first();
        $admins = Admin::query()
            ->leftJoin('admin_roles', 'admin_roles.admin_admin_id', '=', 'tbl_admin.admin_id')
            ->leftJoin('tbl_roles', 'tbl_roles.roles_id', '=', 'admin_roles.roles_roles_id')
            ->where('tbl_roles.roles_name', 'hotel_staff')
            ->where('hotel_id', $requestData->hotel_id)->paginate(5);
        return view('admin.Hotel.ManagerHotel.Staff.all_staff')->with(compact('hotel', 'admins'));
    }

    public function goToAddStaff(Request $request)
    {
        $hotel = Hotel::query()->where('hotel_id', $request->hotel_id)->first();
        return view('admin.Hotel.ManagerHotel.Staff.add_staff')->with(compact('hotel'));
    }

    public function loadTableAdminByHotel($hotelId)
    {
        $admins = Admin::query()
            ->leftJoin('admin_roles', 'admin_roles.admin_admin_id', '=', 'tbl_admin.admin_id')
            ->leftJoin('tbl_roles', 'tbl_roles.roles_id', '=', 'admin_roles.roles_roles_id')
            ->where('tbl_roles.roles_name', 'hotel_staff')
            ->where('hotel_id', $hotelId)->get();
        return $this->output_admin($admins);
    }

    public function output_admin($admins)
    {
        // Kiểm tra nếu không có admin nào
        if ($admins->isEmpty()) {
            return '<tr><td colspan="5" class="text-center">Không có người dùng nào!</td></tr>';
        }
        $output = '';
        foreach ($admins as $key => $admin) {
            $output .= '
            <tr>
            <td>' . $admin->admin_id . '</td>
            <td>' . $admin->admin_name . '</td>
            <td>' . $admin->admin_phone . '</td>
            <td>' . $admin->admin_email . '</td>
            <td>
                <div style="margin-top: 10px">
                    <button type="button" class="btn-sm btn-gradient-dark btn-rounded btn-fw btn-delete-admin-roles" data-admin_id="' . $admin->admin_id . '">Xóa Quyền
                    </button>
                </div>
                <div style="margin-top: 10px">
                                        <a href="' . url('admin/hotel/manager/staff/edit-staff?admin_id=' . $admin->admin_id . '&hotel_id=' . $admin->hotel_id) . '"><button
                                                type="button" class="btn-sm btn-gradient-danger btn-dangee btn-fw">Chỉnh sửa</button>
                                        </a>
                </div>
            </td>
        </tr>
        ';
        }
        return $output;
    }

    public function saveStaff($data)
    {
        $admin                 = new Admin();
        $admin->admin_name     = $data['admin_name'];
        $admin->admin_email    = $data['admin_email'];
        $admin->admin_phone    = $data['admin_phone'];
        $admin->admin_password = md5($data['admin_password']);
        $admin->hotel_id       = $data['hotel_id'];
        $admin->save();

        $role = Roles::where('roles_name', 'hotel_staff')->first();
        if ($role) {
            $admin->roles()->attach($role->roles_id);
        }
    }

    public function goToEditStaff(Request $request)
    {
        $admin = Admin::query()->where('admin_id', $request->admin_id)->first();
        $hotel = Hotel::query()->where('hotel_id', $request->hotel_id)->first();
        return view('admin.Hotel.ManagerHotel.Staff.edit_staff')->with(compact('admin', 'hotel'));
    }

    public function updateStaff($data)
    {
        $admin              = Admin::query()->where('admin_id', $data['admin_id'])->first();
        $admin->admin_name  = $data['admin_name'];
        $admin->admin_email = $data['admin_email'];
        $admin->admin_phone = $data['admin_phone'];
        if (! empty($data['admin_password'])) {
            $admin->admin_password = md5($data['admin_password']);
        }
        $admin->save();
        return $admin;
    }

    public function search($requestData)
    {
        $user = auth()->user();
        if (empty($requestData->search)) {
            $admins = Admin::query()
                ->leftJoin('admin_roles', 'admin_roles.admin_admin_id', '=', 'tbl_admin.admin_id')
                ->leftJoin('tbl_roles', 'tbl_roles.roles_id', '=', 'admin_roles.roles_roles_id')
                ->where('tbl_roles.roles_name', 'hotel_staff')
                ->where('hotel_id', $user->hotel_id)
                ->paginate(5);

            return $this->output_admin($admins);
        }
        $admins = Admin::query()
            ->leftJoin('admin_roles', 'admin_roles.admin_admin_id', '=', 'tbl_admin.admin_id')
            ->leftJoin('tbl_roles', 'tbl_roles.roles_id', '=', 'admin_roles.roles_roles_id')
            ->where('tbl_roles.roles_name', 'hotel_staff')
            ->where('hotel_id', $user->hotel_id)
            ->where(function ($query) use ($requestData) {
                $query->where('tbl_admin.admin_name', 'like', '%' . $requestData->search . '%')
                    ->orWhere('tbl_admin.admin_email', 'like', '%' . $requestData->search . '%')
                    ->orWhere('tbl_admin.admin_phone', 'like', '%' . $requestData->search . '%');
            })
            ->paginate(5);

        return $this->output_admin($admins);
    }
}
