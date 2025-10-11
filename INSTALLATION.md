# 🚀 Installation Guide

## Prerequisites

- **XAMPP** or similar LAMP/WAMP stack
- **PHP 7.4+** 
- **MySQL/MariaDB**
- **Web browser** with JavaScript enabled

## Quick Setup

### 1. Clone & Setup
```bash
git clone https://github.com/[your-username]/secure-ecommerce-platform.git
cd secure-ecommerce-platform
```

### 2. XAMPP Configuration
- Move project to `C:\xampp\htdocs\secure-ecommerce-platform\`
- Start Apache and MySQL in XAMPP Control Panel
- Access via: `http://localhost/secure-ecommerce-platform/`

### 3. Payment Gateway Configuration

**For each payment gateway, create config files:**

```bash
# Copy the example config to each payment directory
copy Payments\config.example.php Payments\Stripe\config.php
copy Payments\config.example.php Payments\paypal\config.php  
copy Payments\config.example.php Payments\midtrans\config.php
```

**Edit each config.php file with your credentials:**

#### Stripe Setup:
1. Register at [Stripe Dashboard](https://dashboard.stripe.com/)
2. Get your test API keys
3. Update `Payments/Stripe/config.php`

#### PayPal Setup:
1. Create [PayPal Sandbox Account](https://developer.paypal.com/)
2. Get business account email
3. Update `Payments/paypal/config.php`

#### Midtrans Setup:
1. Register at [Midtrans Dashboard](https://dashboard.midtrans.com/)
2. Get Server Key and Client Key  
3. Update `Payments/midtrans/config.php`

### 4. Google Services Setup

#### reCAPTCHA:
1. Register at [Google reCAPTCHA](https://www.google.com/recaptcha/)
2. Create v2 and v3 site keys for `localhost`
3. Update site keys in:
   - `register_v2.php` (for v2)
   - `register_v3.php` (for v3)
   - Your config files

### 5. Test the Application

1. **Browse Products**: `http://localhost/secure-ecommerce-platform/`
2. **Add to Cart**: Click "Add to Cart" on any product
3. **View Cart**: Click "View Full Cart" 
4. **Test Checkout**: Try different payment methods
5. **Test Security Features**: Try the 2FA and CAPTCHA implementations

## 🧪 Testing Payment Gateways

### Stripe Test Cards:
```
4242424242424242 - Visa (Success)
4000000000000002 - Card declined
4000000000009995 - Insufficient funds
```

### PayPal:
- Use PayPal Sandbox accounts
- Test with dummy PayPal credentials

### Google Pay:
- Works in supported browsers
- Requires HTTPS for production

## 🔐 Security Testing

### 2FA Testing (from AS1/Q4):
1. Set up Google Authenticator 
2. Scan QR code
3. Test TOTP verification

### CAPTCHA Testing (from AS1/Q2):
1. Test reCAPTCHA v2 checkbox
2. Test reCAPTCHA v3 invisible verification

### Email OTP Testing (from AS1/Q3):
1. Configure SMTP settings
2. Test email delivery
3. Verify OTP codes

## 🚨 Important Notes

- **This is for DEVELOPMENT/TESTING only**
- **Never use production API keys in code**
- **All payments are in SANDBOX/TEST mode**
- **SSL certificate required for production**

## 📝 Configuration Files Structure

```
Payments/
├── config.example.php      # Template (committed to git)
├── Stripe/
│   ├── config.php         # Your actual keys (NOT in git)
│   └── checkout.php
├── paypal/
│   ├── config.php         # Your actual keys (NOT in git)  
│   └── ipn.php
└── midtrans/
    ├── config.php         # Your actual keys (NOT in git)
    └── checkout.php
```

## 🔧 Troubleshooting

### Common Issues:

**"Payment gateway not configured"**
- Check if config.php files exist in each payment directory
- Verify API keys are correct

**"CAPTCHA verification failed"**  
- Check reCAPTCHA site keys match your domain
- Ensure localhost is added to authorized domains

**"Email OTP not sending"**
- Verify SMTP credentials
- Check Gmail app passwords if using Gmail

**"Cart not working"**
- Ensure PHP sessions are enabled
- Check file permissions

## 🆘 Support

This is an academic project for demonstration purposes. For issues:

1. Check the installation steps above
2. Verify all config files are properly set up
3. Test with provided sandbox credentials
4. Review browser console for JavaScript errors

---

**Ready to explore secure e-commerce development!** 🛍️🔒