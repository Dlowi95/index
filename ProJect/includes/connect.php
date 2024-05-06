<!-- Kết nối với database -->
<?php
if(!defined('_CODE')){
    die('Không thể truy cập ....');
}

try{
    if(class_exists('PDO')){
       $dsn = 'mysql:dbname='._DB.';host='._HOST;
       $option = [
        PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
       ];
       $conn = new PDO($dsn,_USER,_PASS, $option);
    //    if($conn){
    //     echo 'Connected';
    //    }
    }
}catch(Exception $e){
    echo '<div style ="color:red";padding: 5px 15px;border: 1px solid red;">';
    echo $e -> getMessage().'<br';
    echo '</div>';
    die();
}