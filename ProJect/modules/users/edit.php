<!-- Đăng ký tài khoản -->
<?php
if(!defined('_CODE')){
    die('Không thể truy cập ....');
}
$data = [
    'pageTitle' => 'Edit người dùng'
];
layouts('header-login',$data);



$filterAll = filter();

if(!empty($filterAll['id'])){
    $userId = $filterAll['id'];

    // Kiểm tra xem userId có tồn tại trong database hay không?
    // Nếu tồn tại => lấy thông tin người dùng
    // Nếu không tồn tại => Chuyển hướng đến trang list

    $userDetail = oneRaw("SELECT * FROM users WHERE id = '$userId'");
    if(!empty($userDetail)){
        // tồn tại
        setFlashData('user-detail', $userDetail);
    }else{
        redirect('?module=users&action=list');
    }
}

if(isPost()){
    $filterAll = filter();
    $errors = [];

    // Validate fullname : bắt buộc phải nhập, phải nhập nhiều hơn 5 ký tự
    if(empty($filterAll['fullname'])){
        $errors['fullname']['required'] = 'Họ tên bắt buộc nhập !';
    }else{
        if(strlen($filterAll['fullname']) < 5){
            $errors['fullname']['min'] = 'Họ tên tối thiểu 5 ký tự !';
        }
    }

    // Validate email : bắt buộc nhập, đúng định dạng email, kiểm tra email đã tồn tại trong csdl chưa
    if(empty($filterAll['email'])){
        $errors['email']['required'] = 'Email bắt buộc nhập !';
    }else{
        $email = $filterAll['email'];
        $sql = "SELECT id FROM users WHERE email = '$email' AND id <> $userId";
        if(getRows($sql) > 0 ){
        $errors['email']['unique'] = 'Email đã tồn tại !';

        }
    }
    
    // Validate số điện thoại: bắt buộc nhập, số có đúng định dạng không
    if(empty($filterAll['phone'])){
        $errors['phone']['required'] = 'Số điện thoại bắt buộc nhập !';
    }else{
        if(!isPhone($filterAll['phone'])){
            $errors['phone']['isPhone'] = 'Số điện thoại không hợp lệ !';
        }
    }

    if(!empty($filterAll['password'])){
        // Validate password: bắt buộc phải nhập, pass phải >= 8 ký tự
        if(empty($filterAll['password'])){
            $errors['password']['required'] = ' Bắt buộc nhập mật khẩu !';
        }else{
            if(strlen($filterAll['password']) <= 8){
                $errors['password']['min'] = 'Mật khẩu phải nhập nhiều hơn 8 ký tự';
            }
        }
        // Validate password_confirm: bắt buộc phải nhập, phải nhập i chang password
        if(empty($filterAll['password_confirm'])){
            $errors['password_confirm']['required'] = ' Bạn phải nhập lại mật khẩu';
        }else{
            if(strlen($filterAll['password']) <= 8){
                $errors['password_confirm']['match'] = 'Mật khẩu bạn nhập lại không đúng';
            }
        }
    }

    if(empty($errors)){
        $dataUpdate = [
            'fullname' => $filterAll['fullname'],
            'email' => $filterAll['email'],
            'phone' => $filterAll['phone'],
            // 'password' => password_hash($filterAll['password'],PASSWORD_DEFAULT),
            'status' => $filterAll['status'],
            'creat_at' => date('Y-m-d H:i:s'),
        ];

        if(!empty($filterAll['password'])){
            $dataUpdate['password'] = password_hash($filterAll['password'],PASSWORD_DEFAULT);
        }

        $condition = "id = $userId";
        $UpdateStatus = update('users',$dataUpdate,$condition);
        if($UpdateStatus){
            // Thông báo thành công:
            setFlashData('smg','Sửa người dùng thành công!');
            setFlashData('smg_type','success');
        }else{
            setFlashData('smg','Hệ thống đang xử lí vui lòng thử lại sau');
            setFlashData('smg_type','danger');
        }

        }else{
        setFlashData('smg','Vui lòng kiểm tra lại dữ liệu!!!');
        setFlashData('smg_type','danger');
        setFlashData('errors',$errors);
        setFlashData('old',$filterAll);
    }

    redirect('?module=users&action=edit&id='.$userId);

}
$smg = getFlashData('smg');
$smg_type = getFlashData('smg_type');
$errors = getFlashData('errors');
$old = getFlashData('old');
$userDetailll = getFlashData('user-detail');
if(!empty($userDetailll)){
    $old = $userDetailll;
}

?>

<div class="container ">
    <div class="row " style="margin: 50px auto;">
        <legend class="text-center text-uppercase fw-bold">Sửa người dùng</legend>
        <?php
    if(!empty($smg)){
        getSmg($smg,$smg_type);
    }
    ?>
        <form action="" method="post">
            <div class="row">
                <div class="col">
                    <div class="form-group mg-form">
                        <label for="">Họ tên</label>
                        <input name="fullname" type="fullname" class="form-control" placeholder="Họ Tên" value="<?php
                echo old('fullname',$old);
                ?>">
                        <?php 
                echo form_error('fullname','<span class="error">','</span>',$errors);
                ?>
                    </div>
                    <div class="form-group mg-form">
                        <label for="">Email</label>
                        <input name="email" type="email" class="form-control" placeholder="Email" value="<?php
                echo old('email',$old);
                ?>">
                        <?php 
                echo form_error('email','<span class="error">','</span>',$errors);
                ?>
                    </div>
                    <div class="form-group mg-form">
                        <label for="">Số điện thoại</label>
                        <input name="phone" type="number" class="form-control" placeholder="Số điện thoại" value="<?php
                echo old('phone',$old);
                ?>">
                        <?php 
                echo form_error('phone','<span class="error">','</span>',$errors);
                ?>
                    </div>
                </div>
                <div class="col">
                    <div class="form-group mg-form">
                        <label for="">Mật khẩu</label>
                        <input name="password" type="password" class="form-control" placeholder="Mật Khẩu (Không nhập nếu không thay đổi)">
                        <?php 
                echo form_error('password','<span class="error">','</span>',$errors);
                ?>
                    </div>
                    <div class="form-group mg-form">
                        <label for="">Nhập lại mật khẩu</label>
                        <input name="password_confirm" type="password" class="form-control"
                            placeholder="Nhập Lại Mật Khẩu (Không nhập nếu không thay đổi)">
                        <?php 
                echo form_error('password_confirm','<span class="error">','</span>',$errors);
                ?>
                    </div>

                    <div class="form-group">
                        <label for="">Trạng thái</label>
                        <select name="status" id="" class="form-control">
                            <option value="0" <?php echo (old('status',$old)==0) ? 'selected' : false;?>>Chưa kích hoạt</option>
                            <option value="1" <?php echo (old('status',$old)==1) ? 'selected' : false;?>>Đã kích hoạt</option>
                        </select>
                    </div>
                </div>
            </div>

            <input type="hidden" name="id" value="<?php echo $userId?>">

            <button type="submit" class="btn btn-primary btn-block mg-form mg-btn-add">Update người dùng</button>
            <a href="?module=users&action=list" class="btn btn-success btn-block mg-form mg-btn-ql">Quay lại</a>

            <hr>
        </form>
    </div>
</div>
<?php
layouts('footer-login');
?>