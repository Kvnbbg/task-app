<?php

// Your code goes here
<!DOCTYPE html>
<html>
<head>
    <title>Task App</title>
    <link rel="stylesheet" href="path/to/your/styles.css">
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

?>
// Path: tasks/process_form.php