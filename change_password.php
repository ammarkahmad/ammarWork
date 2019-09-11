<?php
$title = 'change password';
require_once 'template/header.php';
require 'config/app.php';
require_once 'config/database.php';

if(isset($_SESSION['logged_in'])){
  header('location: index.php');
}

if(!isset($_GET['token']) || !$_GET['token']){
  die('Token parameter is missing');
}

$now = date('Y-m-d   H:i:s');

$stmt = $mysqli->prepare("select * from password_resets where token = ? and expires_at > '$now' ");
$stmt->bind_param('s',$token);
$token = $_GET['token'];

$stmt->execute();

$result = $stmt->get_result();

if(!$result->num_rows){
  die('Token is not valid');
}
$errors = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
$password = mysqli_real_escape_string($mysqli,$_POST['password']);
$confirm_Password = mysqli_real_escape_string($mysqli,$_POST['password_confirmation']);

if (empty($password)) {
    array_push($errors, "password is required");
}
if (empty($confirm_Password)) {
    array_push($errors, "confirm Password is required");
}
if ($password != $confirm_Password){
     array_push($errors, " Password don't match");
}

if(!count($errors)){
 $hashed_password = password_hash($password, PASSWORD_DEFAULT);
 $userId = $result->fetch_assoc()['user_id'];

 $mysqli->query("update users set password = '$hashed_password' where id = $userId");

 $mysqli->query("delete from password_resets where user_id = '$userId'");

  $_SESSION['success_message'] =' Your password has been changed, please log in';

  header('location: login.php');
  die();

  }

}


?>

<div id="password_reset">
    <h4>Create a new password</h4>
    <hr>
    <?php include 'template\errors.php' ?>
   <form method="post" action="">
        <div class="form-group">
            <label for="password">New password :</label>
            <input type="password" class="form-control"  id="password" placeholder="Enter new password" name="password">
        </div>

        <div class="form-group">
            <label for="password_confirmation">Confirm new password :</label>
            <input type="password" class="form-control"  id="password_confirmation" placeholder="Confirm new password" name="password_confirmation">
        </div>

        <div class="form-group ">
            <button type="submit" class="btn btn-primary">change password!</button>
        </div>

    </form>
</div>
<?php
include 'template/footer.php' ?>
