<?php
namespace App\Repositories\AdminRepository;

use App\Repositories\BaseRepository;

use Auth;
use App\Models\Roles;

class AdminRepository extends BaseRepository implements AdminRepositoryInterface
{
    //lấy model tương ứng
    public function getModel()
    {
        return \App\Models\Admin::class;
    }
    public function getAllByPaginate($value){
        return $this->model::with('roles')->paginate($value);
    }
    public function searchNameOrEmail($key){
        $result = $this->model::where('admin_name', 'like', '%'.$key.'%')->orwhere('admin_email', 'like', '%'.$key.'%')->get();
        return $result;
    }
    public function assign_roles($data){
        if (Auth::id() == $data['admin_id']) {
            return "permission_error";
        } else {
            $admin = $this->find($data['admin_id']);
            $admin->roles()->detach(); /* Nó Hủy Các Quyền Hiện Tại Ra */
            // $admin->roles()->attach(); /* Nó kết hợp admin với roles lại và lấy ra quyền */
            if ($data['index_roles']  == 1) {
                $admin->roles()->attach(Roles::where('roles_name', 'admin')->first());
                return "admin";
            } else if ($data['index_roles']  == 2) {
                $admin->roles()->attach(Roles::where('roles_name', 'manager')->first());
                return "manager";
            } else if ($data['index_roles']  == 3) {
                $admin->roles()->attach(Roles::where('roles_name', 'employee')->first());
                return "employee";
            } else if ($data['index_roles']  == 4) {
                $admin->roles()->attach(Roles::where('roles_name', 'hotel_manager')->first());
                return "hotel_manager";
            }
        }
    }

    public function delete_admin_roles($admin_id){
        if (Auth::id() == $admin_id) {
            return "error_delete_admin";
        }else{
            $admin = $this->find($admin_id);
            if ($admin) {
                $admin->roles()->detach(); /* Gỡ quyền */
                $admin->delete();
            }
            return "true";
        } 
    }

    public function update_admin($data){
        if($data['admin_password'] != $data['admin_password1']){
            return "error";
        }else{
            unset($data['_token']);
            unset($data['admin_password1']);
            $data['admin_password'] = md5($data['admin_password']);
            $this->update($data['admin_id'],$data);
            return "true";
        }
    }
    public function output_admin($admins){
        $output = '';
        foreach ($admins as $key => $admin){
            $output .= '
            <tr>
            <td>'.$admin->admin_id.'</td>
            <td>'.$admin->admin_name .'</td>
            <td>'.$admin->admin_phone.'</td>
            <td>'.$admin->admin_email.'</td>
            <td><div style="width: 120px; text-overflow:ellipsis;overflow: hidden">'.$admin->admin_password.'</div></td>
            <td>
               
                   
                        <input type="radio" class="form-check-input"
                            name="roles'.$admin->admin_id.'" ';
                            if($admin->hasRoles('admin')){
                                $output .= 'checked';
                            }else{
                                $output .= ' ';
                            }
                            $output .= '
                            value="1"
                            data-admin_id="'.$admin->admin_id.'">
                   
              
            </td>
            <td>
             
                        <input type="radio" class="form-check-input"
                            name="roles'.$admin->admin_id.'" ';
                            if($admin->hasRoles('manager')){
                                $output .= 'checked';
                            }else{
                                $output .= ' ';
                            }
                            $output .= '
                             value="2"
                            data-admin_id="'.$admin->admin_id.'">
                
            </td>
            <td>
              
                        <input type="radio" class="form-check-input"
                            name="roles'.$admin->admin_id.'" ';
                            if($admin->hasRoles('employee')){
                                $output .= 'checked';
                            }else{
                                $output .= ' ';
                            }
                            $output .= '
                            value="3"
                            data-admin_id="'.$admin->admin_id.'">
                  
            </td>

            <td>
                <div style="margin-top: 10px">
                    <button type="button" class="btn-sm btn-gradient-dark btn-rounded btn-fw btn-delete-admin-roles" data-admin_id="'.$admin->admin_id .'">Xóa Quyền 
                    </button>
                </div>
                <div style="margin-top: 10px">
                    <a href="'.url('admin/auth/impersonate?admin_id=' . $admin->admin_id).'"><button
                            type="button" class="btn-sm btn-gradient-info btn-rounded btn-fw">Chuyển
                            Quyền</button>
                    </a>
                </div>
                <div style="margin-top: 10px">
                                        <a href="'. url('admin/auth/edit-admin?admin_id=' . $admin->admin_id).'"><button
                                                type="button" class="btn-sm btn-gradient-danger btn-dangee btn-fw">Chỉnh sửa</button>
                                        </a>
                </div>
            </td>
        </tr>
        ';
        }
        return $output;
    }
}