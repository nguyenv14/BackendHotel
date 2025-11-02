<?php
namespace App\Http\Controllers\ManageHotel;

use App\Http\Controllers\Controller;
use App\Services\ManageHotel\StaffService;
use Illuminate\Http\Request;

session_start();
class StaffController extends Controller
{
    protected StaffService $staffService;
    public function __construct(StaffService $staffService)
    {
        $this->staffService = $staffService;
    }
    public function index(Request $request)
    {
        return $this->staffService->goToManageStaff($request);
    }

    public function goToAddStaff()
    {
        return $this->staffService->goToAddStaff(request());
    }

    public function loadTableAdminByHotel(Request $request)
    {
        $hotelId = $request->hotel_id;
        return $this->staffService->loadTableAdminByHotel($hotelId);
    }

    public function saveStaff(Request $request)
    {
        $validated = $request->validate([
            'admin_name'                  => 'required|max:255',
            'admin_email'                 => 'required|email|unique:tbl_admin,admin_email',
            'admin_phone'                 => 'required',
            'admin_password'              => 'required|min:6',
            'admin_password_confirmation' => 'required|same:admin_password',
        ], [
            'admin_name.required'                  => 'Vui lòng nhập tên quản trị viên.',
            'admin_name.max'                       => 'Tên quản trị viên không được vượt quá 255 ký tự.',
            'admin_email.required'                 => 'Vui lòng nhập email.',
            'admin_email.email'                    => 'Email không hợp lệ.',
            'admin_email.unique'                   => 'Email này đã được sử dụng.',
            'admin_phone.required'                 => 'Vui lòng nhập số điện thoại.',
            'admin_password.required'              => 'Vui lòng nhập mật khẩu.',
            'admin_password.min'                   => 'Mật khẩu phải có ít nhất 6 ký tự.',
            'admin_password_confirmation.required' => 'Vui lòng xác nhận mật khẩu.',
            'admin_password_confirmation.same'     => 'Mật khẩu xác nhận không khớp.',
        ]);
        $data = $request->all();
        $this->staffService->saveStaff($data);
        return redirect()->route('manage_hotel.staff', ['hotel_id' => $data['hotel_id']])
            ->with('message', 'Thêm nhân viên thành công!');
    }

    public function goToEditStaff(Request $request)
    {
        return $this->staffService->goToEditStaff($request);
    }

    public function updateStaff(Request $request)
    {
        $request->validate([
            'admin_name'                  => 'required|max:255',
            'admin_email'                 => 'required|email|unique:tbl_admin,admin_email,' . $request->admin_id . ',admin_id',
            'admin_phone'                 => 'required',
            'admin_password'              => 'nullable|min:6',
            'admin_password_confirmation' => 'nullable|same:admin_password|required_if:admin_password,!=,\'\'',
        ], [
            'admin_name.required'                     => 'Vui lòng nhập tên quản trị viên.',
            'admin_name.max'                          => 'Tên quản trị viên không được vượt quá 255 ký tự.',
            'admin_email.required'                    => 'Vui lòng nhập email.',
            'admin_email.email'                       => 'Email không hợp lệ.',
            'admin_email.unique'                      => 'Email này đã được sử dụng.',
            'admin_phone.required'                    => 'Vui lòng nhập số điện thoại.',
            'admin_password.min'                      => 'Mật khẩu phải có ít nhất 6 ký tự.',
            'admin_password_confirmation.same'        => 'Mật khẩu xác nhận không khớp.',
            'admin_password_confirmation.required_if' => 'Vui lòng xác nhận mật khẩu.',
        ]);
        $data   = $request->all();
        $result = $this->staffService->updateStaff($data);
        return redirect()->route('manage_hotel.staff', ['hotel_id' => $result->hotel_id])
            ->with('message', [
                'type'    => 'success',
                'content' => 'Cập nhật nhân viên thành công!',
            ]);
    }

    public function searchStaff(Request $request)
    {
        return $this->staffService->search($request);
    }
}
