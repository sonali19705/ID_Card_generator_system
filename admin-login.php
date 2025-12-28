    <!DOCTYPE html>
    <html lang="en">
    <head>  
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>ID Card Generator - Admin Login</title>
        <link rel="stylesheet" href="admin.css">
    </head>
    <body class="auth-body">

        <div class="auth-container">
            <div class="auth-card">
                <h2 class="auth-title">Admin Login</h2>
                <p class="auth-subtitle">Access your admin dashboard</p>

                <!-- Admin Login Form -->
                <form id="adminloginForm" action="admin_login_process.php" method="POST">
                    <div class="form-group">
                        <label for="adminEmail">Admin Email</label>
                        <input type="email" id="adminEmail" name="adminEmail" placeholder="Enter admin email" required>
                    </div>

                    <div class="form-group">
                        <label for="adminPassword">Password</label>
                        <input type="password" id="adminPassword" name="adminPassword" placeholder="Enter password" required>
                    </div>

                    <button type="submit" class="btn-primary">Login</button>
                </form>

                <p class="auth-links">
                    <a href="#" id="forgotPasswordLink">Forgot Password?</a><br>
                </p>
            </div>
        </div>

        <!-- Forgot Password Modal -->
        <div id="forgotModal" class="modal">
            <div class="modal-content">
                <span class="close-btn" id="closeModal">&times;</span>
                <h3>Reset Admin Password</h3>
                <p>Enter your registered email to receive reset instructions.</p>
                <form id="forgotForm" action="admin_forgot_process.php" method="POST">
                    <div class="form-group">
                        <input type="email" id="forgotEmail" name="forgotEmail" placeholder="Enter admin email" required>
                    </div>
                    <button type="submit" class="btn-primary">Send Reset Link</button>
                </form>
            </div>
        </div>

        <script src="admin.js"></script>
    </body>
    </html>
