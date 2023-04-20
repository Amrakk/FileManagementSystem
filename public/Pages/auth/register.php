<!-- SIGNUP PAGE

    functions:
    -   signup

-->

<?php
    session_start();
    if(isset($_SESSION['user_id'])) {
        header('Location: http:/Home');
        exit();
    }

    require_once '../../functions.php';
    
    $error = '';
    $first_name = $_POST['first_name'] ?? '';
    $last_name = $_POST['last_name'] ?? '';
    $email = $_POST['email'] ?? '';
    $user = $_POST['user'] ?? '';
    $pass = $_POST['pass'] ?? '';
    $pass_confirm = $_POST['pass_confirm'] ?? '';

   
    if(!empty($first_name) && !empty($last_name) && !empty($email) && !empty($user) && !empty($pass) && !empty($pass_confirm)) {
        $url = 'http://localhost/api/auth/register';
        $data = array(
            'first_name' => $first_name,
            'last_name' => $last_name,
            'email' => $email,
            'username' => $user,
            'password' => $pass,
            'password_confirm' => $pass_confirm
        );
        $response = callApi($url, $data, 'POST');

        if(isset($response['code']) || $response['code'] >= 10) {
            if($response['code'] == 0) {
                $_SESSION['user_id'] = $response['data']['id'];
                header('Location: http://localhost/Home');
                exit();
            }
            else $error = $response['message'];

        } else {
            $error = "There was an error while processing your request. Please try again later";
        }
    }

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Register an account</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>
    
    <style>
        .bg {
            background: #eceb7b;
        }
    </style>
</head>
<body>
<?php require_once('../../includes/header.php'); ?>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-5 col-lg-6 col-md-8 border my-5 p-4 rounded mx-3">
                <h3 class="text-center text-secondary mt-2 mb-3 mb-3">Create a new account</h3>
                <form method="post" action="" novalidate>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="first_name">First name</label>
                            <input oninput="validateInput()" value="<?= $first_name?>" name="first_name" required class="form-control" type="text" placeholder="First name" id="first_name">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="last_name">Last name</label>
                            <input oninput="validateInput()" value="<?= $last_name?>" name="last_name" required class="form-control" type="text" placeholder="Last name" id="last_name">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input oninput="validateInput()" value="<?= $email?>" name="email" required class="form-control" type="email" placeholder="Email" id="email">
                    </div>
                    <div class="form-group">
                        <label for="user">Username</label>
                        <input oninput="validateInput()" value="<?= $user?>" name="user" required class="form-control" type="text" placeholder="Username" id="user">
                    </div>
                    <div class="form-group">
                        <label for="pass">Password</label>
                        <input oninput="validateInput()" value="<?= $pass?>" name="pass" required class="form-control" type="password" placeholder="Password" id="pass">
                    </div>
                    <div class="form-group">
                        <label for="pass_confirm">Confirm Password</label>
                        <input oninput="validateInput()" value="<?= $pass_confirm?>" name="pass_confirm" required class="form-control" type="password" placeholder="Confirm Password" id="pass_confirm">
                    </div>

                    <div class="form-group">
                        <?php
                            if (!empty($error)) {
                                echo "<div class='alert alert-danger' id='message'>$error</div>";
                            }
                        ?>
                        <div id="message"></div>
                        <button type="submit" class="btn btn-success px-5 mt-3 mr-2">Register</button>
                        <button type="reset" class="btn btn-outline-success px-5 mt-3">Reset</button>
                    </div>
                    <div class="form-group">
                        <p>Already have an account? <a href="login.php">Login</a> now.</p>
                    </div>
                </form>

            </div>
        </div>

    </div>

    <script src="http://localhost/public/assets/js/auth/register.js"></script>
</body>
</html>

