<?php
include('includes/dbconnection.php');

// Initialize query components
$searchQuery = "";
$filterQuery = "";
$orderQuery = "ORDER BY date DESC, time DESC";

// Check if the search form is submitted
if (isset($_GET['search'])) {
    $search = $_GET['search'];
    $searchQuery = "WHERE title LIKE '%$search%' OR description LIKE '%$search%'";
}

// Check if filters are applied
if (isset($_GET['category']) && $_GET['category'] != "") {
    $category = $_GET['category'];
    $filterQuery .= " AND category='$category'";
}

if (isset($_GET['location']) && $_GET['location'] != "") {
    $location = $_GET['location'];
    $filterQuery .= " AND location='$location'";
}

// Combine queries
if ($searchQuery != "") {
    $filterQuery = substr($filterQuery, 4); // Remove the initial " AND"
    $sql = "SELECT * FROM events $searchQuery $filterQuery $orderQuery";
} else {
    $sql = "SELECT * FROM events WHERE 1=1 $filterQuery $orderQuery";
}

$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Events</title>
  <link href="./output.css" rel="stylesheet">
  <style>
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
    <div class="flex items-center space-x-6">
      <h1 class="font-semibold text-xl text-gray-800"><a href="index.html">Eventify</a></h1>
      <nav class="desktop-menu">
        <ul class="flex space-x-4">
          <li><a href="index.php" class="text-gray-600 hover:text-gray-900">Home</a></li>
          <li><a href="events.php" class="text-gray-600 hover:text-gray-900">Events</a></li>
          <li><a href="about.php" class="text-gray-600 hover:text-gray-900">About</a></li>
        </ul>
      </nav>
    </div>
    <div class="flex items-center space-x-4">
        <button class="hidden sm:block px-4 py-2 bg-blue-500 text-white rounded-md font-semibold hover:bg-blue-600 transition duration-300">
          <a href="login.html">Sign In</a>
        </button>
        <button class="hidden sm:block px-4 py-2 bg-blue-500 text-white rounded-md font-semibold hover:bg-blue-600 transition duration-300">
          <a href="signup.html">Create Account</a>
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
        <li><a href="index.html" class="block px-4 py-2 text-gray-600 hover:text-gray-900">Home</a></li>
        <li><a href="events.html" class="block px-4 py-2 text-gray-600 hover:text-gray-900">Events</a></li>
        <li><a href="about.html" class="block px-4 py-2 text-gray-600 hover:text-gray-900">About</a></li>
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
    <!-- Search and Filters -->
    <br><br>
    <div class="flex justify-between items-center mb-8">
        <form method="GET" action="events.php" class="flex space-x-2">
            <input type="text" name="search" placeholder="Search by name or description" class="border border-gray-300 rounded-md py-2 px-4 focus:outline-none">
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-md font-semibold hover:bg-blue-600 transition duration-300">Search</button>
        </form>
        <div class="flex space-x-2">
            <form method="GET" action="events.php" class="flex space-x-2">
                <select name="category" class="border border-gray-300 rounded-md py-2 px-4 focus:outline-none">
                    <option value="">All Categories</option>
                    <option value="music">Music</option>
                    <option value="conference">Conference</option>
                </select>
                <select name="location" class="border border-gray-300 rounded-md py-2 px-4 focus:outline-none">
                    <option value="">All Locations</option>
                    <option value="addis-ababa">Addis Ababa</option>
                    <option value="arbaminch">Arbaminch</option>
                </select>
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-md font-semibold hover:bg-blue-600 transition duration-300">Filter</button>
            </form>
        </div>
    </div>
    <br>
    <!-- Event Listing Section -->
    <section class="event-listing">
        <div class="container">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php
                if ($result && $result->rowCount() > 0) {
                    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                        $spaceAvailable = $row['space_available'];
                        $capacityMessage = "";
                        if ($spaceAvailable == 0) {
                            $capacityMessage = "<span class='text-red-500'>Sold Out</span>";
                        } elseif ($spaceAvailable < 10) {
                            $capacityMessage = "<span class='text-yellow-500'>Hurry, only $spaceAvailable spaces left!</span>";
                        }
                        ?>
                        <div class="bg-white shadow-lg rounded-lg overflow-hidden">
                            <img src="<?php echo $row['event_image']; ?>" alt="Event" class="w-full h-64 object-cover object-center">
                            <div class="p-6">
                                <h3 class="text-xl font-bold text-gray-800 mb-2"><?php echo $row['title']; ?></h3>
                                <p class="text-gray-700 leading-relaxed mb-4"><?php echo $row['description']; ?></p>
                                <p class="text-gray-600">Date: <?php echo $row['date']; ?> <?php echo $row['time']; ?></p>
                                <p class="text-gray-600">Location: <?php echo $row['location']; ?></p>
                                <p class="text-gray-600"><?php echo $capacityMessage; ?></p>
                                <a href="#" onclick="showSignInPrompt()" class="block mt-4 bg-blue-500 text-white px-4 py-2 rounded-md font-semibold text-center hover:bg-blue-600 transition duration-300">Register</a>
                            </div>
                        </div>
                        <?php
                    }
                } else {
                    echo "No events found.";
                }
                ?>
            </div>
        </div>
    </section>
  </main>
  
  <!-- Footer -->
  <footer class="bg-gray-800 text-white text-center py-6">
    <p>&copy; 2024 Event Management System. All rights reserved.</p>
  </footer>

  <!-- Sign In Prompt Script -->
  <script>
    function showSignInPrompt() {
      const userConfirmed = confirm("Please sign in or create an account first.\nClick OK to Sign In, Cancel to Create an Account.");
      if (userConfirmed) {
        window.location.href = 'login.php';
      } else {
        window.location.href = 'signup.php';
      }
    }
  </script>
</body>
</html>
<?php
// Close database connection
$conn = null;
?>
