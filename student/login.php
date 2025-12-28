<?php
session_start();
require_once "../config/db.php";


$error = "";
$success = "";
$email = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // VALIDATIONS
    if ($email == "") {
        $error = "Please enter your email";
    } elseif ($password == "") {
        $error = "Please enter your password";
    } else {

        $sql = "SELECT * FROM account WHERE email='$email'";
        $result = mysqli_query($conn, $sql);

        if (mysqli_num_rows($result) == 1) {
            $row = mysqli_fetch_assoc($result);

            if ($password === $row['password']) {
                $_SESSION['student_id'] = $row['id'];
                $_SESSION['student_name'] = $row['name'];
                 $_SESSION['student_email'] = $row['email']; // ✅ Add this line

                $success = "Login successful! Redirecting...";
                header("refresh:2;url=Dashboard/index.php");
            } else {
                $error = "Invalid email or password";
            }
         } 
        else {
            $error = "Invalid email or password";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Student Login</title>
            <link rel="stylesheet" href="Styles/login.css">
</head>
<body>

<form method="POST">
    <h3>Student Login</h3>

    <?php if($error != ""){ ?>
        <div class="error"><?php echo $error; ?></div>
    <?php } ?>

    <?php if($success != ""){ ?>
        <div class="success"><?php echo $success; ?></div>
    <?php } ?>

    <input type="email" name="email" placeholder="Email" value="<?php echo isset($email) ? $email : ''; ?>">
    <input type="password" name="password" placeholder="Password">
    <input type="submit" value="Login">
</form>

</body>
</html>
