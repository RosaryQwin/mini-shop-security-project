<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account - Mini Shop</title>
    <link rel="stylesheet" href="../css/style.css">
    
    <!-- reCAPTCHA v2 and v3 Scripts -->
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <script src="https://www.google.com/recaptcha/api.js?render=6LfEVpsrAAAAACvi8Rp4s37GIxdh0kkXYc1VZKiF"></script>
    
    <style>
        .auth-container {
            max-width: 500px;
            margin: 50px auto;
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #333;
        }
        .form-group input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 14px;
            box-sizing: border-box;
        }
        .security-options {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .checkbox-group {
            margin: 15px 0;
        }
        .btn-primary {
            background: #007BFF;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
            margin-top: 10px;
        }
        .btn-primary:hover { background: #0056b3; }
        .password-requirements {
            font-size: 12px;
            color: #666;
            margin-top: 5px;
        }
        .security-level {
            display: inline-block;
            padding: 4px 8px;
            background: #28a745;
            color: white;
            border-radius: 4px;
            font-size: 12px;
            margin-left: 10px;
        }
    </style>
</head>
<body>

<header>
    🛍️ Mini Shop - Create Account
    <a href="../index.php">← Back to Shop</a>
</header>

<div class="auth-container">
    <h2>Create Your Secure Account</h2>
    
    <form id="registerForm" action="process_register.php" method="post">
        
        <div class="form-group">
            <label for="email">Email Address</label>
            <input type="email" id="email" name="email" required>
        </div>
        
        <div class="form-group">
            <label for="full_name">Full Name</label>
            <input type="text" id="full_name" name="full_name" required>
        </div>
        
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
            <div class="password-requirements">
                Minimum 6 characters. Must include uppercase, lowercase, and number.
            </div>
        </div>
        
        <div class="form-group">
            <label for="confirm_password">Confirm Password</label>
            <input type="password" id="confirm_password" name="confirm_password" required>
        </div>
        
        <div class="security-options">
            <h4>🔒 Security Options <span class="security-level">RECOMMENDED</span></h4>
            
            <div class="checkbox-group">
                <label>
                    <input type="checkbox" name="enable_2fa" id="enable_2fa" checked>
                    Enable Google Authenticator 2FA (Highly Recommended)
                </label>
            </div>
            
            <div class="checkbox-group">
                <label>
                    <input type="checkbox" name="enable_email_otp" id="enable_email_otp" checked>
                    Enable Email OTP for High-Value Purchases ($100+ AUD)
                </label>
            </div>
            
            <div class="checkbox-group">
                <label>
                    <input type="checkbox" name="security_notifications" id="security_notifications" checked>
                    Receive Security Notifications via Email
                </label>
            </div>
        </div>
        
        <!-- reCAPTCHA v2 Checkbox -->
        <div class="form-group">
            <div class="g-recaptcha" data-sitekey="6LcdSJsrAAAAAJZjWTh5PgEuYCCtRFp9p-xVR930"></div>
        </div>
        
        <!-- Hidden field for reCAPTCHA v3 token -->
        <input type="hidden" name="recaptcha_v3_token" id="recaptcha_v3_token">
        
        <button type="submit" class="btn-primary">Create Secure Account</button>
        
        <div style="text-align: center; margin-top: 20px;">
            Already have an account? <a href="login.php">Login here</a>
        </div>
    </form>
</div>

<script>
// reCAPTCHA v3 Implementation
grecaptcha.ready(function() {
    grecaptcha.execute('6LfEVpsrAAAAACvi8Rp4s37GIxdh0kkXYc1VZKiF', {action: 'register'})
    .then(function(token) {
        document.getElementById('recaptcha_v3_token').value = token;
    });
});

// Form validation
document.getElementById('registerForm').addEventListener('submit', function(e) {
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('confirm_password').value;
    
    // Password strength validation (made easier for testing)
    const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[A-Za-z\d@$!%*?&]{6,}$/;
    
    if (!passwordRegex.test(password)) {
        alert('Password must be at least 6 characters and include uppercase, lowercase, and number.');
        e.preventDefault();
        return false;
    }
    
    if (password !== confirmPassword) {
        alert('Passwords do not match!');
        e.preventDefault();
        return false;
    }
    
    // Check reCAPTCHA v2
    const recaptchaResponse = grecaptcha.getResponse();
    if (!recaptchaResponse) {
        alert('Please complete the reCAPTCHA verification.');
        e.preventDefault();
        return false;
    }
});

// Real-time password strength indicator
document.getElementById('password').addEventListener('input', function() {
    const password = this.value;
    const requirements = document.querySelector('.password-requirements');
    
    let strength = 0;
    if (password.length >= 6) strength++;
    if (/[a-z]/.test(password)) strength++;
    if (/[A-Z]/.test(password)) strength++;
    if (/\d/.test(password)) strength++;
    if (/[@$!%*?&]/.test(password)) strength++;
    
    const strengthText = ['Very Weak', 'Weak', 'Fair', 'Good', 'Strong'];
    const strengthColors = ['#ff4757', '#ff6348', '#ffa502', '#26de81', '#2ed573'];
    
    if (password.length > 0) {
        requirements.innerHTML = `Password Strength: <span style="color: ${strengthColors[strength-1] || '#ff4757'}">${strengthText[strength-1] || 'Very Weak'}</span>`;
    } else {
        requirements.innerHTML = 'Minimum 6 characters. Must include uppercase, lowercase, and number.';
    }
});
</script>

</body>
</html>