<?php

// Set the appropriate headers for CORS
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

// Define the error details
$error = [
    "code" => 500,
    "message" => "Internal Server Error",
    "details" => "An unexpected error occurred."
];

// Convert the error details to JSON
$errorJson = json_encode($error);

// Output the JSON response
echo $errorJson;