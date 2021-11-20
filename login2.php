<?php

session_start();

require 'functions.php';

//cek cookie
if( isset($_COOKIE['id']) && isset($_COOKIE['key']) ) {
    $id = $_COOKIE['id'];
    $key = $_COOKIE['key'];

    $result = mysqli_query($conn, "SELECT username FROM tb_user WHERE id = '$id'");
    $row = mysqli_fetch_assoc($result);

    //cek cookie dan username
    if( $key === hash('sha256', $row['username']) ){
        $_SESSION['login'] = true;
    }
  }

  // cek session
if( isset($_SESSION["login"]) ) {
  header("Location: index.php");
  exit;
}


//ketika akan masuk ke program
if (isset($_POST['login']) ) {

    $username = $_POST['username'];
    $password = $_POST['password'];

    $result = mysqli_query($conn, "SELECT * FROM tb_user WHERE username = '$username'");

    //cek username
    if( mysqli_num_rows($result) === 1 ) {

        //cek password
        $row = mysqli_fetch_assoc($result);

        if( password_verify($password, $row['password']) ) {

            //set session
            $_SESSION["login"] = true;
            $_SESSION['id'] = $row['id'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['level'] = $row['level'];
            $_SESSION['foto'] = $row['foto'];

            //cek remember me
            if ( isset($_POST['remember']) ) {

                //buat cookie
                setcookie('id', $row['id'], time()+60);
                setcookie('key', hash('sha256', $row['username']), time()+60);
            }

            header("Location: index.php");
            exit;

        } else {
            echo "<script>
                    alert('User atau Password salah!');
                  </script>";
        }

    } else {
        echo "<script>
				        alert('User tidak ditemukan!');
			        </script>";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
	<title>SISTEM INFORMASI WAREHOUSE MANAGEMENT SYSTEM</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
<!--===============================================================================================-->	
	<link rel="icon" type="image/png" href="Login_v18/images/icons/favicon.ico"/>
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="Login_v18/vendor/bootstrap/css/bootstrap.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="Login_v18/fonts/font-awesome-4.7.0/css/font-awesome.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="Login_v18/fonts/Linearicons-Free-v1.0.0/icon-font.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="Login_v18/vendor/animate/animate.css">
<!--===============================================================================================-->	
	<link rel="stylesheet" type="text/css" href="Login_v18/vendor/css-hamburgers/hamburgers.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="Login_v18/vendor/animsition/css/animsition.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="Login_v18/vendor/select2/select2.min.css">
<!--===============================================================================================-->	
	<link rel="stylesheet" type="text/css" href="Login_v18/vendor/daterangepicker/daterangepicker.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="Login_v18/css/util.css">
	<link rel="stylesheet" type="text/css" href="Login_v18/css/main.css">
<!--===============================================================================================-->
</head>
<body style="background-color: #666666;">
	
	<div class="limiter">
		<div class="container-login100">
			<div class="wrap-login100">
				<form class="login100-form validate-form" action="" method="post">
					<span class="login100-form-title p-b-43">
						Warehouse Management System
					</span>
					
					
					<div class="wrap-input100 validate-input" data-validate = "Valid username is required">
                        <input
                            type="text"
                            class="input100"
                            id="username"
                            name="username"
                        />
						<span class="focus-input100"></span>
						<span class="label-input100">Username</span>
					</div>
					
					
					<div class="wrap-input100 validate-input" data-validate="Password is required">
                        <input
                            type="password"
                            class="input100"
                            id="password"
                            name="password"
                        />
						<span class="focus-input100"></span>
						<span class="label-input100">Password</span>
					</div>

					<div class="flex-sb-m w-full p-t-3 p-b-32">
						<div class="contact100-form-checkbox">
                            <input
                            type="checkbox"
                            class="input-checkbox100"
                            id="exampleCheck2"
                            name="remember"
                            />
                            <label class="label-checkbox100" for="exampleCheck2">
                                Remember me
                            </label>
						</div>

						<div>
							<a href="#" class="txt1">
								Forgot Password?
							</a>
						</div>
					</div>
			

					<div class="container-login100-form-btn">
						<button style="background-color: limegreen;" class="login100-form-btn" name="login">
							Login
						</button>
					</div>
				</form>

				<div class="login100-more" style="background-image: url('Login_v18/images/wms.jpg');">
				</div>
			</div>
		</div>
	</div>
	
	

	
	
<!--===============================================================================================-->
	<script src="Login_v18/vendor/jquery/jquery-3.2.1.min.js"></script>
<!--===============================================================================================-->
	<script src="Login_v18/vendor/animsition/js/animsition.min.js"></script>
<!--===============================================================================================-->
	<script src="Login_v18/vendor/bootstrap/js/popper.js"></script>
	<script src="Login_v18/vendor/bootstrap/js/bootstrap.min.js"></script>
<!--===============================================================================================-->
	<script src="Login_v18/vendor/select2/select2.min.js"></script>
<!--===============================================================================================-->
	<script src="Login_v18/vendor/daterangepicker/moment.min.js"></script>
	<script src="Login_v18/vendor/daterangepicker/daterangepicker.js"></script>
<!--===============================================================================================-->
	<script src="Login_v18/vendor/countdowntime/countdowntime.js"></script>
<!--===============================================================================================-->
	<script src="Login_v18/js/main.js"></script>

</body>
</html>