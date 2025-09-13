<?php
session_start();
include("config/db.php");

$username = $password = $role = "";
$usernameErr = $passwordErr = $roleErr = "";
$successMsg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (empty($_POST["username"])) {
        $usernameErr = "Username is required";
    } else {
        $username = test_input($_POST["username"]);
        if (!preg_match('/^[A-Za-z0-9._ ]+$/', $username)) {
            $usernameErr = "Only letters, numbers, underscores and dots allowed";
        }
    }

    if (empty($_POST["password"])) {
        $passwordErr = "Password is required";
    } else {
        $password = test_input($_POST["password"]);
        if (strlen($password) < 6 || !preg_match('/^[A-Za-z0-9]+$/', $password)) {
            $passwordErr = "Password must be at least 6 characters and contain only letters and numbers";
        }
    }

    if (empty($_POST["role"])) {
        $roleErr = "Select a role";
    } else {
        $role = $_POST["role"];
        if (!in_array($role, ["teacher","student"])) {
            $roleErr = "Invalid role";
        }
    }

    if (empty($usernameErr) && empty($passwordErr) && empty($roleErr)) {
        $stmt = $conn->prepare("SELECT id FROM users WHERE username=?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $usernameErr = "Username already taken";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (username,password,role) VALUES (?,?,?)");
            $stmt->bind_param("sss",$username,$hashed_password,$role);
            if ($stmt->execute()) {
                $successMsg = "Registration successful! <a href='login.php'>Login here</a>";
                $username = $password = $role = "";
            } else {
                $successMsg = "Registration failed. Try again.";
            }
        }
        $stmt->close();
    }
}

function test_input($data){
    return trim($data);
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edify - Registration</title>
    <link rel="stylesheet" href="assets/css/reglog.css">
</head>
<body>

<header>
    <div><a href="index.php"><h2>Edify</h2></a></div>
    <div><a href="login.php" class="btn">Login</a></div>
</header>

<main class="container">
    <h2>Register</h2>

    <?php if($successMsg) echo "<p class='success'>$successMsg</p>"; ?>

    <form method="POST" class="form">
        <label>Username:</label><br>
        <input type="text" name="username" value="<?php echo $username; ?>">
        <span>* <?php echo $usernameErr; ?></span>
        <br><br>

        <label>Password:</label><br>
        <input type="password" name="password" value="<?php echo $password; ?>">
        <span>* <?php echo $passwordErr; ?></span>
        <br><br>

        <label>Role:</label><br>
        <select name="role">
            <option value="">Select Role</option>
            <option value="teacher" <?php if($role=="teacher") echo "selected"; ?>>Teacher</option>
            <option value="student" <?php if($role=="student") echo "selected"; ?>>Student</option>
        </select>
        <span>* <?php echo $roleErr; ?></span>
        <br><br>

        <input type="submit" class="btn" value="Register">
    </form>
</main>

</body>
</html>
