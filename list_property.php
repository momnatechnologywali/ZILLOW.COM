<?php
// list_property.php - For agents/homeowners to list properties
session_start();
include 'db.php';
 
if (!isset($_SESSION['user_id'])) {
    echo "<script>window.location.href='login.php';</script>";
    exit;
}
 
if ($_POST) {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $location = $_POST['location'];
    $type = $_POST['type'];
    $bedrooms = $_POST['bedrooms'];
    $bathrooms = $_POST['bathrooms'];
    $square_feet = $_POST['square_feet'];
    $amenities = json_encode(explode(',', $_POST['amenities'])); // Simple comma-separated
    $images = json_encode([]); // Placeholder; in real, handle file upload
    $user_id = $_SESSION['user_id'];
 
    $stmt = $pdo->prepare("INSERT INTO properties (title, description, price, location, type, bedrooms, bathrooms, square_feet, amenities, images, user_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$title, $description, $price, $location, $type, $bedrooms, $bathrooms, $square_feet, $amenities, $images, $user_id]);
    $success = "Property listed successfully! Pending approval.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>List Property - DreamZillow</title>
    <style>
        /* Internal CSS - Form styling */
        body { font-family: 'Segoe UI', sans-serif; background: #f8f9fa; padding: 2rem; }
        .form-container { background: white; padding: 2rem; border-radius: 10px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); max-width: 600px; margin: 0 auto; }
        h2 { text-align: center; margin-bottom: 1rem; color: #333; }
        input, textarea, select { width: 100%; padding: 0.8rem; margin-bottom: 1rem; border: 1px solid #ddd; border-radius: 5px; }
        button { width: 100%; padding: 0.8rem; background: #ff6b35; color: white; border: none; border-radius: 5px; cursor: pointer; transition: background 0.3s; }
        button:hover { background: #e55a2b; }
        .success { color: green; text-align: center; margin-bottom: 1rem; }
        .link { text-align: center; margin-top: 1rem; }
        .link a { color: #ff6b35; text-decoration: none; }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>List a Property</h2>
        <?php if (isset($success)): ?><div class="success"><?= $success ?></div><?php endif; ?>
        <form method="POST">
            <input type="text" name="title" placeholder="Property Title" required>
            <textarea name="description" placeholder="Description" rows="4" required></textarea>
            <input type="number" name="price" placeholder="Price" step="0.01" required>
            <input type="text" name="location" placeholder="Location (City, State)" required>
            <select name="type" required>
                <option value="house">House</option>
                <option value="apartment">Apartment</option>
                <option value="commercial">Commercial</option>
            </select>
            <input type="number" name="bedrooms" placeholder="Bedrooms" min="0">
            <input type="number" name="bathrooms" placeholder="Bathrooms" min="0">
            <input type="number" name="square_feet" placeholder="Square Feet" min="0">
            <input type="text" name="amenities" placeholder="Amenities (comma-separated, e.g., pool,garage)">
            <button type="submit">List Property</button>
        </form>
        <div class="link"><a href="dashboard.php">Back to Dashboard</a></div>
    </div>
    <script>
        // JS for dynamic form enhancements if needed
    </script>
</body>
</html>
