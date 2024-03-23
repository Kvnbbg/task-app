<?php
// Your PHP code here
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task App</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/styles.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <!-- Navbar -->
        <ul class="navbar-nav mr-auto">
            <li class="nav-item active"><a class="nav-link" href="#">Task 1</a></li>
            <li class="nav-item"><a class="nav-link" href="#">Task 2</a></li>
            <li class="nav-item"><a class="nav-link" href="#">Task 3</a></li>
        </ul>
    </nav>
    <div class="container mt-5">
        <h1>Welcome to My Task App</h1>
        <div id="task-list" class="mt-4">
            <h2>Tasks</h2>
            <ul id="tasks" class="list-group">
                <li class="list-group-item">Task 1</li>
                <li class="list-group-item">Task 2</li>
                <li class="list-group-item">Task 3</li>
            </ul>
            <script>
                // JavaScript code remains the same
            </script>
        </div>
        <img src="assets/image.jpg" alt="Image" class="img-fluid mt-4">
        <h1>To-Do List</h1>
        <form action="process_form.php" method="POST" class="form-inline mt-4">
            <input type="text" name="task" placeholder="Enter a task" class="form-control mr-2">
            <button type="submit" class="btn btn-primary">Add Task</button>
        </form>
        <footer class="footer mt-5 py-3 bg-light">
            <div class="container">
                <p>&copy; <?php echo date("Y"); ?> Task App. All rights reserved.</p>
            </div>
        </footer>
    </div>
    <!-- Bootstrap JS, Popper.js, and jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
