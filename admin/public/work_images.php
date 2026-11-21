<?php require_once '../../portfolio_lib.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Bhavi Creations - Add Work</title>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
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
                        <h1 class="h3 mb-0 text-gray-800">Add Portfolio Work</h1>
                        <a href="view_works.php" class="btn btn-sm btn-outline-primary">All works</a>
                    </div>

                    <div class="card shadow mb-4">
                        <div class="card-body">
                            <form id="mediaUploadForm" action="upload_works.php" method="POST" enctype="multipart/form-data" style="color:black;">
                                <?php $work = []; include 'works_form_fields.php'; ?>
                                <div class="text-right">
                                    <button type="reset" class="btn btn-light mx-1">Clear</button>
                                    <button type="submit" class="btn btn-success mx-1">Save work</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="text-center my-auto">
                        <p class="mini_text" style="color:black">©2025 Bhavi Creations. Designed by
                            <a href="https://bhavicreations.com/" style="text-decoration: none; color:black" target="_blank">Bhavi Creations</a>
                        </p>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <a class="scroll-to-top rounded" href="#page-top"><i class="fas fa-angle-up"></i></a>

    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="js/sb-admin-2.min.js"></script>
</body>

</html>
