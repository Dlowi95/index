<!-- Kích hoạt tài khoản -->
<?php
if(!defined('_CODE')){
    die('Không thể truy cập ....');
}
layouts('header-login');

$token = filter()['token'];
if(!empty($token)){

    // Truy vẫn để kiểm tra token với database 
    $tokenQuery = oneRaw("SELECT id FROM users WHERE activeToken = '$token'");
    if(!empty($tokenQuery)){
        $userId = $tokenQuery['id'];
        $dataUpdate = [
            'status' => 1,
            'activeToken' => null
        ];
        $updateStatus = update('users',$dataUpdate,"id=$userId");
        if($updateStatus){
            // Update thành công
            setFlashData('msg',' Kích hoạt tài khoản thành công, bạn có thể đăng nhập ngay bây giờ.');
            setFlashData('msg_type','success');
        }else{
            setFlashData('msg','Không thể kích hoạt tài khoản, vui lòng thử lại sau.');
            setFlashData('msg_type','danger');
        }
        redirect('?module=auth&action=login');
    }else{
        getSmg('Liên kết không tồn tại hoặc đã hết hạn.!','danger');
    }
}else{
    getSmg('Liên kết không tồn tại hoặc đã hết hạn.!','danger');
}

?>

<h1>Active</h1>

<?php
layouts('footer-login');

?>