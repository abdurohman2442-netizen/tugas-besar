<?php

require_once ("koneksi.php")




















if ($user && password_verify($pass, $user['password'])) {


$_SESSION['id'] = $user['id'];
$_SESSION['username'] = $user ['username'];
$_SESSION['nama'] = $user ['nama'];
$_SESSION['role'] = $user ['role'];
$_SESSION['kelas'] = $user ['kelas'];
$_SESSION['login'] = true;
 
//beralih ke halaman index 
header("location: index.php");
exit;

}else {
    echo "<script>
    alert('username atau password salah !'); window.location='login.php';
    </script">
}