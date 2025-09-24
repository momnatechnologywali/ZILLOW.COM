<?php
// property_detail.php - Property details and inquiry
session_start();
include 'db.php';
 
if (!isset($_GET['id'])) {
    echo "<script>window.location.href='index.php';</script>";
    exit;
}
 
$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM properties p JOIN users u ON p.user_id = u.id WHERE p.id = ? AND p.status = 'approved'");
$stmt->execute([$id]);
$property = $stmt->fetch(PDO::FETCH_ASSOC);
 
if (!$property) {
    echo "<script>window.location.href='index.php';</script>";
    exit;
}
 
// Handle inquiry
if ($_POST && isset($_SESSION['user_id'])) {
    $message = $_POST['message'];
    $user_id = $_SESSION['user_id'];
    $stmt = $pdo->prepare("INSERT INTO inquiries (property_id, user_id, message) VALUES (?, ?, ?)");
    $stmt->execute([$id, $user_id, $message]);
    $success = "Inquiry sent successfully!";
}
 
// Check if saved
$saved = false;
if (isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("SELECT * FROM saved_properties WHERE user_id = ? AND property_id = ?");
    $stmt->execute([$_SESSION['user_id'], $id]);
    $saved = $stmt->rowCount() > 0;
}
 
if (isset($_POST['toggle_save'])) {
    if ($saved) {
        $stmt = $pdo->prepare("DELETE FROM saved_properties WHERE user_id = ? AND property_id = ?");
    } else {
        $stmt = $pdo->prepare("INSERT INTO saved_properties (user_id, property_id) VALUES (?, ?)");
    }
    $stmt->execute([$_SESSION['user_id'], $id]);
    $saved = !$saved;
    echo "<script>window.location.reload();</script>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($property['title']) ?> - DreamZillow</title>
    <style>
        /* Internal CSS - Detail page */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background: #f8f9fa; }
        header { background: linear-gradient(135deg, #0f0f23 0%, #1a1a2e 100%); color: white; padding: 1rem 0; position: fixed; width: 100%; top: 0; z-index: 1000; }
        nav { display: flex; justify-content: space-between; align-items: center; max-width: 1200px; margin: 0 auto; padding: 0 2rem; }
        .logo { font-size: 1.5rem; font-weight: bold; }
        .container { max-width: 1200px; margin: 0 auto; padding: 2rem; margin-top: 70px; display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; }
        .images { grid-column: span 2; }
        .images img { width: 100%; height: 400px; object-fit: cover; border-radius: 10px; }
        .details { background: white; padding: 2rem; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        .price { font-size: 2rem; font-weight: bold; color: #ff6b35; margin-bottom: 1rem; }
        .location { color: #666; margin-bottom: 1rem; }
        .amenities { list-style: none; }
        .amenities li { margin-bottom: 0.5rem; }
        .inquiry-form { background: white; padding: 2rem; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        textarea { width: 100%; padding: 0.8rem; margin-bottom: 1rem; border: 1px solid #ddd; border-radius: 5px; }
        button { padding: 0.8rem 1.5rem; background: #ff6b35; color: white; border: none; border-radius: 5px; cursor: pointer; margin-right: 1rem; }
        .success { color: green; margin-bottom: 1rem; }
        .save-btn { background: <?= $saved ? '#666' : '#ff6b35' ?>; }
        @media (max-width: 768px) { .container { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
    <header>
        <nav>
            <div class="logo">DreamZillow</div>
            <ul style="display: flex; list-style: none;">
                <li style="margin-left: 2rem;"><a href="index.php" style="color: white; text-decoration: none;">Home</a></li>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li style="margin-left: 2rem;"><a href="dashboard.php" style="color: white; text-decoration: none;">Dashboard</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>
 
    <div class="container">
        <div class="images">
            <img src="<?= $property['images'][0] ?? 'https://via.placeholder.com/800x400?text=Property' ?>" alt="<?= $property['title'] ?>">
        </div>
        <div class="details">
            <h1><?= htmlspecialchars($property['title']) ?></h1>
            <div class="price">$<?= number_format($property['price'], 2) ?></div>
            <div class="location"><?= htmlspecialchars($property['location']) ?></div>
            <p><?= nl2br(htmlspecialchars($property['description'])) ?></p>
            <p><strong>Type:</strong> <?= ucfirst($property['type']) ?></p>
            <p><strong>Bedrooms:</strong> <?= $property['bedrooms'] ?></p>
            <p><strong>Bathrooms:</strong> <?= $property['bathrooms'] ?></p>
            <p><strong>Sq Ft:</strong> <?= number_format($property['square_feet']) ?></p>
            <ul class="amenities">
                <?php foreach (json_decode($property['amenities'], true) ?? [] as $amenity): ?>
                    <li><?= htmlspecialchars($amenity) ?></li>
                <?php endforeach; ?>
            </ul>
            <?php if (isset($_SESSION['user_id'])): ?>
                <form method="POST" style="margin-top: 1rem;">
                    <button name="toggle_save" type="submit" class="save-btn"><?= $saved ? 'Unsave' : 'Save' ?></button>
                </form>
            <?php endif; ?>
        </div>
        <div class="inquiry-form">
            <h3>Contact Agent</h3>
            <p>Agent: <?= htmlspecialchars($property['username']) ?></p>
            <?php if (isset($success)): ?><div class="success"><?= $success ?></div><?php endif; ?>
            <?php if (isset($_SESSION['user_id'])): ?>
                <form method="POST">
                    <textarea name="message" placeholder="Your inquiry..." required rows="5"></textarea>
                    <button type="submit">Send Inquiry</button>
                </form>
            <?php else: ?>
                <p><a href="login.php">Login to inquire</a></p>
            <?php endif; ?>
        </div>
    </div>
 
    <script>
        // JS for image gallery if multiple images
    </script>
</body>
</html>
