<?php
/**
 * Payment Gateway Configuration Template
 * 
 * INSTRUCTIONS:
 * 1. Copy this file to each payment directory (Stripe/, paypal/, midtrans/)
 * 2. Rename to config.php
 * 3. Replace placeholder values with your actual credentials
 * 4. NEVER commit the actual config.php files to version control
 */

// Example Stripe Configuration
define('STRIPE_PUBLISHABLE_KEY', 'pk_test_your_stripe_publishable_key_here');
define('STRIPE_SECRET_KEY', 'sk_test_your_stripe_secret_key_here');

// Example PayPal Configuration  
define('PAYPAL_ID', 'your_paypal_business_email@example.com');
define('PAYPAL_URL', 'https://www.sandbox.paypal.com/cgi-bin/webscr'); // Sandbox URL
define('PAYPAL_CURRENCY', 'AUD');
define('PAYPAL_RETURN_URL', 'http://localhost/your-project/Payments/success.php');
define('PAYPAL_CANCEL_URL', 'http://localhost/your-project/cart.php');
define('PAYPAL_NOTIFY_URL', 'http://localhost/your-project/Payments/paypal/ipn.php');

// Example Midtrans Configuration
define('MIDTRANS_SERVER_KEY', 'your_midtrans_server_key_here');
define('MIDTRANS_CLIENT_KEY', 'your_midtrans_client_key_here');
define('MIDTRANS_IS_PRODUCTION', false); // Set to true for production

// Google reCAPTCHA Configuration
define('RECAPTCHA_V2_SITE_KEY', '6LcdSJsrAAAAAJZjWTh5PgEuYCCtRFp9p-xVR930');
define('RECAPTCHA_V2_SECRET_KEY', 'your_recaptcha_v2_secret_key_here');
define('RECAPTCHA_V3_SITE_KEY', '6LfEVpsrAAAAACvi8Rp4s37GIxdh0kkXYc1VZKiF');
define('RECAPTCHA_V3_SECRET_KEY', 'your_recaptcha_v3_secret_key_here');

// Database Configuration (if using)
define('DB_HOST', 'localhost');
define('DB_USER', 'your_db_username');
define('DB_PASS', 'your_db_password');
define('DB_NAME', 'your_database_name');

// Email Configuration for OTP
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'your_email@gmail.com');
define('SMTP_PASSWORD', 'your_app_password');
define('SMTP_FROM_EMAIL', 'your_email@gmail.com');
define('SMTP_FROM_NAME', 'Mini Shop');

?>