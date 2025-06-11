<?php

require '../config/database.php';
require '../utils/session.php';
requireRole(['admin', 'editor']);

// Validate and sanitize 'id'
if (!isset($_GET['id']) || !filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
    header('Location: index.php');
    exit;
}
$id = (int)$_GET['id'];

$stmt = $conn->prepare("DELETE FROM posts WHERE id = ?");
$stmt->execute([$id]);

header('Location: index.php');
?>