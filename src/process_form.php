<?php

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data
    $name = $_POST['name'];
    $email = $_POST['email'];
    $message = $_POST['message'];

    // Validate form data
    if (empty($name) || empty($email) || empty($message)) {
        header('Location: error.php');
        exit;
    }

    // Add additional validation logic here if needed
    // For example, you can check if the email is valid
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header('Location: error.php');
        exit;
    }

    // Process form data
    // TODO: Add your processing logic here
    // For example, you can save the data to a database
    // or send an email
    // Connect to the database
    $servername = "localhost";
    $username = "your_username";
    $password = "your_password";
    $dbname = "your_database";

    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Prepare and execute the SQL statement
    $stmt = $conn->prepare("INSERT INTO your_table (name, email, message) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $email, $message);
    $stmt->execute();

    // Close the connection
    $stmt->close();
    $conn->close();

    // Redirect to a success page
    header('Location: success.php');
    exit;
}

// If the form is not submitted, redirect to an error page
header('Location: error.php');
exit;