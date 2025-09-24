<?php
// dashboard.php - User dashboard for saved and manage listings
session_start();
include 'db.php';
 
if (!isset($_SESSION['user_id'])) {
    echo "<script>window.location.href='login.php';</script>";
    exit;
}
 
$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];
 
// Saved properties
$saved_stmt = $pdo->prepare("SELECT p.* FROM properties p JOIN saved_properties s ON p.id = s.property_id WHERE s.user_id = ? AND p.status = 'approved'");
$saved_stmt->execute([$user_id]);
$saved_properties = $saved_stmt->fetchAll(PDO::FETCH_ASSOC);
 
// User's listings (for agents)
$listings_stmt = $pdo->prepare("SELECT * FROM properties WHERE user_id = ?");
$listings_stmt->execute([$user_id]);
$listings = $listings_stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - DreamZillow</title>
    <style>
        /* Internal CSS - Dashboard tabs */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background: #f8f9fa; }
        header { background: linear-gradient(135deg, #0f0f23 0%, #1a1a2e 100%); color: white; padding: 1rem 0; }
        nav { display: flex; justify-content: space-between; max-width: 1200px; margin: 0 auto; padding: 0 2rem; }
        .logo { font-size: 1.5rem; font-weight: bold; }
        .container { max-width: 1200px; margin: 0 auto; padding: 2rem; }
        .tabs { display: flex; border-bottom: 1px solid #ddd; margin-bottom: 2rem; }
        .tab { padding: 1rem 2rem; cursor: pointer; background: none; border: none; }
        .tab.active { border-bottom: 2px solid #ff6b35; color: #ff6b35; }
        .section { display: none; }
        .section.active { display: block; }
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem; }
        .property-card { background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.1); padding: 1rem; }
        .btn { display: inline-block; padding: 0.5rem 1rem; background: #ff6b35; color: white; text-decoration: none; border-radius: 5px; margin-right: 0.5rem; }
        .status { color: #666; }
        @media (max-width: 768px) { .tabs { flex-direction: column; } }
    </style>
</head>
<body>
    <header>
        <nav>
            <div class="logo">DreamZillow Dashboard</div>
            <a href="index.php" style="color: white; text-decoration: none;">Home</a>
        </nav>
    </header>
 
    <div class="container">
        <h1>Welcome, <?= $_SESSION['username'] ?? 'User' ?>!</h1>
        <div class="tabs">
            <button class="tab active" onclick="showTab('saved')">Saved Listings</button>
            <?php if ($role === 'agent'): ?>
                <button class="tab" onclick="showTab('listings')">My Listings</button>
            <?php endif; ?>
        </div>
 
        <div id="saved" class="section active">
            <h2>Saved Properties</h2>
            <?php if (empty($saved_properties)): ?>
                <p>No saved properties. <a href="search.php">Start searching</a></p>
            <?php else: ?>
                <div class="grid">
                    <?php foreach ($saved_properties as $prop): ?>
                        <div class="property-card">
                            <h3><?= htmlspecialchars($prop['title']) ?></h3>
                            <div class="price">$<?= number_format($prop['price'], 2) ?></div>
                            <div class="location"><?= htmlspecialchars($prop['location']) ?></div>
                            <a href="property_detail.php?id=<?= $prop['id'] ?>" class="btn">View</a>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
 
        <?php if ($role === 'agent'): ?>
        <div id="listings" class="section">
            <h2>My Property Listings</h2>
            <a href="list_property.php" class="btn">Add New Listing</a>
            <?php if (empty($listings)): ?>
                <p>No listings yet. <a href="list_property.php">Add one</a></p>
            <?php else: ?>
                <div class="grid">
                    <?php foreach ($listings as $prop): ?>
                        <div class="property-card">
                            <h3><?= htmlspecialchars($prop['title']) ?></h3>
                            <div class="price">$<?= number_format($prop['price'], 2) ?></div>
                            <div class="status">Status: <?= ucfirst($prop['status']) ?></div>
                            <a href="property_detail.php?id=<?= $prop['id'] ?>" class="btn">Edit/View</a>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
 
    <script>
        // Internal JS - Tab switching
        function showTab(tabName) {
            document.querySelectorAll('.section').forEach(s => s.classList.remove('active'));
            document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
            document.getElementById(tabName).classList.add('active');
            event.target.classList.add('active');
        }
    </script>
</body>
</html>
