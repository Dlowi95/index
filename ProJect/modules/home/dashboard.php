<?php
if(!defined('_CODE')){
    die('Không thể truy cập ....');
}
$data = [
    'pageTitle' => 'Trang dashboard'
];
layouts('header',$data);

// Kiểm tra trạng thái đăng nhập:
if(!isLogin()){
    redirect('?module=auth&action=login');
}

?>
<h1>DASHBOARD</h1>
<?php
layouts('footer');


?>