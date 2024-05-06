<!-- main -->
<?php
session_start();
require_once('config.php');
require_once('./includes/connect.php');
// Thư viện PHPMailer
require_once('./includes/phpmailer/Exception.php');
require_once('./includes/phpmailer/PHPMailer.php');
require_once('./includes/phpmailer/SMTP.php');

require_once('./includes/functions.php');
require_once('./includes/database.php');
require_once('./includes/session.php');

// setFlashData('dlw','Cài đặt thành công');
// echo getFlashData('dlw');

// sendMail('code11toi@gmail.com','Chao ban nha','Cho mình làm quen bạn dc hong');


$module = _MODULE;
$action = _ACTION;

//module
if(!empty($_GET['module'])){
    if(is_string($_GET['module'])){
        $module = trim($_GET['module']);
    }
}
//action
if(!empty($_GET['action'])){
    if(is_string($_GET['action'])){
        $action = trim($_GET['action']);
    }
}

$path = 'modules/'. $module. '/'. $action . '.php';

if(file_exists($path)){
    require_once($path);
}else{
    require_once('modules/error/404.php');
}