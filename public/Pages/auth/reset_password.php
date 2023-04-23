<?php
    require_once '../../functions.php';
    
    session_start();
    if (isset($_SESSION["user_id"])) {
        header("Location: http:/Home");
        exit();
    }

    $error = '';
    $email = '';
    $pass = '';
    $pass_confirm = '';

    $display_email = filter_input(INPUT_GET,'email',FILTER_SANITIZE_EMAIL);

    if(isset($_GET['email']) && $_GET['token']) {
        $email = $_GET['email'];
        $token = $_GET['token'];
        if(filter_var($email,FILTER_VALIDATE_EMAIL) === false) {
            $error = 'Invalid Email address';
        }
        else if(strlen($token) != 32) {
            $error = 'Invalid Token';
        }
        if (isset($_POST['email']) && isset($_POST['pass']) && isset($_POST['pass-confirm'])) {
            $email = $_POST['email'];+
            $pass = $_POST['pass'];
            $pass_confirm = $_POST['pass-confirm'];

        if (empty($email)) {
            $error = 'Please enter your email';
        }
        else if (filter_var($email, FILTER_VALIDATE_EMAIL) == false) {
            $error = 'This is not a valid email address';
        }
        else if (empty($pass)) {
            $error = 'Please enter your password';
        }
        else if (strlen($pass) < 6) {
            $error = 'Password must have at least 6 characters';
        }
        else if ($pass != $pass_confirm) {
            $error = 'Password does not match';
        }
        else {
            $url = 'http://localhost/api/auth/reset';
            $data = array(
                'email' => $email,
                'password' => $pass,
                'token' => $token
            );
            $response = callApi($url, $data, 'POST');
            if(isset($response['code']) && $response['code'] < 10) {
                if($response['code'] != 0) {
                    $error = $response['message'];
                }
                else {
                    $success =  $response['message'];
                }
            } else {
                $error = "There was an error while processing your request. Please try again later";
            }
        }
    }
    }
    else{
        $error = 'Invalid Email address or Token';
    }

?>
<DOCTYPE html>
<html lang="en">
<head>
    <title>Reset user password</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head>
<body>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <h3 class="text-center text-secondary mt-5 mb-3">Reset Password</h3>
            <form novalidate method="post" action="" class="border rounded w-100 mb-5 mx-auto px-3 pt-3 bg-light">
                <div class="form-group">
                    <?php
                        if (!empty($error)) { 
                        ?> 
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input readonly value="<?= $display_email ?>" name="email" id="email" type="text" class="form-control" placeholder="Email address">
                                </div>
                                    <div class="form-group">
                                    <label for="pass">Password</label>
                                    <input  value="<?= $pass?>" name="pass" required class="form-control" type="password" placeholder="Password" id="pass">
                                    <div class="invalid-feedback">Password is not valid.</div>
                                </div>
                                    <div class="form-group">
                                    <label for="pass_confirm">Confirm Password</label>
                                    <input value="<?= $pass_confirm?>" name="pass-confirm" required class="form-control" type="password" placeholder="Confirm Password" id="pass_confirm">
                                    <div class="invalid-feedback">Password is not valid.</div>
                                </div> 
                                <div class='alert alert-danger'><?=$error?></div>
                                <button class="btn btn-success px-5">Change password</button>
                        
                        <?php
                        }
                        else if(!isset($success)) {
                            ?> 
                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input readonly value="<?= $display_email ?>" name="email" id="email" type="text" class="form-control" placeholder="Email address">
                                    </div>
                                        <div class="form-group">
                                        <label for="pass">Password</label>
                                        <input  value="<?= $pass?>" name="pass" required class="form-control" type="password" placeholder="Password" id="pass">
                                        <div class="invalid-feedback">Password is not valid.</div>
                                    </div>
                                        <div class="form-group">
                                        <label for="pass_confirm">Confirm Password</label>
                                        <input value="<?= $pass_confirm?>" name="pass-confirm" required class="form-control" type="password" placeholder="Confirm Password" id="pass_confirm">
                                        <div class="invalid-feedback">Password is not valid.</div>
                                    </div> 
                                    <button class="btn btn-success px-5">Change password</button>
                            <?php
                        }
                        else
                        {
                            echo "<div class='alert alert-success'>$success</div>";
                        }
                    ?>
                    
                </div>
            </form>

        </div>
    </div>
</div>

</body>
</html>
