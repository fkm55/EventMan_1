<?php
session_start();
include('includes/dbconnection.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

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

    .modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(0,0,0,0.4);
  }
  </style>
</head>
<body class="font-sans bg-gray-100">
     <!-- Navbar -->
    <header class="bg-white shadow-md">
        <div class="container mx-auto px-6 py-4 flex justify-between items-center">
            <div class="flex items-center space-x-6">
                <h1 class="font-semibold text-xl text-gray-800"><a href="index.html">Eventify</a></h1>
                <nav>
                    <ul class="flex space-x-4">
                        <li><a href="user.php" class="text-gray-600 hover:text-gray-900">Dashboard</a></li>
                        <li><a href="uevents.php" class="text-gray-600 hover:text-gray-900">Events</a></li>
                        <li><a href="about.html" class="text-gray-600 hover:text-gray-900">About</a></li>
                    </ul>
                </nav>
            </div>
            <div class="flex items-center space-x-4">
                <span class="text-gray-600">Welcome, <?php echo htmlspecialchars($username); ?>!</span>
                <button class="px-6 py-2 bg-blue-500 text-white rounded-md font-semibold hover:bg-blue-600 transition duration-300"><a href="login.php">Logout</a></button>
            </div>
        </div>
    </header>


  <!-- Main Content -->
  <main class="container mx-auto px-6 py-8">
    <!-- Search and Filters -->
    
    <div class="flex justify-between items-center mb-8">
        <form method="GET" action="uevents.php" class="flex space-x-2">
            <input type="text" name="search" placeholder="Search by name or description" class="border border-gray-300 rounded-md py-2 px-4 focus:outline-none">
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-md font-semibold hover:bg-blue-600 transition duration-300">Search</button>
        </form>
        <div class="flex space-x-2">
            <form method="GET" action="uevents.php" class="flex space-x-2">
                <select name="category" class="border border-gray-300 rounded-md py-2 px-4 focus:outline-none">
                    <option value="">All Categories</option>
                    <option value="music">Music</option>
                    <option value="conference">Conference</option>
                    <!-- Add more categories as needed -->
                </select>
                <select name="location" class="border border-gray-300 rounded-md py-2 px-4 focus:outline-none">
                    <option value="">All Locations</option>
                    <option value="addis-ababa">Addis Ababa</option>
                    <option value="arbaminch">Arbaminch</option>
                    <!-- Add more locations as needed -->
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
                            <img src="<?php echo htmlspecialchars($row['event_image']); ?>" alt="Event" class="w-full h-64 object-cover object-center">
                            <div class="p-6">
                                <h3 class="text-xl font-bold text-gray-800 mb-2"><?php echo htmlspecialchars($row['title']); ?></h3>
                                <p class="text-gray-700 leading-relaxed mb-4"><?php echo htmlspecialchars($row['description']); ?></p>
                                <p class="text-gray-600">Date: <?php echo htmlspecialchars($row['date']); ?> <?php echo htmlspecialchars($row['time']); ?></p>
                                <p class="text-gray-600">Location: <?php echo htmlspecialchars($row['location']); ?></p>
                                <p class="text-gray-600"><?php echo $capacityMessage; ?></p>
                                <a href="#" onclick="openModal(<?php echo htmlspecialchars($row['event_id']); ?>)" class="block mt-4 bg-blue-500 text-white px-4 py-2 rounded-md font-semibold text-center hover:bg-blue-600 transition duration-300">Register</a>
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

    <!-- Add this HTML for the modal -->
<div id="registerModal" class="modal flex items-center justify-center">
  <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
    <span class="close float-right text-gray-500 hover:text-gray-700 cursor-pointer">&times;</span>
    <form id="registerForm" action="register_ticket.php" method="POST" class="space-y-4">
      <h2 class="text-2xl font-semibold mb-4">Register for Event</h2>
      <input type="hidden" name="event_id" id="event_id" value="">
      <div>
        <label for="full_name" class="block text-gray-700">Full Name</label>
        <input type="text" id="full_name" name="full_name" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
      </div>
      <div>
        <label for="dob" class="block text-gray-700">Date of Birth</label>
        <input type="date" id="dob" name="dob" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
      </div>
      <button type="submit" class="w-full bg-blue-500 text-white py-2 px-4 rounded-md font-semibold hover:bg-blue-600 transition duration-300">Submit</button>
    </form>
  </div>
</div>

<script>
  // Get the modal
  var modal = document.getElementById("registerModal");
  var span = document.getElementsByClassName("close")[0];

  // Open modal function
  function openModal(eventId) {
    modal.style.display = "block";
    document.getElementById("event_id").value = eventId;
  }

  // Close modal function
  span.onclick = function() {
    modal.style.display = "none";
  }

  window.onclick = function(event) {
    if (event.target == modal) {
      modal.style.display = "none";
    }
  }
</script>
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
