<?php
// Define the routes as an associative array
$routes = [
    '/' => 'HomeController@index',
    '/login' => 'AuthController@login',
    '/logout' => 'AuthController@logout',
    '/users' => 'UserController@index',
    '/users/create' => 'UserController@create',
    '/users/store' => 'UserController@store',
    '/users/{id}/edit' => 'UserController@edit',
    '/users/{id}/update' => 'UserController@update',
    '/users/{id}/delete' => 'UserController@delete',
];

// Parse the current URL and look for a matching route
$request_uri = explode('?', $_SERVER['REQUEST_URI'], 2)[0];
$route_found = false;
foreach ($routes as $route => $handler) {
    // Replace any curly brace placeholders with a regex pattern
    $route_regex = str_replace('/', '\/', $route);
    $route_regex = preg_replace('/{[^\/]+}/', '([^\/]+)', $route_regex);

    // Check if the request URI matches the route pattern
    if (preg_match('/^' . $route_regex . '$/', $request_uri, $matches)) {
        // Extract any parameter values from the URL
        $params = [];
        if (preg_match_all('/{([^\/]+)}/', $route, $param_names)) {
            foreach ($param_names[1] as $param_name) {
                $params[$param_name] = $matches[array_search($param_name, $param_names[1]) + 1];
            }
        }

        // Extract the controller and method names from the handler string
        list($controller_name, $method_name) = explode('@', $handler);

        // Instantiate the controller object and call the method with any parameters
        require_once __DIR__ . '/../src/controllers/' . $controller_name . '.php';
        $controller = new $controller_name();
        call_user_func_array([$controller, $method_name], $params);

        // Stop processing further routes once a match is found
        $route_found = true;
        break;
    }
}

// If no matching route was found, return a 404 error page
if (!$route_found) {
    http_response_code(404);
    echo '<h1>404 Not Found</h1>';
}
?>

