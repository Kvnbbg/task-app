<?php
// task-app/backend/models/Task.php

class Task {
  public $id;
  public $title;
  public $description;
  public $completed;

  public function __construct($id, $title, $description, $completed) {
    $this->id = $id;
    $this->title = $title;
    $this->description = $description;
    $this->completed = $completed;
  }
  
  // Here, you might also include methods to interact with the database.
  // For example, methods to save the task to the database, update it, etc.
  public function save() {
    // Implement the logic to save the task to the database here
    // For example, you can use SQL queries or an ORM to interact with the database
    // Here's an example using PDO:
    $pdo = new PDO("mysql:host=localhost;dbname=task_app", "username", "password");
    $stmt = $pdo->prepare("INSERT INTO tasks (id, title, description, completed) VALUES (?, ?, ?, ?)");
    $stmt->execute([$this->id, $this->title, $this->description, $this->completed]);
}}
?>
