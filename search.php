<?php
// search.php - Search and filtering
session_start();
include 'db.php';
 
$where = "status = 'approved'";
$params = [];
 
if (isset($_GET['location']) && !empty($_GET['location'])) {
    $where .= " AND location LIKE ?";
    $params[] = '%' . $_GET['location'] . '%';
}
if (isset($_GET['type']) && !empty($_GET['type'])) {
    $where .= " AND type = ?";
    $params[] = $_GET['type'];
}
if (isset($_GET['minPrice']) && !empty($_GET['minPrice'])) {
    $where .= " AND price >= ?";
    $params[] = $_GET['minPrice'];
}
if (isset($_GET['maxPrice']) && !empty($_GET['maxPrice'])) {
    $where .= " AND price <= ?";
    $params[] = $_GET['maxPrice'];
}
 
$stmt = $pdo->prepare("SELECT * FROM properties WHERE $where ORDER BY created_at DESC");
$stmt->execute($params);
$properties = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results - DreamZillow</title>
    <style>
        /* Reuse homepage CSS styles here for consistency */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #333; background: #f8f9fa; }
        header { background: linear-gradient(135deg, #0f0f23 0%, #1a1a2e 100%); color: white; padding: 1rem 0; position: fixed; width: 100%; top: 0; z-index: 1000; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        nav { display: flex; justify-content: space-between; align-items: center; max-width: 1200px; margin: 0 auto; padding: 0 2rem; }
        nav ul { display: flex; list-style: none; }
        nav ul li { margin-left: 2rem; }
        nav ul li a { color: white; text-decoration: none; transition: color 0.3s; }
        nav ul li a:hover { color: #ff6b35; }
        .logo { font-size: 1.5rem; font-weight: bold; }
        .section { padding: 4rem 2rem; max-width: 1200px; margin: 0 auto; margin-top: 70px; }
        .section h2 { text-align: center; margin-bottom: 2rem; font-size: 2.5rem; color: #0f0f23; }
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem; }
        .property-card { background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.1); transition: transform 0.3s; }
        .property-card:hover { transform: translateY(-5px); }
        .property-card img { width: 100%; height: 200px; object-fit: cover; }
        .property-card-body { padding: 1rem; }
        .property-card h3 { margin-bottom: 0.5rem; color: #0f0f23; }
        .price { font-size: 1.5rem; font-weight: bold; color: #ff6b35; margin-bottom: 0.5rem; }
        .location { color: #666; margin-bottom: 1rem; }
        .btn { display: inline-block; padding: 0.5rem 1rem; background: #ff6b35; color: white; text-decoration: none; border-radius: 5px; transition: background 0.3s; }
        .btn:hover { background: #e55a2b; }
        .no-results { text-align: center; font-size: 1.2rem; color: #666; }
        @media (max-width: 768px) { nav ul { flex-direction: column; gap: 1rem; } }
    </style>
</head>
<body>
    <header>
        <nav>
            <div class="logo">DreamZillow</div>
            <ul>
                <li><a href="index.php">Home</a></li>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li><a href="dashboard.php">Dashboard</a></li>
                    <li><a href="logout.php">Logout</a></li>
                <?php else: ?>
                    <li><a href="login.php">Login</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>
 
    <section class="section">
        <h2>Search Results</h2>
        <?php if (empty($properties)): ?>
            <div class="no-results">No properties found. Try adjusting your filters.</div>
        <?php else: ?>
            <div class="grid">
                <?php foreach ($properties as $prop): ?>
                    <div class="property-card">
                        <img src="<?= $prop['images'][0] ?? 'https://via.placeholder.com/300x200?text=Property' ?>" alt="<?= $prop['title'] ?>">
                        <div class="property-card-body">
                            <h3><?= htmlspecialchars($prop['title']) ?></h3>
                            <div class="price">$<?= number_format($prop['price'], 2) ?></div>
                            <div class="location"><?= htmlspecialchars($prop['location']) ?></div>
                            <a href="property_detail.php?id=<?= $prop['id'] ?>" class="btn">View Details</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <div style="text-align: center; margin-top: 2rem;">
            <a href="index.php" class="btn">Back to Home</a>
        </div>
    </section>
 
    <script>
        // JS for additional filters if needed
    </script>
</body>
</html>
