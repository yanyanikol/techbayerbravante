<?php
session_start();
include('db.php');

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

// Handle Delete User
if (isset($_POST['delete_user'])) {
    $id = $_POST['user_id'];
    $query = $conn->prepare("DELETE FROM users WHERE id = ?");
    $query->bind_param("i", $id);
    $query->execute();
}

// Handle Edit User
$edit_user = null;
if (isset($_POST['edit_user'])) {
    $id = $_POST['user_id'];
    $query = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $query->bind_param("i", $id);
    $query->execute();
    $result = $query->get_result();
    $edit_user = $result->fetch_assoc();
}

// Handle Update User
if (isset($_POST['update_user'])) {
    $id = $_POST['user_id'];
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

    $query = $conn->prepare("UPDATE users SET first_name=?, middle_name=?, last_name=?, age=?, gender=?, contact=?, position=?, email=?, password=?, role=?, photo=? WHERE id=?");
    $query->bind_param("sssisssssssi", $first_name, $middle_name, $last_name, $age, $gender, $contact, $position, $email, $password, $role, $photo, $id);
    $query->execute();
}

// Fetch Users
$users = $conn->query("SELECT * FROM users");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Panel</title>
    <link rel="stylesheet" href="css/accountstyle.css">
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
            text-align: center;
        }
        th, td {
            padding: 10px;
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
                <img src="<?php echo $user['image'] ?? 'images/default.png'; ?>" alt="Profile Picture" width="100" height="100">
    </div>
    
<!-- Photo Upload -->
    <div>
    <label for="photo">Profile Photo</label>
    <input type="file" name="photo" id="photo">
    </div>

    <div>
    <!-- First Name Label -->
    <label for="first_name">First Name</label>
    <input type="text" name="first_name" id="first_name" placeholder="First Name" value="<?php echo $edit_user['first_name'] ?? ''; ?>" required>
    </div>


    <div>
    <!-- Middle Name Label -->
    <label for="middle_name">Middle Name</label>
    <input type="text" name="middle_name" id="middle_name" placeholder="Middle Name" value="<?php echo $edit_user['middle_name'] ?? ''; ?>">
    </div>


    <div>
    <!-- Last Name Label -->
    <label for="last_name">Last Name</label>
    <input type="text" name="last_name" id="last_name" placeholder="Last Name" value="<?php echo $edit_user['last_name'] ?? ''; ?>" required>
    </div>


    <div>
    <!-- Age Label -->
    <label for="age">Age</label>
    <input type="number" name="age" id="age" placeholder="Age" value="<?php echo $edit_user['age'] ?? ''; ?>" required>
    </div>


    <div>
    <!-- Gender Label -->
    <label for="gender">Gender</label>
    <select name="gender" id="gender">
        <option value="male" <?php echo (isset($edit_user['gender']) && $edit_user['gender'] == 'male') ? 'selected' : ''; ?>>Male</option>
        <option value="female" <?php echo (isset($edit_user['gender']) && $edit_user['gender'] == 'female') ? 'selected' : ''; ?>>Female</option>
        <option value="others" <?php echo (isset($edit_user['gender']) && $edit_user['gender'] == 'others') ? 'selected' : ''; ?>>Others</option>
    </select>
    </div>

    <div>
    <!-- Contact Label -->
    <label for="contact">Contact</label>
    <input type="text" name="contact" id="contact" placeholder="Contact" value="<?php echo $edit_user['contact'] ?? ''; ?>">
    </div>

    <div>
    <!-- Position Label -->
    <label for="position">Position</label>
    <input type="text" name="position" id="position" placeholder="Position" value="<?php echo $edit_user['position'] ?? ''; ?>">
    </div>

    <div>
    <!-- Email Label -->
    <label for="email">Email</label>
    <input type="email" name="email" id="email" placeholder="Email" value="<?php echo $edit_user['email'] ?? ''; ?>" required>
    </div>

    <div>
    <!-- Password Label -->
    <label for="password">Password</label>
    <input type="password" name="password" id="password" placeholder="Password" value="<?php echo $edit_user['password'] ?? ''; ?>" required>
    </div>

    <div>
    <!-- Role Label -->
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

    </div>
    </div>

    <!-- User List -->
    <h3>User List</h3>
<table>
    <tr>
        <th>First Name</th>
        <th>Middle Name</th>
        <th>Last Name</th>
        <th>Age</th>
        <th>Gender</th>
        <th>Contact</th>
        <th>Position</th>
        <th>Email</th>
        <th>Role</th>
        <th>Password</th>
        <th>Photo</th>
        <th>Actions</th>
    </tr>
    <?php while ($row = $users->fetch_assoc()) { ?>
    <tr>
        <td><?php echo $row['first_name']; ?></td>
        <td><?php echo $row['middle_name']; ?></td>
        <td><?php echo $row['last_name']; ?></td>
        <td><?php echo $row['age']; ?></td>
        <td><?php echo $row['gender']; ?></td>
        <td><?php echo $row['contact']; ?></td>
        <td><?php echo $row['position']; ?></td>
        <td><?php echo $row['email']; ?></td>
        <td><?php echo $row['role']; ?></td>
        <td><?php echo $row['password']; ?></td>
        <td>
            <?php if ($row['image']) { ?>
                <img src="<?php echo $row['image']; ?>" alt="Profile Picture" width="50" height="50">
            <?php } ?>
        </td>
        <td>
            <form method="POST">
                <input type="hidden" name="user_id" value="<?php echo $row['id']; ?>">
                <button type="submit" name="edit_user" class="edit">Edit</button>
                <button type="submit" name="delete_user" class="delete">Delete</button>
            </form>
        </td>
    </tr>
    <?php } ?>
</table>

</body>
</html>
