<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to IMS - Inventory Management System</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        html, body {
            height: 100%;
        }
        body {
            display: flex;
            flex-direction: column;
        }
        .main-content {
            flex: 1 0 auto;
        }
        .footer {
            flex-shrink: 0;
        }
        .hero-section {
            background-color: #f8f9fa;
            padding: 6rem 0;
        }
        .feature-icon {
            font-size: 3rem;
            color: #007bff;
        }
        .features-section {
            padding: 4rem 0;
        }
        .form-group {
            position: relative;
        }
        .form-control-icon {
            position: absolute;
            top: 50%;
            left: 15px;
            transform: translateY(-50%);
            color: #ced4da;
        }
        .form-control { 
            padding-left: 40px; 
        }
        .input-group .form-control {
            padding-left: 12px; /* Reset padding for input groups */
        }
        .password-toggle { cursor: pointer; }
        #password-strength-text { min-height: 20px; }
    </style>
</head>
<body>
    <div class="main-content">
        <!-- Hero Section -->
        <div class="hero-section text-center">
            <div class="container">
                <h1 class="display-4">Inventory Management System</h1>
                <p class="lead">A simple, powerful, and intuitive solution to manage your stock, track products, and generate reports.</p>
                <hr class="my-4">
                <p>Get started by logging in or creating a new account.</p>
                <button class="btn btn-primary btn-lg" data-toggle="modal" data-target="#loginModal">Login</button>
                <button class="btn btn-success btn-lg" data-toggle="modal" data-target="#registerModal">Register</button>
            </div>
        </div>

        <!-- Features Section -->
        <div class="features-section text-center">
            <div class="container">
                <div class="row">
                    <div class="col-md-4">
                        <i class="fas fa-tachometer-alt feature-icon mb-3"></i>
                        <h3>Comprehensive Dashboard</h3>
                        <p class="text-muted">Get a quick overview of your inventory with key stats at a glance.</p>
                    </div>
                    <div class="col-md-4">
                        <i class="fas fa-boxes feature-icon mb-3"></i>
                        <h3>Product Management</h3>
                        <p class="text-muted">Easily add, edit, and track all your products and their history.</p>
                    </div>
                    <div class="col-md-4">
                        <i class="fas fa-chart-line feature-icon mb-3"></i>
                        <h3>Sales & Reporting</h3>
                        <p class="text-muted">Record sales, generate invoices, and export data for analysis.</p>
                    </div>
                </div>
            </div>
        </div>
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
                        <div class="form-group mb-3">
                            <i class="fas fa-envelope form-control-icon"></i>
                            <label for="email">Email</label>
                            <input type="email" name="email" id="login_email" class="form-control" value="<?php echo htmlspecialchars(isset($form_errors) && $form_errors['type'] === 'login' ? ($form_errors['post']['email'] ?? '') : ''); ?>" required>
                        </div>
                        <div class="form-group mb-4">
                            <label for="login_password">Password</label>
                            <div class="input-group">
                                <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-lock"></i></span></div>
                                <input type="password" name="password" id="login_password" class="form-control" required>
                                <div class="input-group-append">
                                    <span class="input-group-text password-toggle"><i class="fas fa-eye"></i></span>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">Login</button>
                    </form>
                </div>
                <div class="modal-footer justify-content-center">
                    <p class="text-muted mb-0">
                        Don't have an account? 
                        <a href="#" data-dismiss="modal" data-toggle="modal" data-target="#registerModal">Register here</a>
                    </p>
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
                        <div class="form-group mb-3">
                            <i class="fas fa-user form-control-icon"></i>
                            <label for="full_name">Full Name</label>
                            <input type="text" name="full_name" id="full_name" class="form-control" value="<?php echo htmlspecialchars(isset($form_errors) && $form_errors['type'] === 'register' ? ($form_errors['post']['full_name'] ?? '') : ''); ?>" required>
                        </div>
                        <div class="form-group mb-3">
                            <i class="fas fa-envelope form-control-icon"></i>
                            <label for="register_email">Email</label>
                            <input type="email" name="email" id="register_email" class="form-control" value="<?php echo htmlspecialchars(isset($form_errors) && $form_errors['type'] === 'register' ? ($form_errors['post']['email'] ?? '') : ''); ?>" required>
                            <small id="email-validation-status" class="form-text"></small>
                        </div>
                        <div class="form-group mb-2">
                            <label for="register_password">Password</label>
                            <div class="input-group">
                                <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-lock"></i></span></div>
                                <input type="password" name="password" id="register_password" class="form-control" required>
                                <div class="input-group-append">
                                    <span class="input-group-text password-toggle"><i class="fas fa-eye"></i></span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group mb-3">
                            <div class="progress" style="height: 5px;">
                                <div id="password-strength-bar" class="progress-bar" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <small id="password-strength-text" class="form-text text-muted"></small>
                        </div>
                        <div class="form-group mb-4">
                            <label for="confirm_password">Confirm Password</label>
                            <div class="input-group">
                                <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-check-circle"></i></span></div>
                                <input type="password" name="confirm_password" id="confirm_password" class="form-control" required>
                                <div class="input-group-append">
                                    <span class="input-group-text password-toggle"><i class="fas fa-eye"></i></span>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-success btn-block">Create Account</button>
                    </form>
                </div>
                <div class="modal-footer justify-content-center">
                    <p class="text-muted mb-0">
                        Already have an account? <a href="#" data-dismiss="modal" data-toggle="modal" data-target="#loginModal">Login here</a>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer bg-light text-center py-4">
        <div class="container">
            <p class="text-muted mb-0">&copy; <?php echo date('Y'); ?> Inventory Management System. All Rights Reserved.</p>
        </div>
    </footer>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
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

            // Handle switching between modals
            $('[data-dismiss="modal"][data-toggle="modal"]').on('click', function(e) {
                var target = $(this).data('target');
                // Find the currently open modal and hide it
                $('.modal.show').modal('hide');
                // Show the new modal
                $(target).modal('show');
            });

            // Handle password visibility toggle
            $('.password-toggle').on('click', function() {
                var input = $(this).closest('.input-group').find('input');
                var icon = $(this).find('i');
                if (input.attr('type') === 'password') {
                    input.attr('type', 'text');
                    icon.removeClass('fa-eye').addClass('fa-eye-slash');
                } else {
                    input.attr('type', 'password');
                    icon.removeClass('fa-eye-slash').addClass('fa-eye');
                }
            });

            // Handle password strength meter
            $('#register_password').on('input', function() {
                var password = $(this).val();
                var strength = 0;
                var strengthText = '';
                var progressBar = $('#password-strength-bar');
                var strengthTextElem = $('#password-strength-text');

                if (password.length >= 8) strength++; // Length
                if (password.match(/[a-z]/)) strength++; // Lowercase
                if (password.match(/[A-Z]/)) strength++; // Uppercase
                if (password.match(/[0-9]/)) strength++; // Numbers
                if (password.match(/[^a-zA-Z0-9]/)) strength++; // Special chars

                progressBar.removeClass('bg-danger bg-warning bg-info bg-success');

                if (password.length === 0) {
                    strength = 0;
                    strengthText = '';
                } else if (strength <= 2) {
                    strengthText = 'Weak';
                    progressBar.addClass('bg-danger');
                } else if (strength === 3) {
                    strengthText = 'Medium';
                    progressBar.addClass('bg-warning');
                } else if (strength === 4) {
                    strengthText = 'Strong';
                    progressBar.addClass('bg-info');
                } else {
                    strengthText = 'Very Strong';
                    progressBar.addClass('bg-success');
                }

                var strengthPercent = (strength / 5) * 100;
                if (password.length > 0 && password.length < 8) {
                    strengthPercent = Math.max(0, (password.length / 8) * 20); // Give some progress for length < 8
                    strengthText = 'Weak (must be 8+ chars)';
                    progressBar.addClass('bg-danger');
                }

                if (password.length === 0) {
                    progressBar.css('width', '0%');
                } else {
                    progressBar.css('width', strengthPercent + '%');
                }

                strengthTextElem.text(strengthText);
            });

            // Handle real-time email validation
            $('#register_email').on('blur', function() {
                var emailInput = $(this);
                var email = emailInput.val();
                var validationStatus = $('#email-validation-status');
                var registerButton = $('#registerModal button[type="submit"]');

                // Basic client-side check
                if (email.length === 0 || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                    validationStatus.text('');
                    emailInput.removeClass('is-invalid is-valid');
                    return;
                }

                $.ajax({
                    url: 'index.php?action=checkEmail',
                    method: 'POST',
                    data: { email: email },
                    dataType: 'json',
                    success: function(response) {
                        if (response.available) {
                            emailInput.removeClass('is-invalid').addClass('is-valid');
                            validationStatus.text('Email is available.').removeClass('text-danger').addClass('text-success');
                            registerButton.prop('disabled', false);
                        } else {
                            emailInput.removeClass('is-valid').addClass('is-invalid');
                            validationStatus.text('This email is already taken.').removeClass('text-success').addClass('text-danger');
                            registerButton.prop('disabled', true);
                        }
                    },
                    error: function() {
                        validationStatus.text('Could not verify email. Please try again.').addClass('text-danger');
                    }
                });
            });
        });
    </script>
</body>
</html>