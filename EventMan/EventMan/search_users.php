<?php
include('includes/dbconnection.php');

// Check if the search form is submitted for users
if (isset($_GET['searchUsers'])) {
    $search = $_GET['searchUsers'];
    // Retrieve users matching the search query
    $userSql = "SELECT user_id, username, phone_number, email, gender, date_of_birth, status 
                FROM users 
                WHERE CONCAT(first_name, ' ', last_name) LIKE :search 
                OR phone_number LIKE :search 
                OR email LIKE :search";
    $stmt = $conn->prepare($userSql);
    $stmt->execute(['search' => "%$search%"]);
} else {
    // If no search query, retrieve all users
    $userSql = "SELECT user_id, username, phone_number, email, gender, date_of_birth, status FROM users";
    $stmt = $conn->query($userSql);
}

$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
