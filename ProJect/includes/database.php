<!-- Các hàm xử lý liên quan đến CSDL -->
<?php
if(!defined('_CODE')){
    die('Không thể truy cập ....');
}

function query($sql, $data = [], $check = false){
    global $conn;
    $ketqua = false;
    try{
        $statement = $conn->prepare($sql);
        if(!empty($data)){
           $ketqua = $statement->execute($data);
        }else{
           $ketqua = $statement->execute();
        }
    }catch(Exception $e){
        echo '<div style ="color:red; padding: 5px 15px; border: 1px solid red;">';
        echo $e->getMessage().'<br>';
        echo 'FILE: '.$e->getFile().'<br>';
        echo 'LINE: '.$e->getLine().'<br>';
        echo '</div>';
        die();
    }
    if($check){
        return $statement; // Trả về đối tượng PDOStatement
    }
    return $ketqua;
}


// Hàm insert
function insert($table, $data){
    $key = array_keys($data);
    $truong = implode(',', $key);
    $valuetb = ':'.implode(',:',$key);


    $sql = 'INSERT INTO '.$table . '('.$truong.')'. 'VALUES('.$valuetb.')';
    // $sql = "INSERT INTO  $table ($truong) VALUES ($valuetb)";

    $kq = query($sql,$data);
    return $kq;
}

// Hàm update
function update($table, $data,$condition = ''){
    $update = '';
    foreach($data as $key => $value){
        $update .= $key . '= :'. $key .',';
    } 
    $update = trim($update, ',');
    if(!empty($condition)){
        $sql = 'UPDATE '.$table.' SET '.$update.' WHERE '.$condition;
    }else{
        $sql = 'UPDATE '.$table.' SET '.$update;
    }
    $kq = query($sql,$data);
    return $kq;
}

// Hàm delete
function delete($table, $condition = ''){
    if(empty($condition)){
        $sql = 'DELETE FROM '.$table;
    }else{
        $sql = 'DELETE FROM '.$table.' WHERE '.$condition;
    }
    $kq = query($sql);
    return $kq;
}

// Hàm select: =>>
// -- Lấy nhiều dòng dữ liệu:
function getRaw ($sql){
    $statement = query($sql, '', true); // Kiểm tra kết quả của hàm query()
    if($statement instanceof PDOStatement){
        $dataFetch = $statement->fetchAll(PDO::FETCH_ASSOC); // Sử dụng fetchAll() nếu đối tượng hợp lệ
        return $dataFetch;
    } else {
        echo "Không thể lấy dữ liệu từ cơ sở dữ liệu"; // Xử lý trường hợp đối tượng không hợp lệ
        return []; // Trả về mảng rỗng hoặc giá trị mặc định khác
    }
}
//----------------------------------------------------------------
// -- Lấy 1 dòng dữ liệu:
function oneRaw($sql){
    $kq = query($sql, '', true); 
    if(is_object($kq)){
        $dataFetch = $kq->fetch(PDO::FETCH_ASSOC);
    }
    return $dataFetch;
}
//----------------------------------------------------------------
//-- Đếm số dòng dữ liệu:
function getRows($sql){
    $statement = query($sql, '', true); 
    if($statement instanceof PDOStatement){ // Kiểm tra xem $statement là một đối tượng PDOStatement hợp lệ
        return $statement->rowCount(); // Trả về số dòng kết quả từ truy vấn
    } else {
        return 0; // Trả về giá trị mặc định nếu không có kết quả
    }
}

