<?php
    session_start();
    if (isset($_SESSION['user_id'])) {
        header("location: http:/Home");
        exit();
    }
        
    require_once '../../functions.php';
    
    $error = '';
    $user = '';
    $pass = '';

    if (isset($_POST['user']) && isset($_POST['pass'])) {
        $user = trim($_POST['user']);
        $pass = trim($_POST['pass']);

        if($user == "") {
            $error = "Please enter your username";
        } else if ($pass == "") {
            $error = "Please enter your password";
        } else {
            $user = htmlspecialchars($user);
            $pass = htmlspecialchars($pass);
    
            $data = array('username' => $user, 'password' => $pass);
            $url = 'http://localhost/api/auth/login';
            
            $response = callApi($url, $data, "POST");
            
            if(isset($response['code']) && $response['code'] < 10) {
                if($response['code'] == 0) {
                    $_SESSION['user_id'] = $response['data']['id'];
                    header('Location: http://localhost/Home/user');
                    exit();
                }
                else $error = $response['message'];

            } else {
                $error = "There was an error while processing your request. Please try again later";
            }
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>User Login</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="http://localhost/public/assets/css/auth/login.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head>
<body>
<?php require_once('../../includes/header.php'); ?>



<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <h3 class="text-center text-secondary mt-5 mb-3">User Login</h3>
            <form method="post" action="" class="border rounded w-100 mb-5 mx-auto px-3 pt-3 bg-light" onsubmit="return validateInput()">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input value="<?= $user ?>" name="user" id="username" type="text" class="form-control" placeholder="Username">
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input value="<?= $pass ?>" name="pass" id="password" type="password" class="form-control" placeholder="Password">
                </div>
                
                <div class="form-group">
                    <?php
                        if (!empty($error)) {
                            echo "<div class='alert alert-danger'>$error</div>";
                        }
                    ?>
                    <button class="btn btn-success px-5">Login</button>
                </div>
                <div class="form-group">
                    <p>Don't have an account yet? <a href="http://localhost/auth/register">Register now</a>.</p>
                    <p>Forgot your password? <a href="http://localhost/auth/forgot">Reset your password</a>.</p>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Import footer -->
<?php // require_once('../includes/footer.php'); ?>
<!-- <script src="../../assets/js/auth/login.js"></script> -->
</body>
</html>
