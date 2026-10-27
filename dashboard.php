<?php
session_start();
include('db.php');

// Not logged in? Send them to the login page
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Get this user's details from the database
$sql = "SELECT id, name, email, created_at FROM users WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $_SESSION['user_id']);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);

// The user was deleted from the database? Log them out
if (!$user) {
    session_destroy();
    header("Location: login.php");
    exit;
}

$firstName = explode(" ", $user['name'])[0];
$initial = strtoupper(mb_substr($user['name'], 0, 1));
$since = date("d M Y", strtotime($user['created_at']));
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <header class="topbar">
    <div class="brand"><span class="dot"></span> MyApp</div>
    <a href="logout.php" class="logout">Log out</a>
  </header>

  <main class="container">
    <section class="welcome">
      <div class="avatar"><?php echo htmlspecialchars($initial); ?></div>
      <div>
        <h1>Welcome back, <?php echo htmlspecialchars($firstName); ?></h1>
        <p>Here is a quick look at your account.</p>
      </div>
    </section>

    <section class="stats">
      <div class="stat">
        <span class="stat-label">Member since</span>
        <span class="stat-value"><?php echo htmlspecialchars($since); ?></span>
      </div>
      <div class="stat">
        <span class="stat-label">Status</span>
        <span class="stat-value">Active</span>
      </div>
      <div class="stat">
        <span class="stat-label">User ID</span>
        <span class="stat-value">#<?php echo (int) $user['id']; ?></span>
      </div>
    </section>

    <section class="panel">
      <h2>Account details</h2>
      <dl class="details">
        <div class="row"><dt>Name</dt><dd><?php echo htmlspecialchars($user['name']); ?></dd></div>
        <div class="row"><dt>Email</dt><dd><?php echo htmlspecialchars($user['email']); ?></dd></div>
        <p><a href="edit-name.php">Edit name</a></p>
      </dl>
    </section>
  </main>
</body>
</html>