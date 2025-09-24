<?php
// index.php - Homepage
session_start();
include 'db.php';
 
// Fetch featured properties (approved ones)
$stmt = $pdo->query("SELECT * FROM properties WHERE status = 'approved' ORDER BY created_at DESC LIMIT 6");
$properties = $stmt->fetchAll(PDO::FETCH_ASSOC);
 
// Sample market insights (static for now)
$insights = [
    'Average home price in NY: $500,000',
    'Trending: Suburban homes up 10%'
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dream it. Rent it. Buy it. - Zillow Clone</title>
    <style>
        /* Internal CSS - Modern, responsive, Zillow-inspired design */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #333; background: #f8f9fa; }
        header { background: linear-gradient(135deg, #0f0f23 0%, #1a1a2e 100%); color: white; padding: 1rem 0; position: fixed; width: 100%; top: 0; z-index: 1000; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        nav { display: flex; justify-content: space-between; align-items: center; max-width: 1200px; margin: 0 auto; padding: 0 2rem; }
        nav ul { display: flex; list-style: none; }
        nav ul li { margin-left: 2rem; }
        nav ul li a { color: white; text-decoration: none; transition: color 0.3s; }
        nav ul li a:hover { color: #ff6b35; }
        .logo { font-size: 1.5rem; font-weight: bold; }
        .hero { background: linear-gradient(rgba(0,0,0,0.4), rgba(0,0,0,0.4)), url('https://via.placeholder.com/1920x600?text=Hero+Image') center/cover; height: 60vh; display: flex; align-items: center; justify-content: center; text-align: center; color: white; margin-top: 70px; }
        .hero h1 { font-size: 3rem; margin-bottom: 1rem; }
        .hero p { font-size: 1.2rem; }
        .search-bar { background: white; padding: 2rem; max-width: 800px; margin: 2rem auto; border-radius: 10px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); display: flex; gap: 1rem; }
        .search-bar input, .search-bar select, .search-bar button { flex: 1; padding: 0.8rem; border: 1px solid #ddd; border-radius: 5px; }
        .search-bar button { background: #ff6b35; color: white; border: none; cursor: pointer; transition: background 0.3s; }
        .search-bar button:hover { background: #e55a2b; }
        .section { padding: 4rem 2rem; max-width: 1200px; margin: 0 auto; }
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
        .insights { background: #f0f8ff; padding: 2rem; border-radius: 10px; text-align: center; }
        footer { background: #0f0f23; color: white; text-align: center; padding: 2rem; margin-top: 4rem; }
        @media (max-width: 768px) { .search-bar { flex-direction: column; } .hero h1 { font-size: 2rem; } nav ul { flex-direction: column; gap: 1rem; } }
    </style>
</head>
<body>
    <header>
        <nav>
            <div class="logo">DreamZillow</div>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="search.php">Search</a></li>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li><a href="dashboard.php">Dashboard</a></li>
                    <li><a href="logout.php">Logout</a></li>
                <?php else: ?>
                    <li><a href="login.php">Login</a></li>
                    <li><a href="signup.php">Sign Up</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>
 
    <section class="hero">
        <div>
            <h1>Dream it. Rent it. Buy it.</h1>
            <p>Find your perfect home with our smart recommendations.</p>
        </div>
    </section>
 
    <div class="search-bar">
        <input type="text" placeholder="Enter a city or neighborhood" id="location">
        <select id="type">
            <option value="">Any Type</option>
            <option value="house">House</option>
            <option value="apartment">Apartment</option>
            <option value="commercial">Commercial</option>
        </select>
        <input type="number" placeholder="Min Price" id="minPrice">
        <input type="number" placeholder="Max Price" id="maxPrice">
        <button onclick="searchProperties()">Search</button>
    </div>
 
    <section class="section">
        <h2>Featured Properties</h2>
        <div class="grid" id="featured">
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
    </section>
 
    <section class="section">
        <h2>Market Insights</h2>
        <div class="insights">
            <ul style="list-style: none;">
                <?php foreach ($insights as $insight): ?>
                    <li style="margin-bottom: 0.5rem;"><?= $insight ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    </section>
 
    <footer>
        <p>&copy; 2025 DreamZillow. All rights reserved. Committed to fair housing.</p>
    </footer>
 
    <script>
        // Internal JS - For search redirection using JS
        function searchProperties() {
            const location = document.getElementById('location').value;
            const type = document.getElementById('type').value;
            const minPrice = document.getElementById('minPrice').value;
            const maxPrice = document.getElementById('maxPrice').value;
            let url = 'search.php?location=' + encodeURIComponent(location);
            if (type) url += '&type=' + type;
            if (minPrice) url += '&minPrice=' + minPrice;
            if (maxPrice) url += '&maxPrice=' + maxPrice;
            window.location.href = url; // JS redirection
        }
 
        // Add search on Enter key
        document.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') searchProperties();
        });
    </script>
</body>
</html>
