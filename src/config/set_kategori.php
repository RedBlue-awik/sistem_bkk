<?php
session_start();
if (isset($_POST['kategori'])) {
    $_SESSION['kategori_filter'] = $_POST['kategori'];
    echo json_encode(['success' => true, 'kategori' => $_POST['kategori']]);
} else {
    echo json_encode(['success' => false, 'message' => 'Kategori tidak ditemukan']);
}
