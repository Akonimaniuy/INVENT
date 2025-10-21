<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IMS - Inventory Management System</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            padding-top: 56px; /* Space for fixed navbar */
        }
        .footer {
            width: 100%;
            padding: 15px;
            background-color: #f8f9fa;
            text-align: center;
            position: relative;
            bottom: 0;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
        <a class="navbar-brand" href="index.php">IMS</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item"><a class="nav-link" href="index.php?action=dashboard">Dashboard</a></li>
                <li class="nav-item"><a class="nav-link" href="index.php?action=products">Products</a></li>
                <li class="nav-item"><a class="nav-link" href="index.php?action=categories">Categories</a></li>
                <li class="nav-item"><a class="nav-link" href="index.php?action=reports">Reports</a></li>
            </ul>
            <ul class="navbar-nav">
                <li class="nav-item"><span class="navbar-text mr-3">Welcome, <?php echo htmlspecialchars($_SESSION['user_full_name'] ?? 'Guest'); ?></span></li>
                <li class="nav-item"><a class="nav-link" href="index.php?action=logout">Logout</a></li>
            </ul>
        </div>
    </nav>
    <div class="container mt-4">