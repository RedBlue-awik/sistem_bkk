<?php
session_start();
unset($_SESSION['kategori_filter']);
echo json_encode(['success' => true]);
