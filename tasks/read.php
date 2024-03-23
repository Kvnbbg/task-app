<?php

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

// Fetch tasks from the database
$sql = "SELECT * FROM tasks";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Output data of each row
    while ($row = $result->fetch_assoc()) {
        echo "Task ID: " . $row["id"] . "<br>";
        echo "Task Name: " . $row["name"] . "<br>";
        echo "Task Description: " . $row["description"] . "<br>";
        echo "<br>";
    }
} else {
    echo "No tasks found.";
}

// Close the database connection
$conn->close();

?>
