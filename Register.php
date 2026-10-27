<?php
include('db.php');

$error = "";
$name = "";
$email = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        $error = "Passwords do not match";
    } elseif (strlen($password) < 8) {
        $error = "Password must be at least 8 characters long";
    } else {
        // Is this email already used?
        $sql = "SELECT id FROM users WHERE email = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) > 0) {
            $error = "This email is already registered";
        } else {
            // Scramble the password, then save the user
            $hashed = password_hash($password, PASSWORD_DEFAULT);

            $sql = "INSERT INTO users (name, email, password) VALUES (?, ?, ?)";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "sss", $name, $email, $hashed);
            mysqli_stmt_execute($stmt);

            header("Location: login.php?registered=1");
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register</title>
  <link rel="stylesheet" href="style.css">
</head>
<body class="auth-page">
  <main class="auth-card">
    <h1>Create account</h1>
    <p class="subtitle">It takes less than a minute.</p>

    <?php if ($error !== ""): ?>
      <div class="alert error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form action="register.php" method="POST">
      <label for="name">Full name</label>
      <input type="text" id="name" name="name" placeholder="Riya Sharma" value="<?php echo htmlspecialchars($name); ?>" required>

      <label for="email">Email</label>
      <input type="email" id="email" name="email" placeholder="you@example.com" value="<?php echo htmlspecialchars($email); ?>" required>

      <label for="password">Password</label>
      <input type="password" id="password" name="password" placeholder="At least 8 characters" minlength="8" required>

      <label for="confirm_password">Confirm password</label>
      <input type="password" id="confirm_password" name="confirm_password" placeholder="Type it again" minlength="8" required>

      <button type="submit">Create account</button>
    </form>

    <p class="switch">Already have an account? <a href="login.php">Log in</a></p>
  </main>
</body>
</html>