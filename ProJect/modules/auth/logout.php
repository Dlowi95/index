<!-- Đăng xuất tài khoản -->
<?php
if(!defined('_CODE')){
    die('Không thể truy cập ....');
}

if(isLogin()){
    $token = getSession('tokenLogin');
    delete('tokenLogin',"token='$token'");
    removeSession('tokenLogin');
    redirect('?module=auth&action=login');
}
?>