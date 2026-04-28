<?php
session_start();
$conn = mysqli_connect('localhost','root','root','web_db');

$username = $_POST['username'];
$password = $_POST['password'];

$sql = "INSERT INTO user(username,password) VALUES('$username','$password')";
if(mysqli_query($conn,$sql)){
    $_SESSION['user'] = $username;
    header("Location:index.html");
}else{
    echo "用户名已存在！<a href='register.html'>返回</a>";
}
?>