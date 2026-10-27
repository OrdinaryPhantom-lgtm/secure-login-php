<?php
session_start();
include('db.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$id = $_SESSION['user_id'];
$error = "";

$sql = "SELECT * FROM users WHERE ID = ?";
$old = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($old, 'i', $id);
mysqli_stmt_execute($old);
$result = mysqli_stmt_get_result($old);
$user = mysqli_fetch_assoc($result);

if (isset($_POST['update'])) {
    $name = trim($_POST['name']);

    if ($name === "") {
        $error = "Name can't be empty";
    } else {
        $mysql = "UPDATE users SET name = ? WHERE id = ?";
        $update = mysqli_prepare($conn, $mysql);
        mysqli_stmt_bind_param($update, 'si', $name, $id);
        if (mysqli_stmt_execute($update)) {
            header("Location: dashboard.php");
            exit;
        } else {
            $error = "The name could not be updated";
        }
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit name</title>
  <link rel="stylesheet" href="style.css">
</head>
<body class="auth-page">
  <main class="auth-card">
    <h1>Edit your name</h1>
    <p class="subtitle">This is the name shown on your dashboard.</p>

    <!-- PHP: show an error here, only when there is one -->
     <?php if ($error !== ""): ?>
     <div class="alert error"><?php echo htmlspecialchars($error)?>div>
     <?php endif ?>

    <form action="edit-name.php" method="POST">
      <label for="name">Full name</label>
      <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required> <!-- PHP: the current name goes in value -->
      <button type="submit" name="update">Save</button>
    </form>

    <p class="switch"><a href="dashboard.php">Back to dashboard</a></p>
  </main>
</body>
</html>

