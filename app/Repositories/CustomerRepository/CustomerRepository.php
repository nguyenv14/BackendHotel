<?php
namespace App\Repositories\CustomerRepository;

use App\Models\Social;
use App\Repositories\BaseRepository;
use Mail;
use Auth;

class CustomerRepository extends BaseRepository implements CustomerRepositoryInterface
{
    //lấy model tương ứng
    public function getModel()
    {
        return \App\Models\Customers::class;
    }
    public function getAllByPaginate($value)
    {
        return $this->model->paginate($value);
    }
    public function searchNameOrEmail($key)
    {
        if($key){
            $result = $this->model::where('customer_name', 'like', '%' . $key . '%')->orwhere('customer_email', 'like', '%' . $key . '%')->get();
            return $result;
        }else{
            $result = $this->model->get();
            return $result;
        }
       
    }
    public function update_status($data)
    {
        $this->update($data['customer_id'], $data);
        return true;
    }
    public function delete_customer($id)
    {
        $result = $this->delete($id);
        $social = Social::where('user', $id)->first();
        if ($social) {
            $social->delete();
        }
        return $result;
    }
    public function binByPaginate($value)
    {
        $result = $this->model->onlyTrashed()->orderby('created_at', 'desc')->paginate($value);
        return $result;
    }
    public function selected_email($data)
    {

        $customer = $this->find($data['customer_id']);
        $list_mail_customer = session()->get('list_mail_customer');
        if ($data['id_checked'] == $data['customer_id']) { /* Trường Hợp Người Dùng Chọn Check Box */
            if ($list_mail_customer) { /* Khi List Mail Đã Tồn Tại */
                array_push($list_mail_customer, $customer->customer_email); /* Đưa Giá Trị Vào Mảng */
                $list_mail_customer = array_unique($list_mail_customer); /* Loại Bỏ Giá Trị Trùng Lặp */
                session()->put('list_mail_customer', $list_mail_customer);
                return "selected";
            } else { /* Khi List Mail Chưa Tồn Tại */
                $list_mail_customer = array();
                array_push($list_mail_customer, $customer->customer_email);
                session()->put('list_mail_customer', $list_mail_customer);
                return "selected";
            }
        } else if ($data['id_checked'] != $data['customer_id']) { /* Trường Hợp Người Dùng Hủy Chọn Check Box */
            foreach ($list_mail_customer as $key => $value) {
                if ($customer->customer_email == $value) {
                    unset($list_mail_customer[$key]);
                    session()->put('list_mail_customer', $list_mail_customer);
                    return "unselected";
                }
            }
        }
    }

    public function send_mail($to_name, $to_email, $title_email, $content_email)
    {
        $list_email_customer = preg_replace('/\s+/', '', $to_email); /* Xóa Toàn Bộ Khoảng Trắng Có Trong Chuỗi */
        $list_email_customer = explode(",", $list_email_customer); /* Tách Chuỗi Dựa Vào Dấu Chấm Thành Mảng */

        $data = array(
            "title_email" => $title_email,
            "content_email" => $content_email,
        );

        foreach ($list_email_customer as $to_email_customer) {
            if ($this->validate_email($to_email_customer)) {
                Mail::send('admin.Customer.emaillayout', $data, function ($message) use ($to_name, $to_email_customer, $title_email) {
                    $message->to($to_email_customer)->subject($title_email);
                    $message->from($to_email_customer, $to_name);
                });
            }
        }
        session()->forget('list_mail_customer');
        return true;
    }

    /* Hàm Kiểm Tra Xem Đối Tượng Đó Có Phải Là Email Hay Không*/
    public function validate_email($email)
    {
        return (preg_match("/(@.*@)|(\.\.)|(@\.)|(\.@)|(^\.)/", $email) || !preg_match("/^.+\@(\[?)[a-zA-Z0-9\-\.]+\.([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/", $email)) ? false : true;
    }
    public function output_customer($customers)
    {
        $output = '';
        foreach ($customers as $key => $customer) {
            $output .= '
            <tr>
            <td>' . $customer->customer_id . '</td>
            <td>' . $customer->customer_name . '</td>
            <td>' . $customer->customer_phone . '</td>
            <td>' . $customer->customer_email . '</td>
            ';
            if(Auth::user()->hasAnyRoles(['admin','manager'])) {
                $output .= '<td><div style="width: 80px; text-overflow:ellipsis;overflow: hidden">'.$customer->customer_password.'</div></td>';
            }
            $output .= '
            <td>
            ';
            if ($customer->customer_password != "") {
                $output .= 'Hệ Thống';

            } else {
                $output .= $customer->social->provider;
            }
            $output .= '
            </td> ';
            if (Auth::user()->hasAnyRoles(['admin','manager'])) {
                $output .= '
                <td>
                ';
                if ($customer->customer_status == 1) {
                    $output .= '
                    <span class = "update-status" data-customer_id = "'.$customer->customer_id.'" data-customer_status = "0">
                    <i style="color: rgb(52, 211, 52); font-size: 30px"
                        class="mdi mdi-toggle-switch"></i>
                    </span>
                    ';
                } else {
                    $output .= '
                    <span class = "update-status" data-customer_id = "'.$customer->customer_id.'" data-customer_status = "1" >
                    <i style="color: rgb(196, 203, 196);font-size: 30px"
                        class="mdi mdi-toggle-switch-off"></i>
                </span>
                    ';
                }
                $output .= '
            </td>
            <td>' . $customer->customer_ip . '</td>
           
            <td><div style="width: 80px; text-overflow:ellipsis;overflow: hidden">' . $customer->customer_device . '</div></td>
            <td>
                <button type="button" class="btn-sm btn-gradient-danger btn-icon-text btn-delete-customer" data-customer_id = "'. $customer->customer_id.'">
                <i class="mdi mdi-delete-forever btn-icon-prepend"></i> Xóa </button>
            </td>';
            }
            $output .= '
        </tr>
        ';
        }
        return $output;
    }
    public function output_sort_type($customers,$type){
        $output = '';
        foreach ($customers as $key => $customer) {
            if( $customer->social != null && $customer->social->provider == $type ){
                $output .= '
                <tr>
                <td>' . $customer->customer_id . '</td>
                <td>' . $customer->customer_name . '</td>
                <td>' . $customer->customer_phone . '</td>
                <td>' . $customer->customer_email . '</td>
                ';
                if(Auth::user()->hasAnyRoles(['admin','manager'])) {
                    $output .= '<td><div style="width: 80px; text-overflow:ellipsis;overflow: hidden">'.$customer->customer_password.'</div></td>';
                }
                $output .= '
                <td>
                ';
                if ($customer->customer_password != "") {
                    $output .= 'Hệ Thống';
    
                } else {
                    $output .= $customer->social->provider;
    
                }
                $output .= '
                </td> ';
                if (Auth::user()->hasAnyRoles(['admin','manager'])) {
                    $output .= '
                    <td>
                    ';
                    if ($customer->customer_status == 1) {
                        $output .= '
                        <span class = "update-status" data-customer_id = "'.$customer->customer_id.'" data-customer_status = "0">
                        <i style="color: rgb(52, 211, 52); font-size: 30px"
                        class="mdi mdi-toggle-switch"></i>
                        </span>
                        ';
                    } else {
                        $output .= '
                        <span class = "update-status" data-customer_id = "'.$customer->customer_id.'" data-customer_status = "1" >
                        <i style="color: rgb(196, 203, 196);font-size: 30px"
                            class="mdi mdi-toggle-switch-off"></i>
                        </span>
                        ';
                    }
                    $output .= '
                </td>
                <td>' . $customer->customer_ip . '</td>
              
                <td><div style="width: 80px; text-overflow:ellipsis;overflow: hidden">' . $customer->customer_device . '</div></td>
                <td>
                    <button type="button" class="btn-sm btn-gradient-danger btn-icon-text btn-delete-customer" data-customer_id = "'. $customer->customer_id.'">
                    <i class="mdi mdi-delete-forever btn-icon-prepend"></i> Xóa </button>
                </td>';
                }
                $output .= '
            </tr>
            ';
            }
        }

        return $output;
    }

    public function output_customer_system($customers){
        $output = '';
            foreach ($customers as $key => $customer) {
                if( $customer->social == null ){
                    $output .= '
                    <tr>
                    <td>' . $customer->customer_id . '</td>
                    <td>' . $customer->customer_name . '</td>
                    <td>' . $customer->customer_phone . '</td>
                    <td>' . $customer->customer_email . '</td>
                    ';
                    if(Auth::user()->hasAnyRoles(['admin','manager'])) {
                        $output .= '<td><div style="width: 80px; text-overflow:ellipsis;overflow: hidden">'.$customer->customer_password.'</div></td>';
                    }
                    $output .= '
                    <td>
                    ';
                    if ($customer->customer_password != "") {
                        $output .= 'Hệ Thống';
        
                    } else {
                        $output .= $customer->social->provider;
                    }
                    $output .= '
                    </td> ';
                    if (Auth::user()->hasAnyRoles(['admin','manager'])) {
                        $output .= '
                        <td>
                        ';
                        if ($customer->customer_status == 1) {
                            $output .= '
                            <span class = "update-status" data-customer_id = "'.$customer->customer_id.'" data-customer_status = "0">
                            <i style="color: rgb(52, 211, 52); font-size: 30px"
                            class="mdi mdi-toggle-switch"></i>
                            </span>
                            ';
                        } else {
                            $output .= '
                            <span class = "update-status" data-customer_id = "'.$customer->customer_id.'" data-customer_status = "1" >
                            <i style="color: rgb(196, 203, 196);font-size: 30px"
                            class="mdi mdi-toggle-switch-off"></i>
                            </span>'
                        ;
                        }
                        $output .= '
                    </td>
                    <td>' . $customer->customer_ip . '</td>
                    
                    <td><div style="width: 80px; text-overflow:ellipsis;overflow: hidden">' . $customer->customer_device . '</div></td>
                    <td>
                        <button type="button" class="btn-sm btn-gradient-danger btn-icon-text btn-delete-customer" data-customer_id = "'. $customer->customer_id.'">
                        <i class="mdi mdi-delete-forever btn-icon-prepend"></i> Xóa </button>
                    </td>';
                    }
                    $output .= '
                </tr>
                ';
                }
            }

            return $output;
    }
    public function count_bin(){
        $result = $this->model->onlyTrashed()->count();
        return $result;
    }
    public function output_garbage_can($customers)
    {
        $output = '';
        foreach ($customers as $key => $customer) {
            $output .= '
            <tr>
            <td>' . $customer->customer_id . '</td>
            <td>' . $customer->customer_name . '</td>
            <td>' . $customer->customer_phone . '</td>
            <td>' . $customer->customer_email . '</td>
            ';
            if(Auth::user()->hasAnyRoles(['admin','manager'])) {
                $output .= '<td><div style="width: 80px; text-overflow:ellipsis;overflow: hidden">'.$customer->customer_password.'</div></td>';
            }
            $output .= '
            <td>
            ';
            if ($customer->customer_password != "") {
                $output .= 'Hệ Thống';

            } else {
                $output .= $customer->socialTrashed->provider;
            }
            $output .= '
            </td> ';
            if (Auth::user()->hasAnyRoles(['admin','manager'])) {
            $output .= ' 
            <td>'.$customer->deleted_at.'</td>
            <td>

                <button type="button" class="btn-sm btn-gradient-success btn-icon-text btn-restore-item" data-customer_id = "'. $customer->customer_id.'">
                <i class="mdi mdi-backup-restore btn-icon-prepend"></i> Khôi Phục </button>
                </br>
                <button type="button" class="btn-sm btn-gradient-danger btn-icon-text mt-3 btn-delete-item" data-customer_id = "'. $customer->customer_id.'">
                <i class="mdi mdi-delete-forever btn-icon-prepend"></i> Xóa </button>

            </td>';
            }
            $output .= '
        </tr>
        ';
        }
        return $output;
    }

    public function output_search_bin($key){
        if($key){
            $result = $this->model::onlyTrashed()->where('customer_name', 'like', '%' . $key . '%')->orwhere('customer_email', 'like', '%' . $key . '%')->get();
            $output =  $this->output_garbage_can($result);
            return $output;
        }else{
            $result =  $this->model->onlyTrashed()->get();
            $output = $this->output_garbage_can($result);
            return $output;
        }
       
    }

    public function restore_item($id){
        $result = $this->model->withTrashed()->find($id);
        $result->restore();
        $social = Social::find($id);
        if($social){
            $social->restore();
        }
        return true;
    }
    public function delete_item($id){
        $result = $this->model->withTrashed()->find($id);
        $result->forceDelete();
        $social = Social::find($id);
        if($social){
            $social->forceDelete();
        }
        return true;
    }
}
