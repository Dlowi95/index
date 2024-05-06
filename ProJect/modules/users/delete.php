<?php
if(!defined('_CODE')){
    die('Không thể truy cập ....');
}

// Kiểm tra id trong database -> tồn tại ->tiến hành Xóa
// Xóa dữ liệu trong bảng tokenLogin -> Xóa dữ liệu bảng users

$filterAll = filter();
if(!empty($filterAll['id'])){
    $userId = $filterAll['id'];
    $userDetail = getRows("SELECT * FROM users WHERE id = $userId");
    if($userDetail > 0){
        // Thực hiện xóa
        $deleteToken = delete('tokenLogin',"user_Id = $userId");
        if($deleteToken){
            // Xóa user
            $deletesUser = delete('users',"id = $userId");
            if($deletesUser){
                // Thông báo thành công:
                setFlashData('smg','Xóa người dùng thành công!');
                setFlashData('smg_type','success');
            }else{
                setFlashData('smg','Lỗi hệ thống vui lòng thử lại sau!!!');
                setFlashData('smg_type','danger');
            }
        }
    }else{
        setFlashData('smg','Người dùng không tồn tại trong hệ thống.');
        setFlashData('smg_type','danger');
    }
}else{
        setFlashData('smg','Liên kết không tồn tại.');
        setFlashData('smg_type','danger');
}
redirect('?module=users&action=list');