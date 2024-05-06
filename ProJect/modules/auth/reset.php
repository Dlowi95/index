<!-- Reset password -->
<?php
if(!defined('_CODE')){
    die('Không thể truy cập ....');
}
$data = [
    'pageTitle' => 'Reset password'
];
layouts('header-login', $data);
$token = filter()['token'];
if(!empty($token)){
    // Truy vấn database kiểm tra token:
    $tokenQuery = oneRaw("SELECT id,fullname,email FROM users WHERE forgotToken = '$token'");
    if(!empty($tokenQuery)){
        $userId = $tokenQuery['id'];
        if(isPost()){
        $filterAll = filter();
        $errors = []; // Mảng chứa các lỗi

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
            if(($filterAll['password']) != $filterAll['password_confirm']){
                $errors['password_confirm']['match'] = 'Mật khẩu bạn nhập lại không đúng';
       }
    }
        // Kiểm tra lỗi cho validate
            if(empty($errors)){
                // Xử lí việc update password
                $paswordHash =  password_hash($filterAll['password'],PASSWORD_DEFAULT);
                $dataUpdate = [
                    'password' => $paswordHash,
                    'forgotToken' => null,
                    'update_at' => date('Y-m-d H:i:s')
                ];
                $updateStatus = update('users',$dataUpdate,"id = '$userId'");
                if($updateStatus){
                    setFlashData('msg','Thay đổi mật khẩu thành công:3');
                    setFlashData('msg_type','success');
                    redirect('?module=auth&action=login');
                }else{
                    setFlashData('msg','Lỗi hệ thống vui lòng thử lại sau!!!');
                    setFlashData('msg_type','danger');
                }
            }else {
                setFlashData('msg','Vui lòng nhập dữ liệu!!!');
                setFlashData('msg_type','danger');
                setFlashData('errors',$errors);
                redirect('?module=auth&action=reset&token='.$token);
    }


}
$smg = getFlashData('msg');
$smg_type = getFlashData('msg_type');
$errors = getFlashData('errors');
?>
<!-- Form đặt lại mật khẩu -->
<div class="contanier">
   <form class="w-50 mx-auto py-5 shadow p-4" action="" method="post">
    <h3 class="text-dark text-center text-uppercase fw-bold">Đặt lại mật khẩu</h3><hr>
    <?php
    if(!empty($msg)){
        getSmg($msg,$msg_type);
    }?>
    <div class="mb-3 input-form ">
        <label for="exampleFormControlInput1" class="form-label">Password</label>
        <input name="password" type="password" class="form-control " id="exampleFormControlInput1" placeholder="Nhập mật khẩu">
        <?php
            echo form_error('password','<span class="error">','</span>',$errors);
        ?>
      </div>
      <div class="mb-3 input-form ">
        <label for="exampleFormControlInput1" class="form-label">Nhập Lại Password</label>
        <input name="password_confirm" type="password" class="form-control " id="exampleFormControlInput1" placeholder="Nhập lại mật khẩu">
        <?php
            echo form_error('password_confirm','<span class="error">','</span>',$errors);
        ?>
      </div>
      <input type="hidden" name="token" value="<?php echo $token;?>">
      <div class="mb-3 d-flex">
      <button type="submit" class="btn btn-primary btn-block mg-form">Gửi</button></div>
        <hr>
        <p class="text-center "><a href="?module=auth&action=login">Đăng nhập tài khoản</a></p>
      
   </form>
   </div>

<?php
    }else{
        getSmg('Liên kết không tồn tại hoặc đã hết hạn....','danger');
    }
}else{
    getSmg('Liên kết không tồn tại hoặc đã hết hạn....','danger');
}
?>
<?php
layouts('footer-login');
?>