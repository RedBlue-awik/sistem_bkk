<?php
session_start();
require './src/functions.php';
if (isset($_SESSION['id_pengguna'])) {
    $id_user = $_SESSION['id_pengguna'];
    mysqli_query($conn, "DELETE FROM online_users WHERE id_user = '$id_user'");
}
$_SESSION = [];
session_unset();
session_destroy();

header("Location: index.php");
exit;
