<?php
session_start();
include('db.php');

if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}

$error = "";
$email = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Find the user with this email
    $sql = "SELECT id, password FROM users WHERE email = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);

    // Check the typed password against the saved scramble
    if ($user && password_verify($password, $user['password'])) {
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'];
        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Wrong email or password";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Log in</title>
  <link rel="stylesheet" href="style.css">
</head>
<body class="auth-page">
  <main class="auth-card">
    <h1>Welcome back</h1>
    <p class="subtitle">Log in to your account.</p>

    <?php if (isset($_GET['registered']) && $error === ""): ?>
      <div class="alert success">Account created. You can log in now.</div>
    <?php endif; ?>

    <?php if ($error !== ""): ?>
      <div class="alert error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form action="login.php" method="POST">
      <label for="email">Email</label>
      <input type="email" id="email" name="email" placeholder="you@example.com" value="<?php echo htmlspecialchars($email); ?>" required>

      <label for="password">Password</label>
      <input type="password" id="password" name="password" placeholder="Your password" required>

      <button type="submit">Log in</button>
    </form>

    <p class="switch">New here? <a href="register.php">Create an account</a></p>
  </main>
</body>
</html>