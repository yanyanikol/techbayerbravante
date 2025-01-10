<?php
include('connection.php');

// Handle Add Center form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_center'])) {
    $name = $_POST['name'];
    $location = $_POST['location'];
    $manager = $_POST['manager'];
    $status = $_POST['status'];

    // Insert new center into the database
    $stmt = $pdo->prepare("INSERT INTO centers (name, location, manager, status) VALUES (?, ?, ?, ?)");
    $stmt->execute([$name, $location, $manager, $status]);

    // Redirect after adding new center
    header('Location: update_center.php');
    exit;
}

// Handle delete operation
if (isset($_GET['delete_id']) && is_numeric($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];

    // Delete the center from the database
    $stmt = $pdo->prepare("DELETE FROM centers WHERE id = ?");
    $stmt->execute([$delete_id]);

    // Redirect after deletion
    header('Location: update_center.php');
    exit;
}

// Query to get all centers
$stmt = $pdo->prepare("SELECT * FROM centers");
$stmt->execute();
$centers = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Centers</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="sidenav">
        <?php include('sidenav.php'); ?>
    </div>

    <div class="main-content">
        <h1>Manage Centers</h1>

       <!-- Button to Add New Center -->
       <h2>Add New Center</h2>
       <a href="add_center.php">
            <button type="button">Add</button>
        </a>

        <br><br>

        <!-- Display all centers -->
        <div class="centers-list">
    <h2>All Centers</h2>
    <?php foreach ($centers as $center): ?>
        <div class="center-box" style="display: flex; justify-content: space-between; align-items: center; padding: 10px; border: 1px solid #ddd; border-radius: 8px; margin-bottom: 10px; font-size: 14px;">
            <!-- Fixed position for each field -->
            <div style="flex: 0 0 50px; text-align: center;">
                <?php echo htmlspecialchars($center['id']); ?>
            </div>
            <div style="flex: 1 1 auto; text-align: left; margin-left: 10px;">
                <?php echo htmlspecialchars($center['name']); ?>
            </div>
            <div style="flex: 1 1 auto; text-align: left; margin-left: 10px;">
                <?php echo htmlspecialchars($center['location']); ?>
            </div>
            <div style="flex: 1 1 auto; text-align: left; margin-left: 10px;">
                <?php echo htmlspecialchars($center['manager']); ?>
            </div>
            <!-- Status -->
            <span class="status <?php echo htmlspecialchars(strtolower($center['status'])); ?>" style="flex: 0 0 120px; text-align: center; margin-right: 20px;">
                <?php echo htmlspecialchars($center['status']); ?>
            </span>
            <!-- Buttons -->
            <span style="white-space: nowrap; flex: 0 0 200px; text-align: right;">
                <a href="edit_center.php?id=<?php echo $center['id']; ?>" 
                   style="padding: 6px 12px; background-color: #2a2185; color: #fff; text-decoration: none; border-radius: 4px; margin-right: 10px; font-size: 14px; display: inline-block; text-align: center;">
                    Edit
                </a>
                <a href="update_center.php?delete_id=<?php echo $center['id']; ?>" 
                   onclick="return confirm('Are you sure you want to delete this center?')" 
                   style="padding: 6px 12px; background-color: #f00; color: #fff; text-decoration: none; border-radius: 4px; font-size: 14px; display: inline-block; text-align: center;">
                    Delete
                </a>
            </span>
        </div>
    <?php endforeach; ?>
</div>

    </div>
</body>
</html>
