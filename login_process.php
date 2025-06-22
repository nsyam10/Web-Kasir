<?php
session_start();
include 'koneksi.php';

$username = mysqli_real_escape_string($conn, $_POST['username']);
$password = $_POST['password']; // Ambil password asli, JANGAN di-hash


$query = mysqli_query($conn, "SELECT * FROM user WHERE LOWER(username)=LOWER('$username')");
$data = mysqli_fetch_assoc($query);

// Hapus debug
// echo "Input Username: $username <br>";
// echo "Input Password: $password <br>";
// echo "Password Hash dari DB: " . $data['password'] . "<br>";
// exit;


if ($data && password_verify($password, $data['password'])) {
    $_SESSION['username'] = $data['username'];
    header("Location: dashboard.php");
    exit;
} else {
    echo "<script>alert('Username atau password salah!');window.location='login.php';</script>";
}
?>
