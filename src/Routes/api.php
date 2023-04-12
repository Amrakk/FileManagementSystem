<?php
    echo "api";
    // receive the request
    // validate the request
    // send the request to the controller
    // receive the response from the controller
    // send the response back to the client
?>


















<!-- tmp -->


<?php

// Define a function for handling the home page
function home_page() {
    echo "Welcome to the home page!";
}

// Define a function for handling the about page
function about_page() {
    echo "This is the about page.";
}

// Define a function for handling a user profile page
function user_profile($username) {
    echo "This is the profile page for user $username.";
}

// Define an array of valid routes and their corresponding functions
$routes = array(
    '/' => 'home_page',
    '/about' => 'about_page',
    '/users/:username' => 'user_profile'
);

// Get the current request URL
$request_url = $_SERVER['REQUEST_URI'];

// Remove query string from the URL
$request_url = strtok($request_url, '?');

// Find the matching route for the request URL
foreach ($routes as $route => $function) {
    // Replace any URL parameters with a regex pattern
    $route_pattern = preg_replace('/:[^\/]+/', '([^\/]+)', $route);

    // Check if the request URL matches the route pattern
    if (preg_match('#^' . $route_pattern . '$#', $request_url, $matches)) {
        // Call the corresponding function with any URL parameters
        array_shift($matches);
        call_user_func_array($function, $matches);
        exit();
    }
}

// If no matching route was found, return a 404 error
header("HTTP/1.0 404 Not Found");
echo "Page not found.";
