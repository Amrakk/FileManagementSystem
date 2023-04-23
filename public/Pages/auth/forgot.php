<?php
    session_start();
    if(isset($_SESSION['user_id'])) {
        header('Location: http:/Home');
        exit();
    }
 
    require_once '../../functions.php';
    $error = '';
    $email = $_POST['email'] ?? '';
    if(empty($email)){
        $error = "Please enter your email address";
    }
    else if (!filter_var($email, FILTER_VALIDATE_EMAIL)){
        $error = "Please enter a valid email address";
    }else{
        $url = 'http://localhost/api/auth/forgot';
        $data = array(
            'email' => $email
        );
        $response = callApi($url, $data, 'POST');
        if(isset($response['code']) && $response['code'] < 10) {
            if($response['code'] != 0) {
                $error = $response['message'];
            }else
            $error = $response['message'];

        } else {
            $error = "There was an error while processing your request. Please try again later";
        }
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
            <form method="post" action="" class="border rounded w-100 mb-5 mx-auto px-3 pt-3 bg-light">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input name="email" id="email" type="text" class="form-control" placeholder="Email address">
                </div>
                <div class="form-group">
                    <p>Enter your email address to continue.</p>
                </div>
                <div class="form-group">
                    <?php
                        if (!empty($error)) {
                            echo "<div class='alert alert-danger'>$error</div>";
                        }
                        else{
                            echo "<div class='alert alert-success'>$error</div>";
                        }
                    ?>
                    <button class="btn btn-success px-5">Reset password</button>
                </div>
            </form>

        </div>
    </div>
</div>

</body>
</html>