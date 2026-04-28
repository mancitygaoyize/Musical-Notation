<?php
session_start();
// 清空会话
$_SESSION = [];
session_destroy();
// 清除cookie
setcookie(session_name(), '', time()-3600, '/');
// 跳回首页
header('Location: index.php');
exit;