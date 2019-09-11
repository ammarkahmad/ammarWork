<?php
$title = 'password_reset';
require_once 'template/header.php';
require 'config/app.php';
require_once 'config/database.php';

if(isset($_SESSION['logged_in'])){
  header('location: index.php');
}

$errors = [];
$email = '';


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = mysqli_real_escape_string($mysqli, $_POST['email']);


    if (empty($email)) {
        array_push($errors, "Email is required");
    }


if(!count($errors)){
  $userExists = $mysqli->query("select id, email, name from users where email='$email' limit 1");

  if($userExists->num_rows){

    $userId = $userExists->fetch_assoc()['id'];

    $tokenExists = $mysqli->query("delete from Password_resets where user_id='$userId'");


    $token = bin2hex(random_bytes(16));

    $expires_at = date('Y-m-d  H:i:s' ,strtotime('+1 day')); // 2019-01-01  17:12:12

    $mysqli->query("insert into Password_resets (user_id, token, expires_at)
    values('$userId','$token','$expires_at');
    ");

    $changePasswordUrl = $config['app_url'].'change_password.php?token='.$token;

    $headers = 'MIME-Version: 1.0' . "\r\n";
    $headers .= 'Content-type: text/html; charset=UFT-8' . "\r\n";

    $headers .= 'From: '.$config['admin_email']."\r\n".
      'Reply-To:'.$config['admin_email']."\r\n".
      'X-Mailer: PHP/' . phpversion();

      $htmlMessage = '<html><body>';
      $htmlMessage .= '<p style="color:#ff0000;">'.$changePasswordUrl.'</p>';
      $htmlMessage .= '</body></html>';

      mail($email, 'Password reset link', $htmlMessage, $headers);

    }

    $_SESSION['success_message'] = 'Please check your email for password reset link';
    header('location: password_reset.php');

  }
}


?>

<div id="password_reset">
    <h4>Password reset</h4>
    <h5 class="text-info">Fill in your email to reset your password</h5>
    <hr>
    <?php include 'template\errors.php' ?>
   <form method="post" action="">
        <div class="form-group">
            <label for="email">Email address :</label>
            <input type="email" class="form-control" value="<?php echo $email ?>" id="email" placeholder="Enter email" name="email">
        </div>
        <div class="form-group ">
            <button type="submit" class="btn btn-primary">Request password reset link!</button>
        </div>

    </form>
</div>
<?php
include 'template/footer.php' ?>
