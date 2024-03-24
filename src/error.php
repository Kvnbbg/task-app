<?php
// Determine if this is an API call or a direct request (e.g., via a query parameter or HTTP header)
$isApiCall = isset($_GET['api']);

if ($isApiCall) {
    header('Content-Type: application/json');
    $response = ['error' => 'An unexpected error occurred. Please try again later.'];
    echo json_encode($response);
    exit(); // Stop script execution after sending JSON response
}

$errorMessage = 'An unexpected error occurred.';
if (isset($_GET['error'])) {
    $errorMessage = htmlspecialchars($_GET['error']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .container { padding-top: 20px; }
        .alert { color: red; }
    </style>
</head>
<body>
    <div class="container text-center">
        <h1 class="alert alert-danger">Error</h1>
        <p><?php echo $errorMessage; ?></p>
        <a href="index.php" class="btn btn-primary">Go Back to Home</a>
    </div>
    <!-- Bootstrap JS, Popper.js, and jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
