<?php
$title = 'Register';
require_once 'template/header.php';
require 'config/app.php';
require_once 'config/database.php';

if(isset($_SESSION['logged_in'])){
  header('location: index.php');
}

$errors = [];
$email='';


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = mysqli_real_escape_string($mysqli, $_POST['email']);
    $password = mysqli_real_escape_string($mysqli,$_POST['password']);


    if (empty($email)) {
        array_push($errors, "Email is required");
    }

    if (empty($password)) {
        array_push($errors, "password is required");
    }

if(!count($errors)){
  $userExists = $mysqli->query("select id, email, password, name, role from users where email='$email' limit 1");

  if(!$userExists->num_rows){

    array_push($errors,"Your email, $email does not exists in our records");

  }else {
    $foundUser = $userExists->fetch_assoc();

   if(password_verify($password, $foundUser['password'])){

         $_SESSION['logged_in']=true;
         $_SESSION['user_id']=$foundUser['id'];
         $_SESSION['user_name']=$foundUser['name'];
         $_SESSION['user_role']= $foundUser['role'];

        if($foundUser['role'] == 'admin'){
          header('location:admin');

        }else {
          $_SESSION['success_message']=" Welcom back,$foundUser[name] ";

           header('location:index.php');
        }


   }else {

     array_push($errors, 'Wrong credentials');

   }
  }
}

    //create a new user
    // if(!count($errors)){
    //     $password= password_hash($password, PASSWORD_DEFAULT);
    //     $query="INSERT INTO users(email,name,password)VALUES('$email','$name','$password')";
    //     $mysqli->query($query);
    //     $_SESSION['logged_in']=true;
    //     $_SESSION['user_id']=$mysqli->insert_id;
    //    // print_r($_SESSION['logged_in']);//يطبع واحد لو صح
    //    // print_r($_SESSION['user_id']);//يطبع الايدي
    //     $_SESSION['user_name']=$name;
    //     $_SESSION['success_message']=" Welcom to our sits,$name ";
    //      header('location:index.php');
//     }
//
 }
?>

<div id="login">
    <h4>Welcome back</h4>
    <h5 class="text-info">Please fill in the fields below to login</h5>
    <hr>
    <?php include 'template\errors.php' ?>
   <form method="post" action="">
        <div class="form-group">
            <label for="email">Email address :</label>
            <input type="email" class="form-control" value="<?php echo $email ?>" id="email" placeholder="Enter email" name="email">
        </div>

        <div class="form-group">
            <label for="Password">Password:</label>
            <input type="password" class="form-control" id="Password" placeholder="Password" name="password">
        </div>

        <div class="form-group ">
            <button type="submit" class="btn btn-primary">login!</button>
            <a href="password_reset.php">Forgot your password?</a>
        </div>

    </form>
</div>
<?php
 include 'template/footer.php' ?>
