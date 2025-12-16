<?php
$name = $email = $pass = $cpass = "";
$validation_failed = false;
$success_message = "";

if (isset($_POST['submit'])) {
    $name = $_POST['name'] ;
    $email = $_POST['email'];
    $pass = $_POST['password'];
    $cpass = $_POST['confirm_password'];

    // Validation
    if ($name == "") {
        echo "<p style='color:red;'>Name is required</p>";
        $validation_failed = true;
    }
    if ($email == "") {
        echo "<p style='color:red;'>Email is required</p>";
        $validation_failed = true;
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<p style='color:red;'>Invalid email</p>";
        $validation_failed = true;
    }
    if (strlen($pass) < 8) {
        echo "<p style='color:red;'>Password must be 8+ characters</p>"; // Fixed: changed 6+ to 8+ to match your condition
        $validation_failed = true;
    }
    if (!preg_match('/[!@#$%^&*(),.?":{}|<>]/', $pass)) {
        echo "<p style='color:red;'>Password must contain at least one special character</p>";
        $validation_failed = true;
    }
    if ($pass != $cpass) {
        echo "<p style='color:red;'>Passwords do not match</p>";
        $validation_failed = true;
    }

    // Check if email already exists (added this validation)
    if ($email != "" && filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $users = file_exists("users.json")
            ? json_decode(file_get_contents("users.json"), true)
            : [];
        if (!is_array($users)) $users = [];
        
        foreach ($users as $user) {
            if (isset($user['email']) && $user['email'] === $email) {
                echo "<p style='color:red;'>Email already registered</p>";
                $validation_failed = true;
                break;
            }
        }
    }

    if (!$validation_failed) {
        // Register the user
        $users = file_exists("users.json")
            ? json_decode(file_get_contents("users.json"), true)
            : [];
        if (!is_array($users)) $users = [];

        $hashed_pass = password_hash($pass, PASSWORD_DEFAULT);
        $new_user = [
            "name" => $name,
            "email" => $email,
            "password" => $hashed_pass
        ];
        $users[] = $new_user;
        file_put_contents("users.json", json_encode($users, JSON_PRETTY_PRINT));

        // Show success message
        $success_message = "<div style='color:green; font-weight:bold; margin:15px 0; text-align:center;'>Registration Successful!</div>";

        // Clear the form after success
        $name = $email = $pass = $cpass = "";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Form</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <h2>Registration Form</h2>

    <!-- Success message appears here -->
    <?php echo $success_message; ?>

    <form method="post">
        Name:<br>
        <input type="text" name="name" value="<?php echo htmlspecialchars($name); ?>" required /><br><br>

        Email:<br>
        <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required /><br><br>

        Password:<br>
        <input type="password" name="password" required /><br><br>

        Confirm Password:<br>
        <input type="password" name="confirm_password" required /><br><br>

        <button type="submit" name="submit">Submit</button>
    </form>
</div>
</body>
</html>