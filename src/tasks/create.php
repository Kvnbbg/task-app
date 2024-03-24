<?php
require 'db.php'; // Use the existing database connection

$taskName = $taskDescription = "";
$feedback = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $taskName = trim($_POST['task_name']);
    $taskDescription = trim($_POST['task_description']);

    // Basic validation
    if (empty($taskName) || empty($taskDescription)) {
        $feedback = "Please fill in all fields.";
    } elseif (strlen($taskName) > 255) {
        $feedback = "Task name is too long. Maximum length is 255 characters.";
    } else {
        try {
            // Prepare SQL and bind parameters
            $stmt = $pdo->prepare("INSERT INTO tasks (name, description) VALUES (:name, :description)");
            $stmt->bindParam(':name', $taskName);
            $stmt->bindParam(':description', $taskDescription);

            $stmt->execute();
            $feedback = "Task saved successfully!";
            // Clear form fields after successful submission
            $taskName = $taskDescription = "";
        } catch(PDOException $e) {
            $feedback = "Error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Create Task</title>
    <!-- Consider including Bootstrap or your CSS for styling -->
</head>
<body>
    <h1>Create Task</h1>
    <?php if (!empty($feedback)): ?>
        <p><?php echo $feedback; ?></p>
    <?php endif; ?>
    <form method="POST" action="">
        <label for="task_name">Task Name:</label>
        <input type="text" name="task_name" id="task_name" value="<?php echo htmlspecialchars($taskName); ?>" required><br><br>
        <label for="task_description">Task Description:</label>
        <textarea name="task_description" id="task_description" required><?php echo htmlspecialchars($taskDescription); ?></textarea><br><br>
        <input type="submit" value="Create Task">
    </form>
</body>
</html>
