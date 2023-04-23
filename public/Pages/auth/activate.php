<?php
    session_start();
    if (isset($_SESSION["user_id"])) {
        header("Location: http:/Home");
        exit();
    }

    require_once '../../functions.php';
    $error = '';
    $email = $_GET["email"] ?? "";
    $token = $_GET["token"] ?? "";

    if(!empty($email) && !empty($token)) {
        $url = "http://localhost/api/auth/activate";
        $data = array(
            "email" => $email,
            "token" => $token
        );
        $response = callApi($url, $data, "PUT");
        if(isset($response['code']) && $response['code'] < 10) {
            if($response['code'] != 0) {
                $error = $response['message'];
            }

        } else {
            $error = "There was an error while processing your request. Please try again later";
        }
    }
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Activate Account</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" />
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css" integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU" crossorigin="anonymous" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head>

<body>
  <div class="container">
    <div class="row">
        <?php
            if(empty($error)) {
        ?>
                    <div class="col-md-6 mt-5 mx-auto p-3 border rounded">
                        <h4>Account Activation</h4>
                        <p class="text-success">Congratulations! Your account has been activated.</p>
                        <p>Click <a href="login.php">here</a> to login and manage your account information.</p>
                        <a class="btn btn-success px-5" href="login.php">Login</a>
                    </div>
        <?php
            }else{
        ?>
                    <div class="col-md-6 mt-5 mx-auto p-3 border rounded">
                        <h4>Account Activation</h4>
                        <p class="text-danger"><?= $error?>.</p>
                        <p>Click <a href="login.php">here</a> to login.</p>
                        <a class="btn btn-success px-5" href="login.php">Login</a>
                    </div>
        <?php
            }
        ?>
  </div>
</body>

</html>