<?php

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the task ID from the request
    $taskId = $_POST['task_id'];

    // TODO: Implement your delete logic here

    // Redirect back to the tasks list page
    header('Location: tasks.php');
    exit;
} else {
    // Redirect back to the tasks list page if accessed directly
    header('Location: tasks.php');
    exit;
}