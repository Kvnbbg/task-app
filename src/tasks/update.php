<?php

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the task ID from the form
    $taskId = $_POST['task_id'];

    // TODO: Retrieve the task from the database using the task ID
    // Retrieve the task from the database using the task ID
    $task = getTaskById($taskId);

    // Update the task with the new data
    $task['title'] = $_POST['title'];
    $task['description'] = $_POST['description'];

    // Save the updated task back to the database
    updateTask($task);

    // Function to retrieve a task by ID from the database
    function getTaskById($taskId) {
        // Database connection parameters
        $host = 'localhost';
        $db   = 'my_database';
        $user = 'my_user';
        $pass = 'my_password';
        $charset = 'utf8mb4';
    
        // Create a new PDO instance
        $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
        $database = new PDO($dsn, $user, $pass);
    
        // Query the database
        $task = $database->query("SELECT * FROM tasks WHERE id = $taskId")->fetch();
    
        return $task;
    }

    // Function to update a task in the database
    function updateTask($task) {
        // Database connection parameters
        $host = 'localhost';
        $db   = 'my_database';
        $user = 'my_user';
        $pass = 'my_password';
        $charset = 'utf8mb4';
    
        // Create a new PDO instance
        $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
        $database = new PDO($dsn, $user, $pass);
    
        // Prepare an SQL statement
        $stmt = $database->prepare("UPDATE tasks SET name = :name, description = :description WHERE id = :id");
    
        // Bind parameters to the SQL statement and execute it
        $stmt->execute([
            'name' => $task['name'],
            'description' => $task['description'],
            'id' => $task['id']
        ]);
    }

    // Redirect to the task list page
    header('Location: task-list.php');
    exit;
}

// If the form is not submitted, display the update form
?>

<!DOCTYPE html>
<html>
<head>
    <title>Update Task</title>
</head>
<body>
    <h1>Update Task</h1>

    <form method="POST" action="update.php">
        <input type="hidden" name="task_id" value="<?php echo $taskId; ?>">

        <!-- TODO: Display the current task data in input fields -->
        <label for="title">Title:</label>
        <input type="text" name="title" id="title" value="<?php echo $task['title']; ?>">
        <br>
        <label for="description">Description:</label>
        <textarea name="description" id="description"><?php echo $task['description']; ?></textarea>
        <br>
        <br>

        <button type="submit">Update Task</button>
    </form>
</body>
</html>