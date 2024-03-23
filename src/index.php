<?php
?>
// Your code goes here
<!DOCTYPE html>
<html>
<head>
    <title>Task App</title>
    <link rel="stylesheet" href="/styles.css">
</head>
<body>
    <nav>
        <!-- Navbar -->
        <ul>
            <li>Task 1</li>
            <li>Task 2</li>
            <li>Task 3</li>
        </ul>
    </nav>
    <h1>Welcome to My Task App</h1>
    
    <div id="task-list">
        <h2>Tasks</h2>
        <ul id="tasks">
            <li>Task 1</li>
            <li>Task 2</li>
            <li>Task 3</li>
        </ul>
        <script>
            // Get the tasks element
            const tasksElement = document.getElementById('tasks');

            // Create a new task element
            const newTaskElement = document.createElement('li');
            newTaskElement.textContent = 'Task 4';

            // Append the new task element to the tasks list
            tasksElement.appendChild(newTaskElement);
        </script>
    <img src="path/to/your/image.jpg" alt="Image">
    <h1>To-Do List</h1>
    <form action="process_form.php" method="POST">
        <input type="text" name="task" placeholder="Enter a task">
        <button type="submit">Add Task</button>
    </form>
    <footer>
        <!-- Footer content -->
        <p>&copy; <?php echo date("Y"); ?> Task App. All rights reserved.</p>
    </footer>
</body>
</html>
// Path: tasks/process_form.php
// Compare this snippet from tasks/db.php:
// <?php
// $host = 'localhost';
// $db = '
// $user =
// $pass =
// $charset = 'utf8mb4';
//
// $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
// $options = [
//     PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
//     PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
//     PDO::ATTR_EMULATE_PREPARES   => false,
// ];
//
// try {
//      $pdo = new PDO($dsn, $user, $pass, $options);
// } catch (\PDOException $e) {
//      throw new \PDOException($e->getMessage(), (int)$e->getCode());
// }
// ?>
// Compare this snippet from tasks/create.php: