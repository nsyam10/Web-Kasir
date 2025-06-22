<?php
session_start();
session_destroy(); // Hapus semua session

// Redirect ke halaman login
header("Location: login.php");
exit;
?>
