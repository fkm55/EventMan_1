<?php
include('includes/dbconnection.php');

// Retrieve the three upcoming events from the database
$sql = "SELECT * FROM events WHERE date >= CURDATE() ORDER BY date LIMIT 3";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><a href="index.html">Eventify</a></title>
  <link href="./output.css" rel="stylesheet">
     <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet"> 

  <style>
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
<header class="bg-white shadow-md">
  <nav class="bg-white shadow-md text-white py-8 px-8 sticky top-0 z-50"></nav>
  <div class="container mx-auto px-6 py-4 flex justify-between items-center sticky top-0">
    <!-- Logo and Tabs -->
    <div class="flex items-center space-x-6">
      <h1 class="font-semibold text-xl text-gray-800"><a href="index.html">Eventify</a></h1>
      <nav class="desktop-menu">
        <ul class="flex space-x-4">
          <li><a href="index.php" class="text-gray-600 hover:text-gray-900">Home    </a></li>
          <li><a href="events.php" class="text-gray-600 hover:text-gray-900">Events   </a></li>
          <li><a href="about.php" class="text-gray-600 hover:text-gray-900">About    </a></li>
          <!-- Add more tabs as needed -->
        </ul>
      </nav>
    </div>
    <!-- Sign In Button and sign up -->
    <div class="flex items-center space-x-4">
      <button  class="hidden sm:block px-4 py-2 bg-blue-500 text-white rounded-md font-semibold hover:bg-blue-600 transition duration-300"><a href="login.html">Sign In</a></button>
      <button  class="hidden sm:block px-4 py-2 bg-blue-500 text-white rounded-md font-semibold hover:bg-blue-600 transition duration-300"><a href="signup.html">Create account</a></button>
      <button class="mobile-menu-button sm:hidden">
        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path fill-rule="evenodd" clip-rule="evenodd" d="M4 6C4 5.44772 4.44772 5 5 5H19C19.5523 5 20 5.44772 20 6C20 6.55228 19.5523 7 19 7H5C4.44772 7 4 6.55228 4 6ZM4 11C4 10.4477 4.44772 10 5 10H19C19.5523 10 20 10.4477 20 11C20 11.5523 19.5523 12 19 12H5C4.44772 12 4 11.5523 4 11ZM5 17C4.44772 17 4 17.4477 4 18C4 18.5523 4.44772 19 5 19H19C19.5523 19 20 18.5523 20 18C20 17.4477 19.5523 17 19 17H5Z" fill="currentColor"/>
        </svg>
      </button>
    </div>
  </div>
  <div class="mobile-menu">
    <ul>
      <li><a href="index.html" class="block px-4 py-2 text-gray-600 hover:text-gray-900">Home    </a></li>
      <li><a href="events.html" class="block px-4 py-2 text-gray-600 hover:text-gray-900">Events   </a></li>
      <li><a href="about.html" class="block px-4 py-2 text-gray-600 hover:text-gray-900">About    </a></li>
    </ul>
  </div>
</header>

<script>
  document.querySelector('.mobile-menu-button').addEventListener('click', function() {
    var menu = document.querySelector('.mobile-menu');
    menu.classList.toggle('active');
  });
</script>

</body>
</html>
  <!-- Main Content -->
  <main class="container mx-auto px-6 py-8">

    <!-- Hero Section -->
    <header class="bg-gray-50 py-10 sm:py-20">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="text-center">
      <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-gray-900">
        Welcome to <span class="text-indigo-600">Eventify</span>
      </h1>
      <p class="mt-4 text-lg sm:text-xl md:text-2xl lg:text-3xl text-gray-700">
        Discover and attend exciting events near you.
      </p>
      <div class="mt-8">
        <a href="events.html" class="text-xl sm:text-2xl lg:text-3xl bg-white hover:bg-indigo-100 px-6 py-3 sm:px-8 sm:py-4 rounded-full inline-block font-semibold text-center shadow-md transition duration-300 ease-in-out">Explore Events</a>
      </div>
    </div>
  </div>
</header>


  <!-- Event Listing Section -->
<section class="mb-12">
    <h2 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-gray-800 mb-6">Upcoming Events</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
        <!-- Display upcoming events dynamically -->
        <?php
        if ($result && $result->rowCount() > 0) {
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                ?>
                <div class="bg-white shadow-lg rounded-lg overflow-hidden transition-transform transform-gpu hover:translate-y-1 hover:bg-blue-100">
                    <img src="<?php echo $row['event_image']; ?>" alt="Event" class="w-full h-64 object-cover object-center">
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-gray-800 mb-2"><?php echo $row['title']; ?></h3>
                        <p class="text-gray-700 leading-relaxed mb-4"><?php echo $row['description']; ?></p>
                        <p class="text-gray-600">Date: <?php echo $row['date']; ?></p>
                        <p class="text-gray-600">Location: <?php echo $row['location']; ?></p>
                        <a href="#" class="block mt-4 bg-blue-500 text-white px-4 py-2 rounded-md font-semibold text-center hover:bg-blue-600 transition duration-300">Register</a>
                    </div>
                </div>
                <?php
            }
        } else {
            echo "No upcoming events.";
        }
        ?>
    </div>
</section>

    

    <!-- Registration Form Section -->
    <section class="bg-gray-100 py-12 px-6 rounded-lg">
  <div class="max-w-md mx-auto">
    <h2 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-gray-800 mb-6">Register for an Event</h2>
    <form>
      <div class="mb-4">
        <label for="name" class="block text-sm font-semibold text-gray-600">Name</label>
        <input type="text" id="name" name="name" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 focus:outline-none focus:border-blue-500" placeholder="Enter your name">
      </div>
      <div class="mb-4">
        <label for="email" class="block text-sm font-semibold text-gray-600">Email</label>
        <input type="email" id="email" name="email" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 focus:outline-none focus:border-blue-500" placeholder="Enter your email">
      </div>
      <div class="mb-4">
        <label for="password" class="block text-sm font-semibold text-gray-600">Password</label>
        <input type="password" id="password" name="password" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 focus:outline-none focus:border-blue-500" placeholder="Enter your password">
      </div>
      <div class="mb-4">
        <label for="confirm-password" class="block text-sm font-semibold text-gray-600">Confirm Password</label>
        <input type="password" id="confirm-password" name="confirm-password" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 focus:outline-none focus:border-blue-500" placeholder="Confirm your password">
      </div>
      <!-- Add more form fields as needed -->
      <button type="submit" class="inline-block w-full bg-blue-500 text-white px-4 py-3 rounded-md font-semibold text-center hover:bg-blue-600 transition duration-300">Register</button>
    </form>
  </div>
</section>

</main>
<!-- Footer -->
<footer class="bg-gray-800 text-white text-center py-6">
  <p>&copy; 2024 Event Management System. All rights reserved.</p>
</footer>

 

</body>
</html>

<?php
// Close database connection
$conn = null;
?>