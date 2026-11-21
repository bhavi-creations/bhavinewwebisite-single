<?php
include '../../db.connection/db_connection.php';
require_once '../../portfolio_lib.php';

portfolio_ensure_schema($conn);

$id = (int) ($_GET['id'] ?? 0);
if ($id <= 0) die('❌ No ID provided.');

$stmt = $conn->prepare('SELECT * FROM our_works WHERE id = ?');
$stmt->bind_param('i', $id);
$stmt->execute();
$work = $stmt->get_result()->fetch_assoc();
if (!$work) die('❌ Work not found.');

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    [$ok, $message] = portfolio_save_work($conn, $work, 'uploads/staff/');
    if ($ok) {
        echo "<script>alert(" . json_encode('✅ ' . $message) . "); window.location.href='view_works.php';</script>";
        exit;
    }
    $error = $message;
    $work = array_merge($work, array_intersect_key($_POST, $work)); // keep what was typed
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Edit Work - Bhavi Creations Dashboard</title>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,300,400,600,700,800,900" rel="stylesheet">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
</head>

<body id="page-top">
    <div id="wrapper">
        <?php include 'sidebar.php'; ?>
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <?php include 'navbar.php'; ?>

                <div class="container-fluid">
                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800">Edit Portfolio Work</h1>
                        <a href="view_works.php" class="btn btn-sm btn-outline-primary">All works</a>
                    </div>

                    <?php if ($error !== ''): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                    <?php endif; ?>

                    <div class="card shadow mb-4">
                        <div class="card-body">
                            <form method="POST" enctype="multipart/form-data" style="color:black;">
                                <?php include 'works_form_fields.php'; ?>
                                <div class="text-right">
                                    <a href="view_works.php" class="btn btn-light mx-1">Cancel</a>
                                    <button type="submit" name="update" class="btn btn-success mx-1">Update work</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="text-center my-auto">
                        <span style="color:black;">©2025 Bhavi Creations. All Rights Reserved. Designed & Developed by
                            <a href="https://bhavicreations.com/" target="_blank" style="color:black; text-decoration:none;">Bhavi Creations</a>
                        </span>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <a class="scroll-to-top rounded" href="#page-top"><i class="fas fa-angle-up"></i></a>

    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="js/sb-admin-2.min.js"></script>
</body>

</html>
