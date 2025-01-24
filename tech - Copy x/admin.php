<?php
session_start();
include('db.php');

// Check if the user is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header('Location: login.php');
    exit;
}

// Handle Add User
if (isset($_POST['add_user'])) {
    $first_name = $_POST['first_name'];
    $middle_name = $_POST['middle_name'];
    $last_name = $_POST['last_name'];
    $age = $_POST['age'];
    $gender = $_POST['gender'];
    $contact = $_POST['contact'];
    $position = $_POST['position'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    // Handle the image upload
    $photo = '';
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
        $photo = 'uploads/' . basename($_FILES['photo']['name']);
        move_uploaded_file($_FILES['photo']['tmp_name'], $photo);
    }

    $query = $conn->prepare("INSERT INTO users (first_name, middle_name, last_name, age, gender, contact, position, email, password, role, photo) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $query->bind_param("sssissssss", $first_name, $middle_name, $last_name, $age, $gender, $contact, $position, $email, $password, $role, $photo);
    $query->execute();
}

// Fetch Users from Database
$users = $conn->query("SELECT * FROM users");

if (!$users) {
    die("Query failed: " . $conn->error);
}

// Handle Delete User
if (isset($_POST['delete_user'])) {
    $user_id = $_POST['user_id'];
    $delete_query = $conn->prepare("DELETE FROM users WHERE id = ?");
    $delete_query->bind_param("i", $user_id);
    $delete_query->execute();
    echo json_encode(['status' => 'success']);
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Panel</title>
    <link rel="stylesheet" href="css/accountstyle.css">
    <script src="https://cdn.jsdelivr.net/npm/ionicons@5.5.3/dist/ionicons.js"></script>
    <style>
        /* Modal styles */
        .modal {
            display: none; 
            position: fixed;
            z-index: 1; 
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto; 
            background-color: rgba(0, 0, 0, 0.4); 
        }
        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 600px;
        }
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }
        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
        /* User grid layout */
        .user-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 20px;
            padding: 20px;
        }

        .user-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            background-color: #f9f9f9;
            text-align: center;
            cursor: pointer;
            transition: transform 0.3s ease;
        }

        .user-card img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            margin-bottom: 10px;
        }

        .user-card h4 {
            margin: 5px 0;
        }

        .user-card .position {
            font-size: 0.9rem;
            color: #777;
        }

        .user-card:hover {
            transform: translateY(-5px);
        }
    </style>
</head>
<body>

<div class="sidenav">
    <?php include('sidenav.php'); ?>
</div>

<div class="main-content">
    <div class="account-container">
        <h2>Admin Panel</h2>

        <!-- User Form -->
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="user_id" value="<?php echo $edit_user['id'] ?? ''; ?>">

            <div>
                <img src="<?php echo $edit_user['photo'] ?? 'images/default.png'; ?>" alt="Profile Picture" width="100" height="100">
            </div>

            <!-- Photo Upload -->
            <div>
                <label for="photo">Profile Photo</label>
                <input type="file" name="photo" id="photo">
            </div>

            <div>
                <label for="first_name">First Name</label>
                <input type="text" name="first_name" id="first_name" placeholder="First Name" value="<?php echo $edit_user['first_name'] ?? ''; ?>" required>
            </div>

            <div>
                <label for="middle_name">Middle Name</label>
                <input type="text" name="middle_name" id="middle_name" placeholder="Middle Name" value="<?php echo $edit_user['middle_name'] ?? ''; ?>">
            </div>

            <div>
                <label for="last_name">Last Name</label>
                <input type="text" name="last_name" id="last_name" placeholder="Last Name" value="<?php echo $edit_user['last_name'] ?? ''; ?>" required>
            </div>

            <div>
                <label for="age">Age</label>
                <input type="number" name="age" id="age" placeholder="Age" value="<?php echo $edit_user['age'] ?? ''; ?>" required>
            </div>

            <div>
                <label for="gender">Gender</label>
                <select name="gender" id="gender">
                    <option value="male" <?php echo (isset($edit_user['gender']) && $edit_user['gender'] == 'male') ? 'selected' : ''; ?>>Male</option>
                    <option value="female" <?php echo (isset($edit_user['gender']) && $edit_user['gender'] == 'female') ? 'selected' : ''; ?>>Female</option>
                    <option value="others" <?php echo (isset($edit_user['gender']) && $edit_user['gender'] == 'others') ? 'selected' : ''; ?>>Others</option>
                </select>
            </div>

            <div>
                <label for="contact">Contact</label>
                <input type="text" name="contact" id="contact" placeholder="Contact" value="<?php echo $edit_user['contact'] ?? ''; ?>">
            </div>

            <div>
                <label for="position">Position</label>
                <input type="text" name="position" id="position" placeholder="Position" value="<?php echo $edit_user['position'] ?? ''; ?>">
            </div>

            <div>
                <label for="email">Email</label>
                <input type="email" name="email" id="email" placeholder="Email" value="<?php echo $edit_user['email'] ?? ''; ?>" required>
            </div>

            <div>
                <label for="password">Password</label>
                <input type="password" name="password" id="password" placeholder="Password" value="<?php echo $edit_user['password'] ?? ''; ?>" required>
            </div>

            <div>
                <label for="role">Role</label>
                <select name="role" id="role">
                    <option value="user" <?php echo (isset($edit_user['role']) && $edit_user['role'] == 'user') ? 'selected' : ''; ?>>User</option>
                    <option value="admin" <?php echo (isset($edit_user['role']) && $edit_user['role'] == 'admin') ? 'selected' : ''; ?>>Admin</option>
                </select>
            </div>

            <button type="submit" name="<?php echo isset($edit_user) ? 'update_user' : 'add_user'; ?>">
                <?php echo isset($edit_user) ? 'Update User' : 'Add User'; ?>
            </button>
        </form>

        <!-- User List in Grid -->
        <h3>User List</h3>
        <div class="user-grid">
            <?php 
            if ($users->num_rows > 0) {
                while ($row = $users->fetch_assoc()) { ?>
                    <div class="user-card" onclick="viewDetails(<?php echo $row['id']; ?>)">
                        <img src="<?php echo $row['photo'] ?? 'images/default.png'; ?>" alt="Profile Picture">
                        <h4><?php echo $row['first_name'] . ' ' . $row['last_name']; ?></h4>
                        <div class="position"><?php echo $row['position']; ?></div>
                    </div>
                <?php } 
            } else {
                echo "No users found.";
            }
            ?>
        </div>
    </div>
</div>

<!-- Modal for Viewing User Details -->
<div id="myModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <div class="modal-header">User Details</div>
        <div class="modal-body">
            <div class="modal-details">
                <div class="details-text" id="modal-text"></div>
                <div class="details-image" id="modal-image"></div>
            </div>
        </div>
        <div class="modal-footer">
            <button id="editBtn" class="view-details" onclick="editUser()">Edit</button>
            <button id="deleteBtn" class="view-details" onclick="deleteUser()">Delete</button>
        </div>
    </div>
</div>

<script>
    var modal = document.getElementById("myModal");
    var span = document.getElementsByClassName("close")[0];
    var modalText = document.getElementById("modal-text");
    var modalImage = document.getElementById("modal-image");
    var currentUserId = null;

    // Function to open the modal and load the user details
    function viewDetails(userId) {
        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'get_user_details.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                var userDetails = JSON.parse(xhr.responseText);
                if (userDetails) {
                    modalText.innerHTML = `
                        <p><strong>Name:</strong> ${userDetails.first_name} ${userDetails.last_name}</p>
                        <p><strong>Age:</strong> ${userDetails.age}</p>
                        <p><strong>Gender:</strong> ${userDetails.gender}</p>
                        <p><strong>Contact:</strong> ${userDetails.contact}</p>
                        <p><strong>Email:</strong> ${userDetails.email}</p>
                        <p><strong>Role:</strong> ${userDetails.role}</p>
                    `;
                    modalImage.innerHTML = `<img src="uploads/${userDetails.photo}" alt="${userDetails.first_name}" width="150">`; // Correct image path
                    currentUserId = userDetails.id;
                    modal.style.display = "block";
                } else {
                    alert("User not found.");
                }
            }
        };
        xhr.send('user_id=' + userId);
    }

    // Close the modal
    span.onclick = function() {
        modal.style.display = "none";
    }

    // Close the modal if clicked outside
    window.onclick = function(event) {
        if (event.target === modal) {
            modal.style.display = "none";
        }
    }

    // Edit user function (redirect to edit page)
    function editUser() {
        if (currentUserId) {
            window.location.href = `edit_user.php?id=${currentUserId}`;
        }
    }

    // Delete user function (handles deletion directly via POST request)
    function deleteUser() {
        if (currentUserId) {
            if (confirm("Are you sure you want to delete this user?")) {
                var xhr = new XMLHttpRequest();
                xhr.open('POST', '', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.onreadystatechange = function() {
                    if (xhr.readyState === 4 && xhr.status === 200) {
                        var response = JSON.parse(xhr.responseText);
                        if (response.status === 'success') {
                            alert("User deleted successfully!");
                            location.reload();
                        } else {
                            alert("Error deleting user.");
                        }
                    }
                };
                xhr.send('delete_user=true&user_id=' + currentUserId);
            }
        }
    }
</script>

</body>
</html>
