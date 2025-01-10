<?php
include('connection.php');

// Query to get all center details
$stmt = $pdo->prepare("SELECT * FROM centers");
$stmt->execute();
$centers = $stmt->fetchAll();

// Check if there are any centers
if (!$centers) {
    echo "No centers found.";
    exit; // Stop the script if no centers are found
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Centers</title>
    <link rel="stylesheet" href="styles.css">
    <style>

.center-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); /* Responsive grid */
    gap: 20px;
    margin-left: 30px;
    margin-top: 20px;
    justify-content: left; /* Align items to the right */
}
        .view-details-btn {
            background-color: #4CAF50;
            color: white;
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            margin-top: 10px;
        }

        .view-details-btn:hover {
            background-color: #45a049;
        }

        .view-details-btn {
    background-color: #2a2185; /* Button background color */
    color: white; /* Text color */
    border: none; /* Remove border */
    padding: 10px 20px; /* Add padding for size */
    border-radius: 50px; /* Make the button round */
    cursor: pointer; /* Change cursor to pointer */
    font-size: 16px; /* Adjust text size */
    transition: background-color 0.3s, transform 0.2s; /* Smooth hover effects */
    margin-top: 10px;
    }

    .view-details-btn:hover {
        background-color: #4c43e0; /* Lighter shade for hover effect */
        transform: scale(1.05); /* Slightly enlarge on hover */
    }

      /* Modal styles */

      .modal {
    display: none;
    position: fixed;
    z-index: 1;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    overflow: auto;
    transition: opacity 0.3s ease;
}

.modal-content {
    background-color: #fff;
    margin: 10% auto;
    padding: 20px;
    border-radius: 10%; /* Makes the modal circular */
    width: 300px; /* Set fixed width */
    height: 300px; /* Set fixed height for circular shape */
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    position: relative;
    display: flex;
    flex-direction: column;
    justify-content: flex-start; /* Ensure header stays on top */
    transition: transform 0.3s ease-out;
    overflow: hidden; /* Keeps content inside the rounded borders */
}

.modal-header {
    font-size: 20px;
    font-weight: bold;
    text-align: center; /* Center header text */
    color: #fff;
    background-color: #2a2185; /* Header background color */
    width: 100%; /* Ensure it spans the full width of the modal */
    padding: 10px; /* Add padding for better spacing */
    border-top-left-radius: 10%; /* Match circular modal shape */
    border-top-right-radius: 10%; /* Match circular modal shape */
    position: absolute; /* Position it at the top */
    top: 0; /* Align to the top of the modal */
    left: 0; /* Align to the left of the modal */
    height: 20%; /* Adjust height for desired coverage */
    display: flex; /* Center content */
    align-items: center; /* Center vertically */
    justify-content: center; /* Center horizontally */
    z-index: 10; /* Ensures header stays above content */
}

.modal-body {
    margin-top: 75px; /* Create space below header */
    text-align: center;
    flex-grow: 1; /* Allow the body to grow and fill available space */
    overflow-y: auto; /* Allows scrolling if content overflows */
}

.close {
    color: #aaa;
    position: absolute;
    top: 10px;
    right: 10px;
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
}

.close:hover,
.close:focus {
    color: black;
    text-decoration: none;
    cursor: pointer;
}

    </style>
</head>
<body>
    <div class="sidenav">
        <?php include('sidenav.php'); ?>
    </div>

    <div class="main-content">
        <h1>All Centers</h1>
        <div class="center-grid">
            <?php foreach ($centers as $center): ?>
                <div class="center-box">
                    <img src="images/<?php echo htmlspecialchars($center['image']); ?>" alt="Center Image">
                    <h2><?php echo htmlspecialchars($center['name']); ?></h2>
                    <p>
                        <span class="status <?php echo htmlspecialchars(strtolower($center['status'])); ?>">
                            <?php echo htmlspecialchars($center['status']); ?>
                        </span>
                    </p>

                    <!-- View Details Button -->
                    <button class="view-details-btn" onclick="viewDetails(<?php echo htmlspecialchars($center['id']); ?>)">View Details</button>

                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Modal -->
    <div id="myModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <div class="modal-header">Center Details</div>
            <div class="modal-body" id="modal-body"></div>
        </div>
    </div>

    <script>
        // Function to open the modal and load center details
        function viewDetails(centerId) {
            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'get_center_details.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    var centerDetails = JSON.parse(xhr.responseText);
                    if (centerDetails) {
                        var modalBody = document.getElementById('modal-body');
                        modalBody.innerHTML = `
                            <p><strong>Name:</strong> ${centerDetails.name}</p>
                            <p><strong>Location:</strong> ${centerDetails.location}</p>
                            <p><strong>Manager:</strong> ${centerDetails.manager}</p>
                            <p><strong>Status:</strong> ${centerDetails.status}</p>
                        `;

                        // Show the modal with smooth animation
                        var modal = document.getElementById('myModal');
                        modal.style.display = 'block';
                        modal.style.opacity = 1;
                    } else {
                        alert("Center not found.");
                    }
                }
            };
            xhr.send('center_id=' + centerId);
        }

        // Close the modal when the user clicks the "X"
        var closeBtn = document.getElementsByClassName('close')[0];
        closeBtn.onclick = function() {
            var modal = document.getElementById('myModal');
            modal.style.display = 'none';
        }

        // Close the modal if the user clicks outside of the modal
        window.onclick = function(event) {
            var modal = document.getElementById('myModal');
            if (event.target === modal) {
                modal.style.display = 'none';
            }
        }
    </script>
</body>
</html>
