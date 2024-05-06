<!-- Quên mật khẩu -->
<!-- Đăng nhập tài khoản -->
<?php
if(!defined('_CODE')){
    die('Không thể truy cập ....');
}
$data = [
    'pageTitle' => 'Quên mật khẩu'
];
layouts('header-login',$data);

// Kiểm tra trạng thái đăng nhập:
if(isLogin()){
    redirect('?module=home&action=dashboard');
}


if(isPost()){
    $filterAll = filter();
    if(!empty($filterAll['email'])){
        $email = $filterAll['email'];

        // Truy vẫn lấy thông tin users theo email:
        $queryUser = oneRaw("SELECT id FROM users WHERE email = '$email'");
        if(!empty($queryUser)){
            $userId = $queryUser['id'];

            // Tạo forgot token:
            $forgotToken = sha1(uniqid().time());
            $dataUpdate = [
                'forgotToken' => $forgotToken
            ];
            $updateStatus = update('users',$dataUpdate,"id=$userId");

            if($updateStatus){
                // Tạo link reset update password cho người dùng:
                $linkReset = _WEB_HOST.'?module=auth&action=reset&token='.$forgotToken;
                
                // Gửi email cho người dùng:
                $subject = 'Yêu cầu khôi phục mật khẩu';
                $content = 'Xin chào '.$filterAll['fullname'].'</br>';
                $content .= '. Chúng tôi nhận được yêu cầu khôi phục mật khẩu từ bạn.
                Vui lòng click vào link dưới đây để thay đổi mật khẩu: </br>';
                $content .= $linkReset . '</br>';
                $content .= '. Trân trọng cảm ơn, nếu bạn không muốn thay đổi mật khẩu vui lòng bỏ qua email này.';
                // Tiến hành gửi mail:
                
                $sendEmail = sendMail($email, $subject, $content);
                if($sendEmail){
                    setFlashData('msg','Chúng tôi thấy yêu cầu thay đổi mật khẩu!!!, Vui lòng kiểm tra email để thay đổi mật khẩu <3');
                    setFlashData('msg_type','success');           
                }else{
                    setFlashData('msg','Có lỗi xảy ra trong quá trình gửi mail, vui lòng thử lại sau');
                    setFlashData('msg_type','danger');
                }

            }else{
                setFlashData('msg','Không thể thay đổi mật khẩu, vui lòng thử lại sau.');
                setFlashData('msg_type','danger');
            }
        }else{
            setFlashData('msg','Địa chỉ email không tồn tại!!!');
            setFlashData('msg_type','danger');
        }
    }else{
        setFlashData('msg','Vui lòng nhập email!!!');
        setFlashData('msg_type','danger');
    }
    redirect('?module=auth&action=forgot');
}

$msg = getFlashData('msg');
$msg_type = getFlashData('msg_type');
?>

<div class="contanier ">
   <form class="w-50 mx-auto py-5 shadow p-4 form-container" action="" method="post">
    <h3 class="text-dark text-center text-uppercase fw-bold">Quên mật khẩu</h3><hr>
    <?php
    if(!empty($msg)){
        getSmg($msg,$msg_type);
    }?>
    <div class="mb-3 input-form ">
        <label for="exampleFormControlInput1" class="form-label">Email address</label>
        <input name="email" type="email" class="form-control " id="exampleFormControlInput1" placeholder="Nhập email">
      </div>
      <div class="mb-3 d-flex">
      <button type="submit" class="btn btn-primary btn-block mg-form">Gửi</button></div>
        <hr>
        <p class="text-center "><a href="?module=auth&action=login">Đăng nhập</a></p>
        <p class="text-center "><a href="?module=auth&action=register">Đăng ký tài khoản</a></p>
      
   </form>
   </div>
<?php
layouts('footer-login');
?>