<?php
$title = 'Register';
require_once 'template/header.php';
require 'config/app.php';
require_once 'config/database.php';

$errors = [];


$email=$name='';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = mysqli_real_escape_string($mysqli, $_POST['email']);
    $name = mysqli_real_escape_string($mysqli,$_POST['name']);
    $password = mysqli_real_escape_string($mysqli,$_POST['password']);
    $confirm_Password = mysqli_real_escape_string($mysqli,$_POST['confirmPassword']);


    if (empty($email)) {
        array_push($errors, "Email is required");
    }
    if (empty($name)) {
        array_push($errors, "Name is required");
    }
    if (empty($password)) {
        array_push($errors, "password is required");
    }
    if (empty($confirm_Password)) {
        array_push($errors, "confirm Password is required");
    }
    if ($password != $confirm_Password){
         array_push($errors, " Password don't match");
    }
    if (!count($errors)){
        $userExists=$mysqli->query("SELECT id, email FROM users WHERE email ='$email' LIMIT 1");
        if ($userExists->num_rows){
             array_push($errors, "Email already register");
        }
    }

    //create a new user
    if(!count($errors)){
        $password= password_hash($password, PASSWORD_DEFAULT);
        $query="INSERT INTO users(email,name,password)VALUES('$email','$name','$password')";
        $mysqli->query($query);
        $_SESSION['logged_in']=true;
        $_SESSION['user_id']=$mysqli->insert_id;
       // print_r($_SESSION['logged_in']);//يطبع واحد لو صح
       // print_r($_SESSION['user_id']);//يطبع الايدي
        $_SESSION['user_name']=$name;
        $_SESSION['success_message']=" Welcom to our website,$name ";
         header('location:index.php');
    }

}
?>

<div id="register">
    <h4>Welcome to our site</h4>
    <h5 class="text-info">Please fill in the fields below to register</h5>
    <hr>
    <?php include 'template\errors.php' ?>
   <form method="post" action="">
        <div class="form-group">
            <label for="email">Email address :</label>
            <input type="email" class="form-control" value="<?php echo $email ?>" id="email" placeholder="Enter email" name="email">
        </div>
        <div class="form-group">
            <label for="name">Name:</label>
            <input type="text" class="form-control" value="<?php echo $name ?>" id="name" placeholder="Enter Name" name="name">
        </div>
        <div class="form-group">
            <label for="Password">Password:</label>
            <input type="password" class="form-control" id="Password" placeholder="Password" name="password">
        </div>
        <div class="form-group">
            <label for="confirmPassword">confirm Password :</label>
            <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" placeholder="confirm your Password">
        </div>
        <div class="form-group ">
            <button type="submit" class="btn btn-primary">Regestir!</button>
            <a href="login.php">Already have an account? login hear</a>
        </div>

    </form>
</div>

<?php include 'template/footer.php' ?>
