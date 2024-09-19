<?php
session_start();
// DB credentials.
define('DB_HOST','localhost');
define('DB_USER','root');
define('DB_PASS','');
define('DB_NAME','event_management');

try {
    // Establish database connection.
    $conn = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASS);
    // Set the PDO error mode to exception.
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    // Display error message if connection fails.
    echo "Connection failed: " . $e->getMessage();
    die(); // Terminate script execution.
}

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
      <h1 class="font-semibold text-2xl text-gray-800"><a href="index.html">Eventify</a></h1>
      <nav class="desktop-menu">
        <ul class="flex space-x-4">
          <li><a href="index.html" class="text-gray-600 hover:text-gray-900">Home</a></li>
          <li><a href="events.html" class="text-gray-600 hover:text-gray-900">Events</a></li>
          <li><a href="aboutus.html" class="text-gray-600 hover:text-gray-900">About us </a></li>
        </ul>
      </nav>
    </div>
    <!-- Sign In Button and sign up -->
    <div class="flex items-center space-x-4">
      <button class="hidden sm:block px-4 py-2 bg-blue-500  text-white rounded-full font-semibold hover:bg-indigo-700 transition duration-300">
        <a href="login.html">Sign In</a>
      </button>
      <button class="hidden sm:block px-4 py-2 bg-blue-500  text-white rounded-full font-semibold hover:bg-indigo-700 transition duration-300">
        <a href="signup.html">Create account</a>
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

  <!-- Hero Section -->
  <header class="bg-gray-50 py-10 sm:py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="text-center">
        <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-gray-900">
          Welcome to <span class="text-indigo-600">Eventify</span>
        </h1>
        <p class="mt-4 text-xl sm:text-2xl md:text-3xl lg:text-4xl text-gray-700">
          Discover and attend exciting events near you.
        </p>
        <div class="mt-8">
          <a href="events.html" class="text-xl sm:text-2xl lg:text-3xl bg-blue-500 hover:bg-indigo-700 px-6 py-3 sm:px-8 sm:py-4 rounded-full inline-block font-semibold text-center text-white shadow-lg transition duration-300 ease-in-out">Explore Events</a>
        </div>
      </div>
    </div>
  </header>

  <!-- Event Listing Section -->
  <section class="mb-12">
    <h2 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-gray-800 mb-8">Upcoming Events</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
      <!-- Event Card -->
      <div class="bg-white shadow-lg rounded-lg overflow-hidden transform hover:scale-105 transition-transform duration-300">
        <img src="https://via.placeholder.com/600x400" alt="Event" class="w-full h-64 object-cover object-center">
        <div class="p-6">
          <h3 class="text-xl font-bold text-gray-800 mb-2">Event Name</h3>
          <p class="text-gray-700 leading-relaxed mb-4">Event Description</p>
          <p class="text-gray-600">Date: Event Date</p>
          <p class="text-gray-600">Location: Event Location</p>
            <a href="#" class="block mt-4 bg-blue-500 text-white px-4 py-2 rounded-md font-semibold text-center hover:bg-indigo-700 transition duration-300">Register</a>
        </div>
      </div>
      <!-- Add more event cards here -->
      <div class="bg-white shadow-lg rounded-lg overflow-hidden transform hover:scale-105 transition-transform duration-300">
        <img src="https://via.placeholder.com/600x400" alt="Event" class="w-full h-64 object-cover object-center">
        <div class="p-6">
          <h3 class="text-xl font-bold text-gray-800 mb-2">Event Name 1</h3>
          <p class="text-gray-700 leading-relaxed mb-4">Event Description 1</p>
          <p class="text-gray-600">Date: Event Date 1</p>
          <p class="text-gray-600">Location: Event Location 1</p>
          <a href="#" class="block mt-4 bg-blue-500 text-white px-4 py-2 rounded-md font-semibold text-center hover:bg-indigo-700 transition duration-300">Register</a>
        </div>
      </div>
      <div class="bg-white shadow-lg rounded-lg overflow-hidden transform hover:scale-105 transition-transform duration-300">
        <img src="https://via.placeholder.com/600x400" alt="Event" class="w-full h-64 object-cover object-center">
        <div class="p-6">
          <h3 class="text-xl font-bold text-gray-800 mb-2">Event Name 2</h3>
          <p class="text-gray-700 leading-relaxed mb-4">Event Description 2</p>
          <p class="text-gray-600">Date: Event Date 2</p>
          <p class="text-gray-600">Location: Event Location 2</p>
           <a href="#" class="block mt-4 bg-blue-500 text-white px-4 py-2 rounded-md font-semibold 
		   text-center hover:bg-indigo-700 transition duration-300">Register</a>  
        </div>
      </div>
    </div>
    
  </section>
<!-- Past Events Section -->
<section class="mb-12">
  <h2 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-gray-800 mb-8 text-center">Memorable Events We Hosted</h2>
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
    <!-- Past Event Card 1 -->
    <div class="bg-gradient-to-r from-purple-400 via-pink-500 to-red-500 rounded-lg overflow-hidden shadow-xl transform hover:scale-105 transition-transform duration-300">
      <img src="https://i.ytimg.com/vi/lpAqq9UE_6Q/maxresdefault.jpg" alt="Past Event 1" class="w-full h-40 object-cover">
      <div class="p-4 bg-white bg-opacity-80 backdrop-blur-md">
        <h3 class="text-lg font-semibold text-gray-800 mb-2">Lij Mike Concert</h3>
        <p class="text-gray-700 mb-3">An electrifying performance by Lij Mike.</p>
        <p class="text-gray-600 text-sm"><strong>Date:</strong> Date 1</p>
        <p class="text-gray-600 text-sm"><strong>Location:</strong> Location 1</p>
      </div>
    </div>
    <!-- Past Event Card 2 -->
    <div class="bg-gradient-to-r from-green-400 via-blue-500 to-purple-500 rounded-lg overflow-hidden shadow-xl transform hover:scale-105 transition-transform duration-300">
      <img src="https://i.ytimg.com/vi/18BgrpHKDWw/maxresdefault.jpg" alt="Past Event 2" class="w-full h-40 object-cover">
     <div class="p-4 bg-white bg-opacity-80 backdrop-blur-md">
        <h3 class="text-lg font-semibold text-gray-800 mb-2">Teddy Afro Concert</h3>
        <p class="text-gray-700 mb-3">A mesmerizing performance by Teddy Afro in Addis Ababa.</p>
        <p class="text-gray-600 text-sm"><strong>Date:</strong>20/09/2015 E.C</p>
        <p class="text-gray-600 text-sm"><strong>Location:</strong>ADDIS ABABA</p>
      </div>
    </div>
    <!-- Past Event Card 3 -->
    <div class="bg-gradient-to-r from-yellow-400 via-orange-500 to-red-500 rounded-lg overflow-hidden shadow-xl transform hover:scale-105 transition-transform duration-300">
      <img src="https://cdn-az.allevents.in/events5/banners/b08eead1b906e7049161a068baaa1d74932d8d9d25a8afc5b4916b58b8c62e61-rimg-w1200-h600-gmir.jpg?v=1700177481" alt="Past Event 3" class="w-full h-40 object-cover">
      <div class="p-4 bg-white bg-opacity-80 backdrop-blur-md">
        <h3 class="text-lg font-semibold text-gray-800 mb-2">Venorica Adane Concert</h3>
        <p class="text-gray-700 mb-3">Experience the magic of Venorica Adane's performance in America.</p>
        <p class="text-gray-600 text-sm"><strong>Date:</strong> Date 3</p>
        <p class="text-gray-600 text-sm"><strong>Location:</strong> Location 3</p>
      </div>
    </div>
  </div>
</section>

 <!-- Concert Highlights Section -->
    <section class="mb-12">
      <h2 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-gray-800 mb-8 text-center">Concert Highlights</h2>
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
        <!-- Concert Highlight Video -->
        <div class="bg-white rounded-lg overflow-hidden shadow-xl transform hover:scale-105 transition-transform duration-300">
          <div class="aspect-w-16 aspect-h-9">
            <iframe class="w-full h-full" src="https://www.youtube.com/embed/oNbc351sadM" frameborder="0" allowfullscreen></iframe>
          </div>
        </div>
        <div class="bg-white rounded-lg overflow-hidden shadow-xl transform hover:scale-105 transition-transform duration-300">
          <div class="aspect-w-16 aspect-h-9">
            <iframe class="w-full h-full" src="https://www.youtube.com/embed/m7MQ83iqPTc" frameborder="0" allowfullscreen></iframe>
          </div>
        </div>
        <div class="bg-white rounded-lg overflow-hidden shadow-xl transform hover:scale-105 transition-transform duration-300">
          <div class="aspect-w-16 aspect-h-9">
            <iframe class="w-full h-full" src="https://www.youtube.com/embed/m53O_nlooHQ" frameborder="0" allowfullscreen></iframe>
          </div>
        </div>
      </div>
	  
	  <div class="fixed bottom-6 right-6">
       <h2><a href="events.html" class="bg-blue-500 hover:bg-indigo-700 text-white py-3 px-6 rounded-full shadow-lg font-semibold transition duration-300">
        More Events
      </a></h2>
    </div>
    </section>
	
	<!-- Testimonials Section -->
<section class="py-12 bg-gradient-to-r from-purple-500 to-blue-500">
  <h2 class="text-4xl sm:text-5xl lg:text-6xl font-bold text-white mb-12 text-center">What Our Users Say About</h2>
  <div class="relative overflow-hidden">
    <div class="testimonials-wrapper grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-12 transition-transform duration-1000 ease-in-out">
      <!-- Testimonial 1 -->
      <div class="testimonial bg-white p-8 rounded-lg shadow-lg text-center">
        <p class="text-gray-800 italic mb-6">"Eventify made it so easy to find events that I’m interested in. Highly recommend!"</p>
        <h3 class="text-lg font-semibold text-purple-500">Abebe Alemu</h3>
      </div>
      <!-- Testimonial 2 -->
      <div class="testimonial bg-white p-8 rounded-lg shadow-lg text-center">
        <p class="text-gray-800 italic mb-6">"I’ve discovered so many new activities through Eventify. It’s a fantastic platform!"</p>
        <h3 class="text-lg font-semibold text-blue-500">Gadisa Yesufe</h3>
      </div>
      <!-- Testimonial 3 -->
      <div class="testimonial bg-white p-8 rounded-lg shadow-lg text-center">
        <p class="text-gray-800 italic mb-6">"The best way to stay updated on events in my city. Eventify is amazing!"</p>
        <h3 class="text-lg font-semibold text-green-500">Betty Asmeraw</h3>
      </div>
      <!-- Additional Testimonials -->
      <div class="testimonial bg-white p-8 rounded-lg shadow-lg text-center">
        <p class="text-gray-800 italic mb-6">"Eventify has changed the way I explore my city. So many exciting events to discover!"</p>
        <h3 class="text-lg font-semibold text-purple-500">Daniel Tadesse</h3>
      </div>
      <div class="testimonial bg-white p-8 rounded-lg shadow-lg text-center">
        <p class="text-gray-800 italic mb-6">"I love using Eventify to plan my weekends. It's convenient and fun!"</p>
        <h3 class="text-lg font-semibold text-blue-500">Hirut Woldemariam</h3>
      </div>
      <div class="testimonial bg-white p-8 rounded-lg shadow-lg text-center">
        <p class="text-gray-800 italic mb-6">"Eventify's recommendations are always spot on. I never miss out on any event!"</p>
        <h3 class="text-lg font-semibold text-green-500">Temesgen Gebre</h3>
      </div>
      <!-- Add more testimonials here if needed -->
    </div>
  </div>
</section>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    const testimonialsWrapper = document.querySelector('.testimonials-wrapper');
    const testimonials = document.querySelectorAll('.testimonial');
    let currentTestimonialIndex = 0;
    const testimonialCount = testimonials.length;

    function showTestimonial(index) {
      testimonialsWrapper.style.transform = `translateX(-${index * 33.3333}%)`;
    }

    function rotateTestimonials() {
      currentTestimonialIndex = (currentTestimonialIndex + 1) % Math.ceil(testimonialCount / 3);
      showTestimonial(currentTestimonialIndex);
    }

    showTestimonial(currentTestimonialIndex); // Display initial testimonials
    setInterval(rotateTestimonials, 6000); // Change testimonial every 6 seconds
  });
</script>

<style>
  .testimonials-wrapper {
    display: flex;
    transition: transform 1s ease-in-out; /* Slower transition */
  }
  .testimonial {
    min-width: 33.3333%;
  }
</style>


  
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

