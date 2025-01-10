<?php
include('connection.php');

// Query to count centers based on their status
$statuses = ['active', 'inactive', 'relaunch', 'pending'];
$counts = [];

foreach ($statuses as $status) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM centers WHERE status = ?");
    $stmt->execute([$status]);
    $counts[$status] = $stmt->fetchColumn();
}

$stmt = $pdo->prepare("SELECT * FROM centers");
$stmt->execute();
$centers = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Center Management</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="sidenav">
        <?php include('sidenav.php'); ?>
    </div>

    <div class="main-content">
        <div class="cardBox">
            <div class="card">
                <div class="box"><h3>Total: <?php echo count($centers); ?></div>
                <div class="iconBx">
                    <ion-icon name="stats-chart-outline"></ion-icon>
                </div>
            </div>

            <div class="card">
                <div class="box"><h3> Active: <?php echo $counts['active']; ?></div>
                <div class="iconBx">
                    <ion-icon name="radio-button-on-outline"></ion-icon>
                </div>
            </div>

            <div class="card">
                <div class="box"><h3> Inactive: <?php echo $counts['inactive']; ?></div>
                <div class="iconBx">
                    <ion-icon name="radio-button-off-outline"></ion-icon>
                </div>
            </div>

            <div class="card">
                <div class="box"><h3> Relaunching: <?php echo $counts['relaunch']; ?></div>
                <div class="iconBx">
                    <ion-icon name="reload-circle-outline"></ion-icon>
                </div>
            </div>

            <div class="card">
                <div class="box"><h3> Pending: <?php echo $counts['pending']; ?></div>
                <div class="iconBx">
                    <ion-icon name="reload-circle-outline"></ion-icon>
                </div>
            </div>
        </div>

        <div class="centers-list">
    <h2>All Centers</h2>

    <table class="centers-list">
        <thead>
            <tr>
                <th>CENTERS</th>
                <th>LOCATION</th>
                <th>MANAGER</th>
                <th>STATUS</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($centers as $center): ?>
                <tr>
                    <td><?php echo htmlspecialchars($center['name']); ?></td>
                    <td><?php echo htmlspecialchars($center['location']); ?></td>
                    <td><?php echo htmlspecialchars($center['manager']); ?></td>
                    <td class="status <?php echo htmlspecialchars(strtolower($center['status'])); ?>">
                        <?php echo htmlspecialchars($center['status']); ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

    </div>
    
    <!-- =========== Scripts =========  -->
    <script src="assets/js/main.js"></script>

    <!-- ====== ionicons ======= -->
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
</body>
</html>
