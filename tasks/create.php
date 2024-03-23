<?php

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data
    $taskName = $_POST['task_name'];
    $taskDescription = $_POST['task_description'];

    // TODO: Add code to save the task to the database or perform any other necessary actions
    // Example code to save the task to the database using PDO
    $servername = "localhost";
    $username = "your_username";
    $password = "your_password";
    $dbname = "your_database";

    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $conn->prepare("INSERT INTO tasks (name, description) VALUES (:name, :description)");
        $stmt->bindParam(':name', $taskName);
        $stmt->bindParam(':description', $taskDescription);
        $stmt->execute();

        echo "Task saved successfully!";
    } catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }

    $conn = null;
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Create Task</title>
</head>
<body>
    <h1>Create Task</h1>
    <form method="POST" action="">
        <label for="task_name">Task Name:</label>
        <input type="text" name="task_name" id="task_name" required><br><br>
        <label for="task_description">Task Description:</label>
        <textarea name="task_description" id="task_description" required></textarea><br><br>
        <input type="submit" value="Create Task">
    </form>
</body>
</html>