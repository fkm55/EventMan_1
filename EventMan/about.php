<?php
session_start();
include('includes/dbconnection.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate input
    $name = isset($_POST['name']) ? $_POST['name'] : '';
    $email = isset($_POST['email']) ? $_POST['email'] : '';
    $comment = isset($_POST['comment']) ? $_POST['comment'] : '';

    if (!empty($comment)) {
        // Prepare SQL statement to insert data
        $sql = "INSERT INTO about (name, email, comment) VALUES (:name, :email, :comment)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':comment', $comment);

        if ($stmt->execute()) {
            $success_message = "Comment submitted successfully!";
        } else {
            $error_message = "Error submitting comment: " . implode(" ", $stmt->errorInfo());
        }
    } else {
        $error_message = "Please fill the comment.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Eventify</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <style>
/* Custom styles for the About Us page */
.about-section {
      padding: 60px 20px;
      background-color: #f9fafb;
      position: relative;
    }

    .content-wrapper {
      display: flex;
      flex-direction: row;
      justify-content: center;
      align-items: flex-start;
      gap: 20px;
    }

    .about-content {
      max-width: 500px;
      background-color: #ffffff;
      border-radius: 8px;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
      padding: 40px;
      position: relative;
      overflow: hidden;
    }

    .about-title {
      font-size: 24px;
      font-weight: 700;
      color: #333333;
      margin-bottom: 20px;
      text-align: center;
      position: relative;
    }

    .about-title::before,
    .about-title::after {
      content: '';
      position: absolute;
      top: 50%;
      width: 50px;
      height: 4px;
      background-color: #ff7e5f;
    }

    .about-title::before {
      left: -60px;
    }

    .about-title::after {
      right: -60px;
    }

    .about-description {
      font-size: 14px;
      line-height: 1.6;
      color: #555555;
      margin-bottom: 20px;
    }

    .comment-box {
      padding: 20px;
      background-color: #ffffff;
      border-radius: 8px;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
      max-width: 400px;
      position: relative;
    }

    .comment-title {
      font-size: 18px;
      font-weight: 700;
      color: #333333;
      margin-bottom: 10px;
      text-align: center;
    }

    .comment-form label {
      font-size: 12px;
      font-weight: 600;
      color: #555555;
      display: block;
      margin-bottom: 4px;
    }

    .comment-form input[type="text"],
    .comment-form input[type="email"],
    .comment-form textarea {
      width: 100%;
      padding: 6px;
      border: 1px solid #dddddd;
      border-radius: 4px;
      margin-bottom: 8px;
      font-size: 12px;
    }

    .comment-form textarea {
      resize: vertical;
      min-height: 80px;
    }

    .comment-form button {
      background-color: #4caf50;
      color: #ffffff;
      border: none;
      padding: 8px 12px;
      border-radius: 4px;
      font-size: 12px;
      cursor: pointer;
      transition: background-color 0.3s ease;
      display: block;
      margin-left: auto;
      margin-right: auto;
    }

    .comment-form button:hover {
      background-color: #45a049;
    }

    /* Bouncing ball animation */
    .bouncing-ball {
      position: absolute;
      width: 20px;
      height: 20px;
      background-color: #ff7e5f;
      border-radius: 50%;
      animation: bounce 2s infinite;
    }

    .bouncing-ball.left {
      top: -20px;
      left: -20px;
    }

    .bouncing-ball.right {
      top: -20px;
      right: -20px;
    }

    .bouncing-ball.bottom-left {
      bottom: -20px;
      left: -20px;
    }

    .bouncing-ball.bottom-right {
      bottom: -20px;
      right: -20px;
    }

    @keyframes bounce {
      0%, 100% {
        transform: translateY(0);
      }
      50% {
        transform: translateY(-30px);
      }
    }

    /* Decorative background colors */
    .background-decor {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: linear-gradient(to right, #ff7e5f, #feb47b);
      clip-path: polygon(0 0, 100% 0, 100% 15%, 0 85%);
      z-index: -1;
    }

    .about-content::before {
      content: '';
      position: absolute;
      top: -50px;
      left: -50px;
      width: 150px;
      height: 150px;
      background-color: rgba(255, 126, 95, 0.2);
      border-radius: 50%;
      z-index: -1;
    }

    .about-content::after {
      content: '';
      position: absolute;
      bottom: -50px;
      right: -50px;
      width: 150px;
      height: 150px;
      background-color: rgba(255, 180, 123, 0.2);
      border-radius: 50%;
      z-index: -1;
    }







    /* Style the sticky navbar */
    .sticky {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      z-index: 9999;
    }

    @media (max-width: 640px) {
      .mobile-menu-button {
        display: block;
      }

      .desktop-menu {
        display: none;
      }

      .mobile-menu {
        display: none;
        position: absolute;
        top: 100%;
        right: 0;
        background-color: #fff;
        border: 1px solid #ddd;
        padding: 10px;
        border-radius: 4px;
        z-index: 1000;
      }

      .mobile-menu.active {
        display: block;
      }
    }

    @media (min-width: 641px) {
      .mobile-menu-button {
        display: none;
      }

      .desktop-menu {
        display: flex;
      }

      .mobile-menu {
        display: none;
      }
    }
  </style>
</head>
<body class="font-sans bg-gray-100">

<!-- Navbar -->
<header class="bg-white shadow-md sticky top-0 z-50">
  <div class="container mx-auto px-6 py-4 flex justify-between items-center">
    <!-- Logo and Tabs -->
    <div class="flex items-center space-x-6">
      <h1 class="font-semibold text-2xl text-gray-800"><a href="index.php">Eventify</a></h1>
      <nav class="desktop-menu">
        <ul class="flex space-x-4">
          <li><a href="index.php" class="text-gray-600 hover:text-gray-900">Home</a></li>
          <li><a href="events.php" class="text-gray-600 hover:text-gray-900">Events</a></li>
          <li><a href="about.php" class="text-gray-600 hover:text-gray-900">About us </a></li>
        </ul>
      </nav>
    </div>
    <!-- Sign In Button and sign up -->
    <div class="flex items-center space-x-4">
      <button class="hidden sm:block px-4 py-2 bg-blue-500  text-white rounded-full font-semibold hover:bg-indigo-700 transition duration-300">
        <a href="login.php">Sign In</a>
      </button>
      <button class="hidden sm:block px-4 py-2 bg-blue-500  text-white rounded-full font-semibold hover:bg-indigo-700 transition duration-300">
        <a href="signup.php">Create account</a>
      </button>
      <button class="mobile-menu-button sm:hidden">
        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path fill-rule="evenodd" clip-rule="evenodd" d="M4 6C4 5.44772 4.44772 5 5 5H19C19.5523 5 20 5.44772 20 6C20 6.55228 19.5523 7 19 7H5C4.44772 7 4 6.55228 4 6ZM4 11C4 10.4477 4.44772 10 5 10H19C19.5523 10 20 10.4477 20 11C20 11.5523 19.5523 12 19 12H5C4.44772 12 4 11.5523 4 11ZM5 17C4.44772 17 4 17.4477 4 18C4 18.5523 4.44772 19 5 19H19C19.5523 19 20 18.5523 20 18C20 17.4477 19.5523 17 19 17H5Z" fill="currentColor"/>
        </svg>
      </button>
    </div>
  </div>
  <div class="mobile-menu">
    <ul>
      <li><a href="index.php" class="block px-4 py-2 text-gray-600 hover:text-gray-900">Home</a></li>

      <li><a href="events.php" class="block px-4 py-2 text-gray-600 hover:text-gray-900">Events</a></li>
      <li><a href="about.php" class="block px-4 py-2 text-gray-600 hover:text-gray-900">About</a></li>
    </ul>
  </div>
</header>

<script>
  document.querySelector('.mobile-menu-button').addEventListener('click', function() {
    var menu = document.querySelector('.mobile-menu');
    menu.classList.toggle('active');
  });
</script>

<!-- Main Content -->
<main class="container mx-auto px-6 py-8">

  <!-- Hero Section -->
  <header class="bg-gray-50 py-10 sm:py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="text-center">
        <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-gray-900">
         <span class="text-indigo-600"></span>
        </h1>
        <p class="mt-4 text-xl sm:text-2xl md:text-3xl lg:text-4xl text-gray-700">
        </p>
        <div class="mt-8">
          <!-- <a href="events.php" class="text-xl sm:text-2xl lg:text-3xl bg-blue-500 hover:bg-indigo-700 px-6 py-3 sm:px-8 sm:py-4 rounded-full inline-block font-semibold text-center text-white shadow-lg transition duration-300 ease-in-out"></a> -->
        </div>
      </div>
    </div>
  </header>
<body class="font-sans bg-gray-100">

<!-- Main Content -->
<main class="container mx-auto">

  <!-- About Section -->
  <section class="about-section">
    <div class="background-decor"></div>
    <div class="content-wrapper">
      <div class="about-content">
        <h2 class="about-title">About Us</h2>
        <p class="about-description">Welcome to Eventify, your one-stop destination to discover and attend exciting events near you. Our mission is to connect event organizers with enthusiastic attendees, creating memorable experiences for all.</p>
        <p class="about-description">At Eventify, we believe in the power of community and the magic of live events. Whether you're passionate about music, art, food, or sports, you'll find something to love on our platform. Join us in celebrating the diversity of human interests and the joy of coming together.</p>
        <p class="about-description">Our team is dedicated to providing a seamless experience for both event organizers and attendees. We strive to make event planning effortless and event discovery delightful. Let's make every moment count, one event at a time.</p>
      </div>

      <!-- Comment Box -->
      <div class="comment-box">
        <div class="bouncing-ball left"></div>
        <div class="bouncing-ball right"></div>
        <div class="bouncing-ball bottom-left"></div>
        <div class="bouncing-ball bottom-right"></div>
        <h3 class="comment-title">Leave a Comment</h3>
        <?php if (isset($success_message)): ?>
        <p class="text-green-500 text-xs italic mb-4"><?php echo $success_message; ?></p>
        <?php endif; ?>
        <?php if (isset($error_message)): ?>
        <p class="text-red-500 text-xs italic mb-4"><?php echo $error_message; ?></p>
        <?php endif; ?>
        <form class="comment-form" method="POST" action="">
          <label for="name">Name:</label>
          <input type="text" id="name" name="name">
          <label for="email">Email:</label>
          <input type="email" id="email" name="email">
          <label for="comment">Comment:</label>
          <textarea id="comment" name="comment" required></textarea>
          <button type="submit">Submit</button>
        </form>
      </div>
    </div>
  </section>

</main>

</body>
</html>
