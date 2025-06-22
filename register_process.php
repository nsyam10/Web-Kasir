<?php
include "koneksi.php";

$username = mysqli_real_escape_string($conn, $_POST['username']);
$password = password_hash($_POST['password'], PASSWORD_DEFAULT);

// Cek duplikat username
$cek = mysqli_query($conn, "SELECT * FROM users WHERE username = '$username'");
if (mysqli_num_rows($cek) > 0) {
    echo "<script>alert('Username sudah digunakan'); window.location='register.php';</script>";
    exit;
}

// Simpan ke database
$query = mysqli_query($conn, "INSERT INTO users (username, password) VALUES ('$username', '$password')");

if ($query) {
    echo "<script>alert('Registrasi berhasil! Silakan login'); window.location='login.php';</script>";
} else {
    echo "<script>alert('Registrasi gagal'); window.location='register.php';</script>";
}
?>
