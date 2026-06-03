<?php
session_start();

// Hapus semua sesi
session_unset();
session_destroy();

// Redirect ke halaman utama
header('Location: login.php');
exit();
?>
