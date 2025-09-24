<?php
// profile.php - Basic profile management (bonus, as per objective)
session_start();
include 'db.php';
 
if (!isset($_SESSION['user_id'])) {
    echo "<script>window.location.href='login.php';</script>";
    exit;
}
 
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
 
if ($_POST) {
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $stmt = $pdo->prepare("UPDATE users SET phone = ?, address = ? WHERE id = ?");
    $stmt->execute([$phone, $address, $user_id]);
    $success = "Profile updated!";
    // Refresh user data
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - DreamZillow</title>
    <style>
        /* Internal CSS - Profile form */
        body { font-family: 'Segoe UI', sans-serif; background: #f8f9fa; padding: 2rem; }
        .form-container { background: white; padding: 2rem; border-radius: 10px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); max-width: 500px; margin: 0 auto; }
        h2 { text-align: center; margin-bottom: 1rem; color: #333; }
        input, textarea { width: 100%; padding: 0.8rem; margin-bottom: 1rem; border: 1px solid #ddd; border-radius: 5px; }
        button { width: 100%; padding: 0.8rem; background: #ff6b35; color: white; border: none; border-radius: 5px; cursor: pointer; }
        .success { color: green; text-align: center; margin-bottom: 1rem; }
        .link a { color: #ff6b35; text-decoration: none; display: block; text-align: center; margin-top: 1rem; }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Profile</h2>
        <?php if (isset($success)): ?><div class="success"><?= $success ?></div><?php endif; ?>
        <form method="POST">
            <input type="text" value="<?= htmlspecialchars($user['username']) ?>" disabled>
            <input type="email" value="<?= htmlspecialchars($user['email']) ?>" disabled>
            <input type="tel" name="phone" value="<?= htmlspecialchars($user['phone']) ?>" placeholder="Phone">
            <textarea name="address" placeholder="Address"><?= htmlspecialchars($user['address']) ?></textarea>
            <button type="submit">Update Profile</button>
        </form>
        <a href="dashboard.php">Back to Dashboard</a>
    </div>
    <script>
        // JS if needed
    </script>
</body>
</html>
