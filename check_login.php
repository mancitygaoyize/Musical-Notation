<?php
session_start();
$conn = mysqli_connect('localhost','root','root','web_db');

$user = $_POST['username'];
$pwd = $_POST['password'];

$sql = "SELECT * FROM user WHERE username='$user' AND password='$pwd'";
$res = mysqli_query($conn,$sql);

if(mysqli_num_rows($res) > 0){
    $_SESSION['user'] = $user;
    header("Location:index.html");
}else{
    echo "账号或密码错误！<a href='login.html'>返回</a>";
}
?>