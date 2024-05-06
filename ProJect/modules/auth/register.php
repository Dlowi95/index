<!-- Đăng ký tài khoản -->
<?php
if(!defined('_CODE')){
    die('Không thể truy cập ....');
}
$data = [
    'pageTitle' => 'Đăng ký tài khoản'
];
layouts('header-login',$data);

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
        $sql = "SELECT id FROM users WHERE email = '$email'";
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

    if(empty($errors)){
        $activeToken = sha1(uniqid().time());
        $dataInsert = [
            'fullname' => $filterAll['fullname'],
            'email' => $filterAll['email'],
            'phone' => $filterAll['phone'],
            'password' => password_hash($filterAll['password'],PASSWORD_DEFAULT),
            'activeToken' => $activeToken,
            'creat_at' => date('Y-m-d H:i:s'),
            // 'updated_at' => date('Y-m-d H:i:s')
        ];
        $insertStatus = insert('users',$dataInsert);
        if($insertStatus){
            

            // Tạo link kích hoạt:
            $linkActive = _WEB_HOST . '?module=auth&action=active&token='. $activeToken;

            
            // Thiết lập gửi mail:
            $subject = ' Kích hoạt tài khoản của bạn';
            $content = 'Chào '.$filterAll['fullname'].'</br>';
            $content .= '. Vui lòng click vào link dưới đây để kích hoạt tài khoản: </br>';
            $content .= $linkActive . '</br>';
            $content .= '. Trân trọng cảm ơn, nếu bạn không muốn kích hoạt tài khoản vui lòng bỏ qua email này.';

            // Tiến hành gửi mail:
            $sendMail = sendMail($filterAll['email'],$subject,$content);

            // Thông báo thành công:
            if($sendMail){
                setFlashData('smg','Đăng ký tài khoản thành công!!!, Vui lòng kiểm tra email để kích hoạt tài khoản <3');
                setFlashData('smg_type','success');
            }else{
                setFlashData('smg','Có lỗi xảy ra trong quá trình gửi mail, vui lòng thử lại sau');
                setFlashData('smg_type','danger');
            }
        }else{
            setFlashData('smg','Đăng ký không thành công!');
            setFlashData('smg_type','danger');

        }
        // Đăng ký thành công --->
        redirect('?module=auth&action=register');

        }else{
        setFlashData('smg','Vui lòng nhập lại dữ liệu!!!');
        setFlashData('smg_type','danger');
        setFlashData('errors',$errors);
        setFlashData('old',$filterAll);
        redirect('?module=auth&action=register');
    }
}
$smg = getFlashData('smg');
$smg_type = getFlashData('smg_type');
$errors = getFlashData('errors');
$old = getFlashData('old');
?>

<div class="contanier">
    <form class="w-50-dk mx-auto py-5 shadow p-4 form-container" action="" method="post">
        <h3 class="text-dark text-center text-uppercase fw-bold">Đăng Ký</h3>
        <hr>
        <?php
    if(!empty($smg)){
        getSmg($smg,$smg_type);
    }?>
        <div class="mb-3 input-form ">
            <label for="exampleFormControlInput1" class="form-label">Họ Tên</label>
            <input name="fullname" type="fullname" class="form-control " id="exampleFormControlInput1"
                placeholder="Nhập Họ Tên" value="<?php
               echo old('fullname',$old);?>">
            <?php
               echo form_error('fullname','<span class="error">','</span>',$errors);
               ?>
        </div>

        <div class="mb-3 input-form ">
            <label for="exampleFormControlInput1" class="form-label">Email</label>
            <input name="email" type="email" class="form-control " id="exampleFormControlInput1"
                placeholder="Nhập email" value="<?php
               echo old('email',$old);?>">
            <?php
               echo form_error('email','<span class="error">','</span>',$errors);
               ?>
        </div>

        <div class="mb-3 input-form ">
            <label for="exampleFormControlInput1" class="form-label">Số điện thoại</label>
            <input name="phone" type="number" class="form-control " id="exampleFormControlInput1"
                placeholder="Nhập số điện thoại" value="<?php
               echo old('phone',$old);?>">
            <?php
               echo form_error('phone','<span class="error">','</span>',$errors);
               ?>
        </div>
        <div class="mb-3 input-form ">
            <label for="exampleFormControlInput1" class="form-label">Password</label>
            <input name="password" type="password" class="form-control " id="exampleFormControlInput1"
                placeholder="Nhập mật khẩu">
            <?php
               echo form_error('password','<span class="error">','</span>',$errors);
            ?>
        </div>
        <div class="mb-3 input-form ">
            <label for="exampleFormControlInput1" class="form-label">Nhập Lại Password</label>
            <input name="password_confirm" type="password" class="form-control " id="exampleFormControlInput1"
                placeholder="Nhập lại mật khẩu">
            <?php
               echo form_error('password_confirm','<span class="error">','</span>',$errors);
            ?>
        </div>
        <div class="mb-3 d-flex">
            <button type="submit" class="btn btn-primary btn-block mg-form">Đăng Ký</button>
        </div>
        <!-- <hr> -->
        <p class="text-center "><a href="?module=auth&action=login">Đăng nhập tài khoản</a></p>

    </form>
</div>
<?php
layouts('footer-login');
?>