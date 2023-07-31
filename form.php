<?php
session_start();
if (!isset($_SESSION['email'])) {
    header('Location: /login.php');
    exit();
}

include_once './src/database/connection.php';
$categories = $dbConn->query("SELECT id,name FROM categories");
$id = $_GET['id'] ?? null;
$isnew = true;
if (isset($id)) {
    $isnew = false;
    $stmt = $dbConn->prepare("SELECT id,name,price,quantity, image,categoryId,description FROM products WHERE id = :id");
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$product) {
        header("Location: /index.php");
        exit();
    }
}

?>


<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>SB Admin 2 - Dashboard</title>

    <!-- Custom fonts for this template-->
    <link href="./src/page/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="./src/page/css/sb-admin-2.min.css" rel="stylesheet">

    <style>
        .table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 8px;
            text-align: left;
        }

        .name-col {
            width: 80%;
        }

        .action-col {
            width: 20%;
        }

        .action-btn {
            width: 100%;
        }
    </style>
</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="../../../index.php">
                <div class="sidebar-brand-icon rotate-n-15">
                    <i class="fas fa-laugh-wink"></i>
                </div>
                <div class="sidebar-brand-text mx-3">SB Admin <sup>2</sup></div>
            </a>

            <!-- Divider -->
            <hr class="sidebar-divider my-0">

            <!-- Nav Item - Dashboard -->
            <li class="nav-item ">
                <a class="nav-link" href="../../../index.php">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span></a>
            </li>


            <!-- Nav Item - Pages Collapse Menu -->
            <li class="nav-item active">
                <a class="nav-link" href="form.php">
                    <i class="fas fa-fw fa-calendar"></i>
                    <span>Forms</span></a>
            </li>

            <!-- Nav Item - Charts -->
            <li class="nav-item">
                <a class="nav-link" href="charts.php">
                    <i class="fas fa-fw fa-chart-area"></i>
                    <span>Charts</span></a>
            </li>

            <!-- Nav Item - Tables -->
            <li class="nav-item">
                <a class="nav-link" href="tables.php">
                    <i class="fas fa-fw fa-table"></i>
                    <span>Tables</span></a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider d-none d-md-block">

            <!-- Sidebar Toggler (Sidebar) -->
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>

        </ul>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

                    <!-- Sidebar Toggle (Topbar) -->
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>

                    <!-- Topbar Search -->
                    <form class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search">
                        <div class="input-group">
                            <input type="text" class="form-control bg-light border-0 small" placeholder="Search for..." aria-label="Search" aria-describedby="basic-addon2">
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="button">
                                    <i class="fas fa-search fa-sm"></i>
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">

                        <!-- Nav Item - Search Dropdown (Visible Only XS) -->
                        <li class="nav-item dropdown no-arrow d-sm-none">
                            <a class="nav-link dropdown-toggle" href="#" id="searchDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-search fa-fw"></i>
                            </a>
                            <!-- Dropdown - Messages -->
                            <div class="dropdown-menu dropdown-menu-right p-3 shadow animated--grow-in" aria-labelledby="searchDropdown">
                                <form class="form-inline mr-auto w-100 navbar-search">
                                    <div class="input-group">
                                        <input type="text" class="form-control bg-light border-0 small" placeholder="Search for..." aria-label="Search" aria-describedby="basic-addon2">
                                        <div class="input-group-append">
                                            <button class="btn btn-primary" type="button">
                                                <i class="fas fa-search fa-sm"></i>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </li>

                        <!-- Nav Item - Alerts -->
                        <li class="nav-item dropdown no-arrow mx-1">
                            <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-bell fa-fw"></i>
                                <!-- Counter - Alerts -->
                                <span class="badge badge-danger badge-counter">3+</span>
                            </a>
                            <!-- Dropdown - Alerts -->
                            <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="alertsDropdown">
                                <h6 class="dropdown-header">
                                    Alerts Center
                                </h6>
                                <a class="dropdown-item d-flex align-items-center" href="#">
                                    <div class="mr-3">
                                        <div class="icon-circle bg-primary">
                                            <i class="fas fa-file-alt text-white"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="small text-gray-500">December 12, 2019</div>
                                        <span class="font-weight-bold">A new monthly report is ready to download!</span>
                                    </div>
                                </a>
                                <a class="dropdown-item d-flex align-items-center" href="#">
                                    <div class="mr-3">
                                        <div class="icon-circle bg-success">
                                            <i class="fas fa-donate text-white"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="small text-gray-500">December 7, 2019</div>
                                        $290.29 has been deposited into your account!
                                    </div>
                                </a>
                                <a class="dropdown-item d-flex align-items-center" href="#">
                                    <div class="mr-3">
                                        <div class="icon-circle bg-warning">
                                            <i class="fas fa-exclamation-triangle text-white"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="small text-gray-500">December 2, 2019</div>
                                        Spending Alert: We've noticed unusually high spending for your account.
                                    </div>
                                </a>
                                <a class="dropdown-item text-center small text-gray-500" href="#">Show All Alerts</a>
                            </div>
                        </li>

                        <!-- Nav Item - Messages -->
                        <li class="nav-item dropdown no-arrow mx-1">
                            <a class="nav-link dropdown-toggle" href="#" id="messagesDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-envelope fa-fw"></i>
                                <!-- Counter - Messages -->
                                <span class="badge badge-danger badge-counter">7</span>
                            </a>
                            <!-- Dropdown - Messages -->
                            <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="messagesDropdown">
                                <h6 class="dropdown-header">
                                    Message Center
                                </h6>
                                <a class="dropdown-item d-flex align-items-center" href="#">
                                    <div class="dropdown-list-image mr-3">
                                        <img class="rounded-circle" src="./src/page/img/undraw_profile_1.svg" alt="...">
                                        <div class="status-indicator bg-success"></div>
                                    </div>
                                    <div class="font-weight-bold">
                                        <div class="text-truncate">Hi there! I am wondering if you can help me with a
                                            problem I've been having.</div>
                                        <div class="small text-gray-500">Emily Fowler · 58m</div>
                                    </div>
                                </a>
                                <a class="dropdown-item d-flex align-items-center" href="#">
                                    <div class="dropdown-list-image mr-3">
                                        <img class="rounded-circle" src="./src/page/img/undraw_profile_2.svg" alt="...">
                                        <div class="status-indicator"></div>
                                    </div>
                                    <div>
                                        <div class="text-truncate">I have the photos that you ordered last month, how
                                            would you like them sent to you?</div>
                                        <div class="small text-gray-500">Jae Chun · 1d</div>
                                    </div>
                                </a>
                                <a class="dropdown-item d-flex align-items-center" href="#">
                                    <div class="dropdown-list-image mr-3">
                                        <img class="rounded-circle" src="./src/page/img/undraw_profile_3.svg" alt="...">
                                        <div class="status-indicator bg-warning"></div>
                                    </div>
                                    <div>
                                        <div class="text-truncate">Last month's report looks great, I am very happy with
                                            the progress so far, keep up the good work!</div>
                                        <div class="small text-gray-500">Morgan Alvarez · 2d</div>
                                    </div>
                                </a>
                                <a class="dropdown-item d-flex align-items-center" href="#">
                                    <div class="dropdown-list-image mr-3">
                                        <img class="rounded-circle" src="https://source.unsplash.com/Mv9hjnEUHR4/60x60" alt="...">
                                        <div class="status-indicator bg-success"></div>
                                    </div>
                                    <div>
                                        <div class="text-truncate">Am I a good boy? The reason I ask is because someone
                                            told me that people say this to all dogs, even if they aren't good...</div>
                                        <div class="small text-gray-500">Chicken the Dog · 2w</div>
                                    </div>
                                </a>
                                <a class="dropdown-item text-center small text-gray-500" href="#">Read More Messages</a>
                            </div>
                        </li>

                        <div class="topbar-divider d-none d-sm-block"></div>

                        <!-- Nav Item - User Information -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small">Douglas McGee</span>
                                <img class="img-profile rounded-circle" src="./src/page/img/undraw_profile.svg">
                            </a>
                            <!-- Dropdown - User Information -->
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="#">
                                    <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Profile
                                </a>
                                <a class="dropdown-item" href="#">
                                    <i class="fas fa-cogs fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Settings
                                </a>
                                <a class="dropdown-item" href="#">
                                    <i class="fas fa-list fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Activity Log
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Logout
                                </a>
                            </div>
                        </li>

                    </ul>

                </nav>
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container m-1">
                    <div class="container mt-3 bg-white rounded py-4">
                        <?php
                        // if ($isnew) {
                        //     echo "<h2>Thêm sản phẩm</h2>";
                        // } else {
                        //     echo "<h2>Chỉnh sửa sản phẩm</h2>";
                        // }
                        ?>

                        <div class="container">
                            <div class="row align-items-start">
                                <div class="col-0">
                                    <h2 class="input-group-append">Thêm sản phẩm</h2>
                                </div>
                                <div class="col">
                                    <div class=""></div>
                                </div>
                                <div class="col-0">
                                    <button class="btn btn-info btn-sm uploadBtn" href="#" data-toggle="modal" data-target="#categoryMotal">
                                        Category
                                        <i class="fas fa-list fa-sm fa-fw ml-2 text-gray-400"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <form name="add_edit" id="form-submit" method="post">
                            <div class=" mb-3 mt-3">
                                <label for="name">Tên sản phẩm:</label>
                                <input value="<?php if (!$isnew) echo "{$product['name']}"; ?>" type="text" class="form-control" id="name" placeholder="Enter name" name="name">
                            </div>
                            <div class="mb-3 mt-3">
                                <label for="price">Giá sản phẩm:</label>
                                <input value="<?php if (!$isnew) echo "{$product['price']}"; ?>" type="number" class="form-control" id="price" placeholder="Enter price" name="price">
                            </div>
                            <div class="mb-3 mt-3">
                                <label for="quantity">Số lượng:</label>
                                <input value="<?php if (!$isnew) echo "{$product['quantity']}"; ?>" type="number" class="form-control" id="quantity" placeholder="Enter quantity" name="quantity">
                            </div>
                            <div class="mb-3 mt-3">
                                <label for="image">Hình ảnh:</label>
                                <textarea id="imageUpdoad" name="imageUpdoad" hidden="hidden"></textarea>
                                <input type="file" name="img[]" class="file" accept="image/*">
                                <div class="input-group mb-3">
                                    <div class="input-group-append">
                                        <button type="button" class="browse btn btn-primary">Browse...</button>
                                    </div>
                                    <input type="text" class="form-control" disabled placeholder="Upload File" id="file">
                                    <div class="input-group-append">
                                        <button type="submit" class='btn btn-success btn-sm uploadBtn' id="uploadImg">
                                            <i class="fas fa-upload"></i> Upload</button>
                                    </div>
                                </div>
                                <progress value="0" max="100" id="progress" class="progressbar"></progress>
                                <div class="m-0 p-0 col-sm-6">
                                    <img id="preview" width='150' height='225' src="<?php if (!$isnew) echo "{$product['image']}";
                                                                                    else echo "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAALcAAAETCAMAAABDSmfhAAAAOVBMVEXMzMzPz8+RkZGxsbHJycnAwMDExMTLy8uwsLCQkJCUlJS9vb21tbWqqqq4uLjGxsajo6Oenp6mpqZnlVg1AAADpklEQVR4nO3cSXLbMBAFUDTmeeD9D5tugLLjiryMlOG/SmSSoMsfYAPQikoBAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA/HeI9o9S7Dl3fdA3d/ru6T4Y9OXKG6zKfzksrbOX04sPytMsFLPWjZuoaa27HFQ+iG8JTlFzbpq6mbzkNF015/Hs1qKXmako6mnWpbmXLq0qV97A58y5B3+Q04VCbkRy9uTWNYlIPuS/khuvxVW21jsGfEWJXDUf0mqcnYearnkaneMP687oD81n1JuyMsIUlwrSPx7916emnpXkbmuX7aKag5TtCrv1Sp6vppO750BClV1G/OmlJ8pr//LcIXeS3HNKbpM5utqZztpi9SR/Tzwyc1zr8nT6JnHd7pHNLy/wXaCSe7WzOty5XTq5yWh35fveNpfOvOhwbjkfyZW0H0vury5wp3n4PnP3dApGufRYUFZ+zFFe/C5LJU/OLVes5N4Nz2fxb0TrGn7kONS8vox30fceRF2vx81NiofX8HDqZCR/HgvX2mtj80jxRqJzTv7a9R0zrxJhz9ZHbqPPFD2zVu0SOq38rNyekUO/ur6piTzbMPLoZXbuGXmPupI5N/O9H/IiIz/4UewFhKvFnhlZ0tNd6rcGF3vLSY44ZKWx9+9d7urUxvWoGa/5Owl3KZB0RbYb2YDknvds9Htarezt5IQ0cwktudM2UuTN87pzreVD5VWRvxp0FVPnMU9G1Xd9QcmGZMXmSpcOeDloj6Q8A+mjF06apNwtfwlLe0as+8o77G2c3DX3Mky+zXq3hCLzzpZ7P5Qms0OONuO5I85oX574C6KPg2+f+0fTrwcAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAD/ku/evVrkpfK/3Hn+ff7iT+cv1oz7jP6ZjAyp8fEO866UK7FVbyg02q/attV4KrFSdVRe/fJtVsmU2G3xoVKPgy4O16nGxrlnHM6pqigSlUGVSgu1UdzvoR/KjqpKiYaief2gT9P5D6seS7VXjRyq1cW94XCD+0SNk5Mx5hrKUHHGyMXzPJQvRLWW6N+Q2/Bo2kaGgwcTPIerw6s4ZLyb52fAB2e8JbfvlrumgvW1WB+iNd7wA3nDW6y9lOpQ3rrAAzzkvBby3cmroLuiLi+C5pNhuWlYUo56rYFru1r+TUfutP9h3LOh/Bte//w3ZPxP/QBTfB24xiyfswAAAABJRU5ErkJggg=="; ?>" class="img-thumbnail">
                                </div>
                            </div>
                            <div class="mb-3 mt-3">
                                <label for="supplier">Loại sản phẩm:</label>
                                <select class="form-control" id="supplier" name="categoryId">
                                    <?php
                                    while ($category = $categories->fetch(PDO::FETCH_ASSOC)) {
                                        if (!$isnew) {
                                            if ($category['id'] == $product['categoryId']) {
                                                $seleted = "selected";
                                            } else {
                                                $seleted = "";
                                            }
                                            echo "<option value='{$category['id']}' {$seleted}>{$category['name']}</option>";
                                        } else {
                                            echo "<option value='{$category['id']}' >{$category['name']}</option>";
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="mb-3 mt-3">
                                <label for="description">Mô tả:</label>~
                                <textarea class="form-control" id="description" placeholder="Enter description" name="description"><?php if (!$isnew) echo "{$product['description']}"; ?></textarea>
                            </div>
                            <button id="btncheck" name="btncheck" class="btn btn-primary">Submit</button>
                        </form>
                    </div>
                </div>
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Copyright &copy; Your Website 2021</span>
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

    <!-- Logout Modal-->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <a class="btn btn-primary" href="/src/api/logout.php">Logout</a>
                </div>
            </div>
        </div>
    </div>

    <div class="modal" id="categoryMotal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Loại sản phẩm</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="mb-3 mt-3">
                        <div class="container mb-3 mt-3">
                            <div class="row align-items-center ">
                                <div class="col">
                                    <input type="text" class="form-control" id="nameCategory" placeholder="Nhập tên loại sản phẩm" name="quantity">
                                </div>
                                <div class="col-0">
                                    <button id="addCategory" class="btn btn-outline-success">
                                        Thêm
                                    </button>
                                </div>
                            </div>
                        </div>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th class="name-col">Name</th>
                                    <th class="action-col">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                include_once './src/database/connection.php';
                                $categories = $dbConn->query("SELECT id, name FROM categories");
                                while ($row = $categories->fetch(PDO::FETCH_ASSOC)) {
                                    echo "<tr>";
                                    echo "<td class='name-col'>{$row['name']}</td>";
                                    echo "<td class='action-col'>
                                            <button onclick='deleteCategory({$row['id']})' class='action-btn btn btn-outline-danger btn-sm first-letter:'>Delete</button>
                                        </td>";
                                    echo "</tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>


    <!-- Bootstrap core JavaScript-->
    <script src="./src/page/vendor/jquery/jquery.min.js"></script>
    <script src="./src/page/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="./src/page/vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="./src/page/js/sb-admin-2.min.js"></script>

    <!-- Page level plugins -->
    <script src="./src/page/vendor/chart.js/Chart.min.js"></script>

    <!-- Page level custom scripts -->
    <script src="./src/page/js/demo/chart-area-demo.js"></script>
    <script src="./src/page/js/demo/chart-pie-demo.js"></script>

    <script src="https://www.gstatic.com/firebasejs/7.13.1/firebase-app.js"></script>
    <script src="https://www.gstatic.com/firebasejs/7.13.1/firebase-storage.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        var file = [];
        const firebaseConfig = {
            apiKey: "AIzaSyBYuqxMPpBNwkv-ElVVAG_AjGStqg7SsTk",
            authDomain: "api-php-image.firebaseapp.com",
            projectId: "api-php-image",
            storageBucket: "api-php-image.appspot.com",
            messagingSenderId: "890040871832",
            appId: "1:890040871832:web:77979cd7845564e3d759ed",
            measurementId: "G-RZDEZENL0D"
        };
        firebase.initializeApp(firebaseConfig);

        $(document).on("click", ".browse", function() {
            var file2 = $(this).parents().find(".file");
            file2.trigger("click");

        });
        $('input[type="file"]').change(function(e) {
            file = [];
            file.push(e.target.files[0]);
            var fileName = e.target.files[0].name;
            $("#file").val(fileName);
            var reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById("preview").src = e.target.result;
            };
            reader.readAsDataURL(this.files[0]);
        });

        document.getElementById("uploadImg").addEventListener("click", function() {
            event.preventDefault();
            if (file.length != 0) {
                for (let i = 0; i < file.length; i++) {
                    var storage = firebase.storage().ref('images/' + file[i].name);
                    var upload = storage.put(file[i]);

                    upload.on(
                        "state_changed",
                        function progress(snapshot) {
                            var percentage =
                                (snapshot.bytesTransferred / snapshot.totalBytes) * 100;
                            document.getElementById("progress").value = percentage;
                        },

                        function error() {
                            alert("error uploading file");
                        },

                        function complete() {
                            getFileUrl('images/' + file[i].name);
                        }
                    );
                }
            } else {
                Swal.fire({
                    title: 'Thông báo',
                    icon: 'error',
                    text: 'Vui lòng chọn ảnh truớc khi upload',
                    confirmButtonText: 'Đồng ý',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {}
                })
            }
        });

        function getFileUrl(filename) {
            var storage = firebase.storage().ref(filename);

            storage
                .getDownloadURL()
                .then(function(url) {
                    document.getElementById("imageUpdoad").innerHTML = url;
                    console.log(url);
                })
                .catch(function(error) {
                    console.log("error encountered");
                });
        }

        document.getElementById("btncheck").addEventListener("click", function() {
            event.preventDefault();
            var idValue = <?php echo isset($id) ? json_encode($id) : 'null'; ?>;
            var name = document.getElementById("name");
            var price = document.getElementById("price");
            var quantity = document.getElementById("quantity");
            var image = document.getElementById("imageUpdoad");
            var description = document.getElementById("description");
            var categoryId = document.getElementById("supplier");

            var imageName = <?php echo isset($isnew) ? "'{$product['image']}'" : "" ?>;
            imageName = imageName.replace("https://firebasestorage.googleapis.com/v0/b/api-php-image.appspot.com/o/images%2F", "");
            imageName = imageName.replace("?alt=media", "");

            if (file.length != 0) {
                imageName = file[0].name;
            } else if (idValue) {
                imageName = imageName;
            } else {
                imageName = "";
            }

            console.log(imageName);
            if (name.value == "") {
                Swal.fire({
                    title: 'Thông báo',
                    icon: 'error',
                    text: 'Vui lòng nhập tên sản phẩm',
                    confirmButtonText: 'Đồng ý',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        name.style.border = "1px solid red";
                    }
                })
            } else if (price.value == "") {
                Swal.fire({
                    title: 'Thông báo',
                    icon: 'error',
                    text: 'Vui lòng nhập giá sản phẩm',
                    confirmButtonText: 'Đồng ý',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        price.style.border = "1px solid red";
                    }
                })

            } else if (quantity.value == "") {
                Swal.fire({
                    title: 'Thông báo',
                    icon: 'error',
                    text: 'Vui lòng nhập số lượng sản phẩm',
                    confirmButtonText: 'Đồng ý',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        quantity.style.border = "1px solid red";
                    }
                })

            } else if (description.value == "") {
                Swal.fire({
                    title: 'Thông báo',
                    icon: 'error',
                    text: 'Vui lòng nhập mô tả sản phẩm',
                    confirmButtonText: 'Đồng ý',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        description.style.border = "1px solid red";
                    }
                })

            } else if (categoryId.value == "") {
                Swal.fire({
                    title: 'Thông báo',
                    icon: 'error',
                    text: 'Vui lòng chọn loại sản phẩm',
                    confirmButtonText: 'Đồng ý',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        categoryId.style.border = "1px solid red";
                    }
                })

            } else if (image.value == "" && !idValue) {
                Swal.fire({
                    title: 'Thông báo',
                    icon: 'error',
                    text: 'Vui lòng chọn và upload ảnh truớc khi lưu',
                    confirmButtonText: 'Đồng ý',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {}
                })
            } else {
                var xmlhttp = new XMLHttpRequest();
                xmlhttp.onreadystatechange = function() {
                    var response = this.responseText;
                    if (this.readyState == 4 && this.status == 200) {
                        let json = JSON.parse(response);
                        if (json.message === "added") {
                            Swal.fire({
                                title: 'Thông báo',
                                icon: 'success',
                                text: 'Thêm sản phẩm thành công',
                                confirmButtonText: 'Đồng ý',
                                reverseButtons: true
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    location.href = "/tables.php";
                                }
                            })
                        } else if (json.message === "updated") {
                            Swal.fire({
                                title: 'Thông báo',
                                icon: 'success',
                                text: 'Update sản phẩm thành công',
                                confirmButtonText: 'Đồng ý',
                                reverseButtons: true
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    location.href = "/tables.php";
                                }
                            })
                        } else {
                            Swal.fire({
                                title: 'Thông báo',
                                icon: 'error',
                                text: 'Thất bại',
                                confirmButtonText: 'Đồng ý',
                                reverseButtons: true
                            }).then((result) => {
                                if (result.isConfirmed) {

                                }
                            })
                        }
                    }
                }
                xmlhttp.open("POST", "./src/api/add_edit_product.php", true);
                xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");


                if (idValue) {
                    xmlhttp.send("id=" + idValue + "&name=" + name.value + "&price=" + price.value + "&quantity=" + quantity.value + "&categoryId=" + categoryId.value + "&description=" + description.value + "&imageUpdoad=" + imageName);

                } else {
                    xmlhttp.send("name=" + name.value + "&price=" + price.value + "&quantity=" + quantity.value + "&categoryId=" + categoryId.value + "&description=" + description.value + "&imageUpdoad=" + imageName);
                }
            }
        });

        function deleteCategory(id) {
            Swal.fire({
                title: 'Xóa loại sản phẩm',
                text: "Bạn có chắc chắn muốn xóa loại sản phẩm này không?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Xóa',
                cancelButtonText: 'Hủy'
            }).then((result) => {
                if (result.isConfirmed) {
                    var xmlhttp = new XMLHttpRequest();
                    xmlhttp.onreadystatechange = function() {
                        var response = this.responseText;
                        if (this.readyState == 4 && this.status == 200) {
                            let json = JSON.parse(response);
                            if (json.message === "success") {
                                Swal.fire({
                                    title: 'Thông báo',
                                    icon: 'success',
                                    text: 'Xóa loại sản phẩm thành công',
                                    confirmButtonText: 'Đồng ý',
                                    reverseButtons: true
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        location.reload();
                                    }
                                })
                            } else {
                                Swal.fire({
                                    title: 'Thông báo',
                                    icon: 'error',
                                    text: 'Xóa loại sản phẩm thất bại. Hãy chắc chắn rằng loại sản phẩm "' + json.name + '" chưa được sử dụng',
                                    confirmButtonText: 'Đồng ý',
                                    reverseButtons: true
                                }).then((result) => {
                                    if (result.isConfirmed) {

                                    }
                                })
                            }
                        }
                    }
                    xmlhttp.open("POST", "./src/api/delete_category_by_id.php", true);
                    xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                    xmlhttp.send("id=" + id);
                }
            })
        };
        document.getElementById("addCategory").addEventListener("click", function() {
            let name = document.getElementById("nameCategory");
            if (name.value == "") {
                Swal.fire({
                    title: 'Thông báo',
                    icon: 'error',
                    text: 'Vui lòng nhập tên loại sản phẩm',
                    confirmButtonText: 'Đồng ý',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        name.style.border = "1px solid red";
                    }
                })
            } else {
                var xmlhttp = new XMLHttpRequest();
                xmlhttp.onreadystatechange = function() {
                    var response = this.responseText;
                    if (this.readyState == 4 && this.status == 200) {
                        let json = JSON.parse(response);
                        if (json.message === "success") {
                            Swal.fire({
                                title: 'Thông báo',
                                icon: 'success',
                                text: 'Thêm loại sản phẩm thành công',
                                confirmButtonText: 'Đồng ý',
                                reverseButtons: true
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    location.reload();
                                }
                            })
                        } else {
                            Swal.fire({
                                title: 'Thông báo',
                                icon: 'error',
                                text: 'Thêm loại sản phẩm thất bại. Đảm bảo rằng không trùng loại sản phẩm',
                                confirmButtonText: 'Đồng ý',
                                reverseButtons: true
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    
                                }
                            })
                        }
                    }


                }
                xmlhttp.open("POST", "./src/api/add_category.php", true);
                xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                xmlhttp.send("name=" + name.value);
            }
        });
    </script>

</body>

</html>