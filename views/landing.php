<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to IMS</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <!-- Main Page Content -->
    <div class="jumbotron text-center">
        <h1 class="display-4">Inventory Management System</h1>
        <p class="lead">A simple, powerful, and intuitive solution to manage your stock, track products, and generate reports.</p>
        <hr class="my-4">
        <p>Get started by logging in or creating a new account.</p>
        <button class="btn btn-primary btn-lg" data-toggle="modal" data-target="#loginModal">Login</button>
        <button class="btn btn-success btn-lg" data-toggle="modal" data-target="#registerModal">Register</button>
    </div>

    <!-- Login Modal -->
    <div class="modal fade" id="loginModal" tabindex="-1" role="dialog" aria-labelledby="loginModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="loginModalLabel">Login</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <?php if (isset($form_errors) && $form_errors['type'] === 'login'): ?>
                        <div class="alert alert-danger"><?php echo htmlspecialchars($form_errors['error']); ?></div>
                    <?php endif; ?>
                    <form action="index.php?action=login" method="post">
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" name="email" id="email" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" name="password" id="password" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">Login</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Register Modal -->
    <div class="modal fade" id="registerModal" tabindex="-1" role="dialog" aria-labelledby="registerModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="registerModalLabel">Register</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <?php if (isset($form_errors) && $form_errors['type'] === 'register'): ?>
                        <div class="alert alert-danger">
                            <ul>
                                <?php foreach ($form_errors['errors'] as $error): ?>
                                    <li><?php echo htmlspecialchars($error); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    <form action="index.php?action=register" method="post">
                        <div class="form-group">
                            <label for="full_name">Full Name</label>
                            <input type="text" name="full_name" id="full_name" class="form-control" value="<?php echo htmlspecialchars($form_errors['post']['full_name'] ?? ''); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" name="email" id="email" class="form-control" value="<?php echo htmlspecialchars($form_errors['post']['email'] ?? ''); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" name="password" id="password" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="confirm_password">Confirm Password</label>
                            <input type="password" name="confirm_password" id="confirm_password" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-success btn-block">Create Account</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script>
        // This script will check if there were form errors and open the correct modal on page load.
        $(document).ready(function() {
            <?php if (isset($form_errors)): ?>
                <?php if ($form_errors['type'] === 'login'): ?>
                    $('#loginModal').modal('show');
                <?php elseif ($form_errors['type'] === 'register'): ?>
                    $('#registerModal').modal('show');
                <?php endif; ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['message'])): ?>
                // If there's a success message from registration, show the login modal
                $('#loginModal').modal('show');
            <?php endif; ?>
        });
    </script>
</body>
</html>