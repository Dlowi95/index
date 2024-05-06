<!-- Đăng nhập tài khoản -->
<?php
if(!defined('_CODE')){
    die('Không thể truy cập ....');
}
$data = [
    'pageTitle' => 'Đăng nhập tài khoản'
];
layouts('header-login',$data);

// Kiểm tra trạng thái đăng nhập:
if(isLogin()){
    redirect('?module=home&action=dashboard');
}


if(isPost()){
    $fillterAll = filter();
    if(!empty(trim($fillterAll['email'])) && !empty(trim($fillterAll['password']))){
        // Kiểm tra đăng nhập:
        $email = $fillterAll['email'];
        $password = $fillterAll['password'];

        // Truy vẫn lấy thông tin users theo email:
        $userQuery = oneRaw("SELECT password, id FROM users WHERE email = '$email'");
        
        if(!empty($userQuery)){
            $passwordHash = $userQuery['password'];
            $userId = $userQuery['id'];
            if(password_verify($password,$passwordHash)){


                //Kiểm tra xem người dùng có đăng nhập chưa:
                $userLogin = getRows("SELECT * FROM tokenLogin WHERE user_Id = '$userId'");
                if($userLogin > 0){
                    setFlashData('msg','Không thể đăng nhập, tài khoản đang đăng nhập một nơi khác');
                    setFlashData('msg_type','danger');
                    redirect('?module=auth&action=login');
                }else{
                    
                    // Tạo token login:
                $tokenLogin = sha1(uniqid().time());

                // Insert vào bảng loginToken
                $dataInsert = [
                    'user_Id' => $userId,
                    'token' => $tokenLogin,
                    'creat_at' => date('Y-m-d H:i:s')
                ];
                $insertStatus = insert('tokenLogin',$dataInsert);
                if($insertStatus){
                    // Insert thành công

                    // Lưu tokenLogin vào session:
                    setSession('tokenLogin',$tokenLogin);

                    redirect('?module=home&action=dashboard');
                }else{
                    setFlashData('msg','Không thể đăng nhập, vui lòng thử lại sau.');
                    setFlashData('msg_type','danger');
                }
                }
            }else{
                setFlashData('msg','Mật khẩu không chính xác.');
                setFlashData('msg_type','danger');
            }
        }else{
            setFlashData('msg','Email không tồn tại!');
            setFlashData('msg_type','danger');
        }
    }else{
        setFlashData('msg','Vui lòng nhập email và mật khẩu!!');
        setFlashData('msg_type','danger');
    }
    redirect('?module=auth&action=login');

}

$msg = getFlashData('msg');
$msg_type = getFlashData('msg_type');
?>

<div class="contanier">
   <form class="w-50 mx-auto py-5 shadow p-4 form-container" action="" method="post">
    <h3 class="text-dark text-center text-uppercase fw-bold">Đăng nhập</h3><hr>
    <?php
    if(!empty($msg)){
        getSmg($msg,$msg_type);
    }?>
    <div class="mb-3 input-form ">
        <label for="exampleFormControlInput1" class="form-label">Email address</label>
        <input name="email" type="email" class="form-control " id="exampleFormControlInput1" placeholder="Nhập email">
      </div>
    <div class="mb-3 input-form ">
        <label for="exampleFormControlInput1" class="form-label">Password</label>
        <input name="password" type="password" class="form-control " id="exampleFormControlInput1" placeholder="Nhập mật khẩu">
      </div>
      <div class="mb-3 d-flex">
      <button type="submit" class="btn btn-primary btn-block mg-form">Đăng Nhập</button></div>
        <hr>
        <p class="text-center "><a href="?module=auth&action=forgot">Quên mật khẩu</a></p>
        <p class="text-center "><a href="?module=auth&action=register">Đăng ký tài khoản</a></p>
      
   </form>
   </div>
<?php
layouts('footer-login');
?>