<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>SB Admin 2 - Login</title>

    <!-- Custom fonts for this template-->
    <link href="./src/page/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="./src/page/css/sb-admin-2.min.css" rel="stylesheet">

</head>

<body class="bg-gradient-primary">

    <div class="container">

        <!-- Outer Row -->
        <div class="row justify-content-center">

            <div class="col-xl-10 col-lg-12 col-md-9">

                <div class="card o-hidden border-0 shadow-lg my-5">
                    <div class="card-body p-0">
                        <!-- Nested Row within Card Body -->
                        <div class="row">
                            <div class="col-lg-6 d-none d-lg-block bg-login-image"></div>
                            <div class="col-lg-6">
                                <div class="p-5">
                                    <div class="text-center">
                                        <h1 class="h4 text-gray-900 mb-4">Welcome Back!</h1>
                                    </div>
                                    <form class="user">
                                        <div class="form-group">
                                            <input type="email" class="form-control form-control-user" id="email_input" aria-describedby="emailHelp" placeholder="Enter Email Address...">
                                        </div>
                                        <div class="form-group">
                                            <input type="password" class="form-control form-control-user" id="password_input" placeholder="Password">
                                        </div>
                                        <div class="form-group">
                                            <div class="custom-control custom-checkbox small">
                                                <input type="checkbox" class="custom-control-input" id="customCheck">
                                                <label class="custom-control-label" for="customCheck">Remember
                                                    Me</label>
                                            </div>
                                        </div>
                                        <button id="submit" class="btn btn-primary btn-user btn-block">
                                            Login
                                        </button>
                                        <hr>
                                        <a href="index.php" class="btn btn-google btn-user btn-block">
                                            <i class="fab fa-google fa-fw"></i> Login with Google
                                        </a>
                                        <a href="index.php" class="btn btn-facebook btn-user btn-block">
                                            <i class="fab fa-facebook-f fa-fw"></i> Login with Facebook
                                        </a>
                                    </form>
                                    <hr>
                                    <div class="text-center">
                                        <a class="small" href="forgot-password.php">Forgot Password?</a>
                                    </div>
                                    <div class="text-center">
                                        <a class="small" href="register.php">Create an Account!</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        var btn = document.getElementById('submit');
        btn.addEventListener('click', func);

        function func() {
            event.preventDefault();
            var email = document.getElementById('email_input').value;
            var password = document.getElementById('password_input').value;
            var xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange = function() {
                var response = this.responseText;
                if (this.readyState == 4 && this.status == 200) {
                    let json = JSON.parse(response);
                    if (json.status === "true") {
                        Swal.fire({
                            title: 'Thông báo',
                            icon: 'success',
                            text: 'Login success',
                            confirmButtonText: 'Đồng ý',
                            reverseButtons: true
                        }).then((result) => {
                            if (result.isConfirmed) {
                                let http = new XMLHttpRequest();
                                http.open("POST", "./src/api/session_start.php", true);
                                http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                                http.send("email=" + email);
                                http.onreadystatechange =function(){
                                    if (this.readyState == 4 && this.status == 200) {
                                        window.location.href = "/index.php";
                                    }
                                }
                            }
                        })
                    } else {
                        Swal.fire({
                            title: 'Thông báo',
                            icon: 'error',
                            text: 'Sai tên đăng nhập hoặc mật khẩu',
                            confirmButtonText: 'Đồng ý',
                            reverseButtons: true
                        }).then((result) => {
                            if (result.isConfirmed) {

                            }
                        })
                    }
                }
            }
            xmlhttp.open("POST", "./src/api/login.php", true);
            xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xmlhttp.send("email=" + email + "&password=" + password);
        }
    </script>
</body>

</html>