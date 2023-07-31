<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>SB Admin 2 - Register</title>

    <!-- Custom fonts for this template-->
    <link href="./src/page/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="./src/page/css/sb-admin-2.min.css" rel="stylesheet">

</head>

<body class="bg-gradient-primary">
    <div class="container">
        <div class="card o-hidden border-0 shadow-lg my-5">
            <div class="card-body p-0">
                <!-- Nested Row within Card Body -->
                <div class="row">
                    <div class="col-lg-5 d-none d-lg-block bg-register-image"></div>
                    <div class="col-lg-7">
                        <div class="p-5">
                            <div class="text-center">
                                <h1 class="h4 text-gray-900 mb-4">Create an Account!</h1>
                            </div>

                            <form class="user" onsubmit="return validateForm();">
                                <div class="form-group">
                                    <input name="email" type="email" class="form-control form-control-user" id="email_input" aria-describedby="emailHelp" placeholder="Enter Email Address...">
                                </div>
                                <div class="form-group">
                                    <input type="password" class="form-control form-control-user" id="password_input" aria-describedby="" placeholder="Password">
                                </div>
                                <button type="submit" class="btn btn-primary btn-user btn-block">
                                    Register Account
                                </button>
                                <hr>
                                <a href="#" class="btn btn-google btn-user btn-block">
                                    <i class="fab fa-google fa-fw"></i> Register with Google
                                </a>
                                <a href="#" class="btn btn-facebook btn-user btn-block">
                                    <i class="fab fa-facebook-f fa-fw"></i> Register with Facebook
                                </a>
                            </form>

                            <hr>
                            <div class="text-center">
                                <a class="small" href="forgot-password.php">Forgot Password?</a>
                            </div>
                            <div class="text-center">
                                <a class="small" href="login.php">Already have an account? Login!</a>
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
        function validateForm() {
            event.preventDefault();
            var email = document.getElementById("email_input").value;
            var password = document.getElementById("password_input").value;
            var emailRegex = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;
            var passwordRegex = /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{6,}$/;
            if (email === "") {
                Swal.fire({
                    title: 'Thông báo',
                    icon: 'error',
                    text: 'Vui lòng nhập email.',
                    confirmButtonText: 'Đồng ý',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        return false;
                    }
                })
            } else if (!email.match(emailRegex)) {
                Swal.fire({
                    title: 'Thông báo',
                    icon: 'error',
                    text: 'Email không hợp lệ.',
                    confirmButtonText: 'Đồng ý',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        return false;
                    }
                })
            } else if (password === "") {
                Swal.fire({
                    title: 'Thông báo',
                    icon: 'error',
                    text: 'Vui lòng nhập mật khẩu.',
                    confirmButtonText: 'Đồng ý',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        return false;
                    }
                })
            } else if (!password.match(passwordRegex)) {
                Swal.fire({
                    title: 'Thông báo',
                    icon: 'error',
                    text: 'Mật khẩu phải có ít nhất 6 ký tự, bao gồm ít nhất một chữ số, một chữ thường và một chữ hoa.',
                    confirmButtonText: 'Đồng ý',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        return false;
                    }
                })
            } else {
                var xmlhttp = new XMLHttpRequest();
                xmlhttp.onreadystatechange = function() {
                    var response = this.responseText;
                    if (this.readyState == 4 && this.status == 200) {
                        let json = JSON.parse(response);
                        if (json.status === "true") {
                            Swal.fire({
                                title: 'Thông báo',
                                icon: 'success',
                                text: 'Đăng kí thành công',
                                confirmButtonText: 'Đồng ý',
                                reverseButtons: true
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    location.href = "/index.php";
                                }
                            })
                        } else {
                            if (this.readyState == 4 && this.status == 200) {
                                Swal.fire({
                                    title: 'Thông báo',
                                    icon: 'error',
                                    text: 'Email đã tồn tại',
                                    confirmButtonText: 'Đồng ý',
                                    reverseButtons: true
                                }).then((result) => {
                                    if (result.isConfirmed) {

                                    }
                                })
                            }
                        }
                    }
                }
                xmlhttp.open("POST", "./src/api/register.php", true);
                xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                xmlhttp.send("email=" + email + "&password=" + password);
            }
        }
    </script>



</body>

</html>