<?php
include('../includes/dbconnection.php');

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = !empty($_POST['email']) ? $_POST['email'] : null;
    $password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_BCRYPT) : null;
    $first_name = !empty($_POST['first_name']) ? $_POST['first_name'] : null;
    $last_name = !empty($_POST['last_name']) ? $_POST['last_name'] : null;
    $status = !empty($_POST['status']) ? $_POST['status'] : null;
    $role = !empty($_POST['role']) ? $_POST['role'] : null;
    $gender = !empty($_POST['gender']) ? $_POST['gender'] : null;
    $date_of_birth = !empty($_POST['date_of_birth']) ? $_POST['date_of_birth'] : null;
    $phone_number = !empty($_POST['phone_number']) ? $_POST['phone_number'] : null;

    if (isset($_POST['createUser'])) {
        // Create user logic
        $insertSql = "INSERT INTO users (username, email, password, first_name, last_name, status, role, gender, date_of_birth, phone_number) 
                    VALUES (:username, :email, :password, :first_name, :last_name, :status, :role, :gender, :date_of_birth, :phone_number)";
        $stmt = $conn->prepare($insertSql);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $password);
        $stmt->bindParam(':first_name', $first_name);
        $stmt->bindParam(':last_name', $last_name);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':role', $role);
        $stmt->bindParam(':gender', $gender);
        $stmt->bindParam(':date_of_birth', $date_of_birth);
        $stmt->bindParam(':phone_number', $phone_number);

        if ($stmt->execute()) {
            $message = "Account created successfully.";
            $messageType = "success";
        } else {
            $message = "Error creating account.";
            $messageType = "error";
        }
    } elseif (isset($_POST['editUser'])) {
        // Edit user logic
        if (empty($username)) {
            $message = "Username cannot be blank.";
            $messageType = "error";
        } else {
            $updateSql = "UPDATE users SET 
                email = COALESCE(NULLIF(:email, ''), email),
                password = COALESCE(NULLIF(:password, ''), password),
                first_name = COALESCE(NULLIF(:first_name, ''), first_name),
                last_name = COALESCE(NULLIF(:last_name, ''), last_name),
                status = COALESCE(NULLIF(:status, ''), status),
                role = COALESCE(NULLIF(:role, ''), role),
                gender = COALESCE(NULLIF(:gender, ''), gender),
                date_of_birth = COALESCE(NULLIF(:date_of_birth, ''), date_of_birth),
                phone_number = COALESCE(NULLIF(:phone_number, ''), phone_number)
                WHERE username = :username";
            $stmt = $conn->prepare($updateSql);
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password', $password);
            $stmt->bindParam(':first_name', $first_name);
            $stmt->bindParam(':last_name', $last_name);
            $stmt->bindParam(':status', $status);
            $stmt->bindParam(':role', $role);
            $stmt->bindParam(':gender', $gender);
            $stmt->bindParam(':date_of_birth', $date_of_birth);
            $stmt->bindParam(':phone_number', $phone_number);

            if ($stmt->execute()) {
                $message = "Account updated successfully.";
                $messageType = "success";
            } else {
                $message = "Error updating account.";
                $messageType = "error";
            }
        }
    } elseif (isset($_POST['terminateUser'])) {
        // Terminate user logic
        if (empty($username)) {
            $message = "Username cannot be blank.";
            $messageType = "error";
        } else {
            $terminateSql = "UPDATE users SET status = 0 WHERE username = :username";
            $stmt = $conn->prepare($terminateSql);
            $stmt->bindParam(':username', $username);

            if ($stmt->execute()) {
                $message = "Account terminated successfully.";
                $messageType = "success";
            } else {
                $message = "Error terminating account.";
                $messageType = "error";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <title>Create/Edit/Terminate User</title>
</head>
<body>
    <h2 class="text-2xl font-bold mb-4">Create/Edit/Terminate User</h2>
    <?php if (!empty($message)): ?>
        <div class="mb-4 p-4 <?php echo ($messageType == 'success') ? 'bg-green-100 border-green-400 text-green-700' : 'bg-red-100 border-red-400 text-red-700'; ?> border rounded">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>
    <form id="userForm" method="POST" action="">
        <div class="grid grid-cols-2 gap-4">
            <div class="mb-4">
                <label for="username" class="block text-gray-700">Username</label>
                <input type="text" name="username" id="username" required class="border p-2 w-full">
            </div>
            <div class="mb-4">
                <label for="email" class="block text-gray-700">Email</label>
                <input type="email" name="email" id="email" class="border p-2 w-full">
            </div>
            <div class="mb-4">
                <label for="password" class="block text-gray-700">Password</label>
                <input type="password" name="password" id="password" class="border p-2 w-full">
            </div>
            <div class="mb-4">
                <label for="first_name" class="block text-gray-700">First Name</label>
                <input type="text" name="first_name" id="first_name" class="border p-2 w-full">
            </div>
            <div class="mb-4">
                <label for="last_name" class="block text-gray-700">Last Name</label>
                <input type="text" name="last_name" id="last_name" class="border p-2 w-full">
            </div>
            <div class="mb-4">
                <label for="status" class="block text-gray-700">Status</label>
                <select name="status" id="status" class="border p-2 w-full">
                    <option value="Active">Active</option>
                    <option value="Inactive">Inactive</option>
                </select>
            </div>
            <div class="mb-4">
                <label for="role" class="block text-gray-700">Role</label>
                <select name="role" id="role" class="border p-2 w-full">
                    <option value="Admin">Admin</option>
                    <option value="User">User</option>
                </select>
            </div>
            <div class="mb-4">
                <label for="gender" class="block text-gray-700">Gender</label>
                <select name="gender" id="gender" class="border p-2 w-full">
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                </select>
            </div>
            <div class="mb-4">
                <label for="date_of_birth" class="block text-gray-700">Date of Birth</label>
                <input type="date" name="date_of_birth" id="date_of_birth" class="border p-2 w-full">
            </div>
            <div class="mb-4">
                <label for="phone_number" class="block text-gray-700">Phone Number</label>
                <input type="text" name="phone_number" id="phone_number" class="border p-2 w-full">
            </div>
        </div>
        <div class="flex gap-4">
            <button type="submit" name="createUser" class="bg-blue-500 text-white px-4 py-2 rounded">Create User</button>
            <button type="submit" name="editUser" class="bg-yellow-500 text-white px-4 py-2 rounded">Edit User</button>
            <button type="submit" name="terminateUser" class="bg-red-500 text-white px-4 py-2 rounded">Terminate User</button>
        </div>
    </form>
</body>
</html>
