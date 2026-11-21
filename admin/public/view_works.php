<?php
// Include the database connection file
include '../../db.connection/db_connection.php';
require_once '../../portfolio_lib.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Bhavi Creations - Staff Images</title>

    <!-- Custom fonts for this template-->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,300,400,600,700,800,900" rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        <?php include 'sidebar.php'; ?>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <?php include 'navbar.php'; ?>
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid">

                    <!-- Page Heading -->
                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h2 class="h2 mb-0 text-info mx-2">Portfolio Works</h2>
                        <a href="work_images.php" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
                            <i class="fas fa-upload fa-sm text-white-50"></i> Upload Work
                        </a>
                    </div>



                    <?php
                    portfolio_ensure_schema($conn);
                    $filter = trim((string) ($_GET['type'] ?? ''));
                    $counts = [];
                    $cres = $conn->query('SELECT media_type, COUNT(*) c FROM our_works GROUP BY media_type');
                    if ($cres) while ($c = $cres->fetch_assoc()) $counts[$c['media_type']] = (int) $c['c'];
                    ?>
                    <div class="mb-4">
                        <a href="view_works.php" class="btn btn-sm mb-1 <?= $filter === '' ? 'btn-primary' : 'btn-outline-primary' ?>">All (<?= array_sum($counts) ?>)</a>
                        <?php foreach ($counts as $t => $c): ?>
                            <a href="view_works.php?type=<?= urlencode($t) ?>" class="btn btn-sm mb-1 <?= strcasecmp($filter, $t) === 0 ? 'btn-primary' : 'btn-outline-primary' ?>"><?= htmlspecialchars($t) ?> (<?= $c ?>)</a>
                        <?php endforeach; ?>
                    </div>

                    <div class="row">
                        <?php
                        if ($filter !== '') {
                            $stmt = $conn->prepare('SELECT * FROM our_works WHERE media_type = ? ORDER BY sort_order DESC, id DESC');
                            $stmt->bind_param('s', $filter);
                            $stmt->execute();
                            $result = $stmt->get_result();
                        } else {
                            $result = $conn->query('SELECT * FROM our_works ORDER BY sort_order DESC, id DESC');
                        }

                        if ($result === false) {
                            echo "<p class='mx-3 text-danger'>Error: " . htmlspecialchars($conn->error) . "</p>";
                        } elseif ($result->num_rows === 0) {
                            echo "<p class='mx-3'>No works uploaded yet.</p>";
                        } else {
                            while ($row = $result->fetch_assoc()) {
                                $id = (int) $row['id'];
                                $file = basename((string) $row['file_path']);
                                $kind = pf_file_kind($file);
                                $path = 'uploads/staff/' . rawurlencode($file);
                                $link = pf_safe_url($row['media_link'] ?? '');
                                $video = pf_video_info($link);
                                $title = trim((string) ($row['title'] ?? '')) ?: ($link && !$video['platform'] ? pf_domain($link) : '(no title)');
                                ?>
                                <div class="col-12 col-md-6 col-xl-3 mb-4">
                                    <div class="card shadow h-100">
                                        <div style="height:220px;background:#f1f3f6;overflow:hidden;position:relative">
                                            <?php if ($kind === 'image'): ?>
                                                <img src="<?= pf_e($path) ?>" alt="" style="width:100%;height:100%;object-fit:cover" loading="lazy">
                                            <?php elseif ($kind === 'video'): ?>
                                                <video src="<?= pf_e($path) ?>" muted loop playsinline preload="metadata" onmouseover="this.play()" onmouseout="this.pause()" style="width:100%;height:100%;object-fit:cover"></video>
                                            <?php elseif ($kind === 'pdf'): ?>
                                                <embed src="<?= pf_e($path) ?>" type="application/pdf" width="100%" height="220">
                                            <?php elseif ($video['embed']): ?>
                                                <iframe src="<?= pf_e($video['embed']) ?>" loading="lazy" style="width:100%;height:100%;border:0" allow="autoplay; encrypted-media"></iframe>
                                            <?php else: ?>
                                                <div class="d-flex h-100 align-items-center justify-content-center text-muted">No preview</div>
                                            <?php endif; ?>
                                            <?php if ($video['label']): ?><span class="badge badge-dark" style="position:absolute;top:8px;left:8px"><?= pf_e($video['label']) ?></span><?php endif; ?>
                                        </div>
                                        <div class="card-body d-flex flex-column">
                                            <span class="badge badge-success align-self-start mb-2"><?= pf_e($row['media_type']) ?></span>
                                            <h6 class="font-weight-bold text-dark mb-1"><?= pf_e($title) ?></h6>
                                            <?php if (!empty($row['description'])): ?><p class="small text-muted mb-1"><?= pf_e($row['description']) ?></p><?php endif; ?>
                                            <?php if ($link): ?><a href="<?= pf_e($link) ?>" target="_blank" rel="noopener" class="small text-truncate d-block mb-2"><?= pf_e($link) ?></a><?php endif; ?>
                                            <div class="mt-auto">
                                                <a href="edit_works.php?id=<?= $id ?>" class="btn btn-sm btn-warning">Edit</a>
                                                <a href="delete_works.php?id=<?= $id ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this work?')">Delete</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php
                            }
                        }
                        $conn->close();
                        ?>
                    </div>



                </div>
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="text-center my-auto">
                        <p class="mini_text" style="color:black">©2025 Bhavi Creations. Designed by
                            <a href="https://bhavicreations.com/" style="text-decoration: none; color:black" target="_blank">
                                Bhavi Creations
                            </a>
                        </p>
                    </div>
                </div>
            </footer>
            <!-- End of Footer -->

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Bootstrap core JavaScript-->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="js/sb-admin-2.min.js"></script>

</body>

</html>