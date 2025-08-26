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
        if (!preg_match("/^[a-zA-Z0-9_]*$/",$username)) {
            $usernameErr = "Only letters, numbers, and underscores allowed";
        }
    }

    if (empty($_POST["password"])) {
        $passwordErr = "Password is required";
    } else {
        $password = test_input($_POST["password"]);
        if (strlen($password) < 8) {
            $passwordErr = "Password must be at least 8 characters";
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
    $data = trim($data);
    return $data;
}
?>

<!DOCTYPE html>
<head>
    <title>Edify - Registration</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<header>
    <div class="header-center"><a href="index.php" style="text-decoration:none;color:inherit;"><h2>Edify</h2></a></div>
    <div class="header-right">
        <a href="login.php" class="btn">Login</a>
    </div>
</header>

<main class="container">
    <h2>Register</h2>

    <?php if($successMsg) echo "<p style='color:green'>$successMsg</p>"; ?>

    <form method="POST" class="form">
        Username:<br>
        <input type="text" name="username" value="<?php echo $username; ?>">
        <span style="color:red">* <?php echo $usernameErr; ?></span>
        <br><br>

        Password:<br>
        <input type="password" name="password" value="<?php echo $password; ?>">
        <span style="color:red">* <?php echo $passwordErr; ?></span>
        <br><br>

        Role:<br>
        <select name="role">
            <option value="">Select Role</option>
            <option value="teacher" <?php if($role=="teacher") echo "selected"; ?>>Teacher</option>
            <option value="student" <?php if($role=="student") echo "selected"; ?>>Student</option>
        </select>
        <span style="color:red">* <?php echo $roleErr; ?></span>
        <br><br>

        <input type="submit" class="btn btn-primary" value="Register">
    </form>
</main>

<script src="assets/js/script.js"></script>
</body>
</html>
