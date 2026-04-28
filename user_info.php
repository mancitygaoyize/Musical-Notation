<?php
session_start();
$user = $_SESSION['user'] ?? null;
echo json_encode(['username'=>$user]);
?>