<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IMS - Inventory Management System</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-white fixed-top">
        <a class="navbar-brand" href="index.php">IMS</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item"><a class="nav-link" href="index.php?action=dashboard"><i class="fas fa-tachometer-alt mr-1"></i> Dashboard</a></li>
                <li class="nav-item"><a class="nav-link" href="index.php?action=products"><i class="fas fa-box-open mr-1"></i> Products</a></li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="salesDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-dollar-sign mr-1"></i> Sales
                    </a>
                    <div class="dropdown-menu" aria-labelledby="salesDropdown">
                        <a class="dropdown-item" href="index.php?action=record_sale">Record Sale</a>
                        <a class="dropdown-item" href="index.php?action=sales_list">View Sales History</a>
                    </div>
                </li>
                <li class="nav-item"><a class="nav-link" href="index.php?action=categories"><i class="fas fa-tags mr-1"></i> Categories</a></li>
                <li class="nav-item"><a class="nav-link" href="index.php?action=reports"><i class="fas fa-chart-pie mr-1"></i> Reports</a></li>
            </ul>
            <ul class="navbar-nav">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-user-circle mr-1"></i> <?php echo htmlspecialchars($_SESSION['user_full_name'] ?? 'Guest'); ?>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown">
                        <a class="dropdown-item" href="index.php?action=logout"><i class="fas fa-sign-out-alt mr-2"></i> Logout</a>
                    </div>
                </li>
            </ul>
        </div>
    </nav>
    <main class="container-fluid">