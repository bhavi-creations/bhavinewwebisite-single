<?php
include '../../db.connection/db_connection.php';
require_once '../../portfolio_lib.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: work_images.php');
    exit;
}

portfolio_ensure_schema($conn);
[$ok, $message] = portfolio_save_work($conn, null);
$conn->close();

$msg = json_encode(($ok ? '✅ ' : '❌ ') . $message);
echo $ok
    ? "<script>alert($msg); window.location.href = 'view_works.php';</script>"
    : "<script>alert($msg); history.back();</script>";
