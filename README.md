# 🛍️ Mini Shop - Secure E-commerce Platform

A university project demonstrating secure web development practices through a mini e-commerce platform. This project showcases authentication security, payment processing, and user protection against common web threats.

**Built for**: Web Security & E-commerce Development Course  
**Student ID**: s3927657

## 🚀 What This Project Does

### 🔐 Security Features I Implemented
- **Multiple Ways to Stay Safe**
  - Google Authenticator app integration for extra security
  - Email verification codes for high-value purchases
  - User accounts with secure login/logout

- **Bot Protection**
  - reCAPTCHA checkboxes to stop automated attacks
  - Invisible background checks that score user behavior

### 💳 Payment Options I Added
- **4 Different Ways to Pay**
  - Stripe for credit/debit cards
  - PayPal for easy online payments
  - Google Pay for mobile users
  - Midtrans (popular in Indonesia)

### 🛒 Shopping Features
- Browse 3 Indonesian food items with photos
- Add items to cart (remembers what you picked)
- Choose between guest checkout or secure member checkout
- See different security levels based on how much you're spending

## 🛠️ Technologies I Used

**Backend (Server-side):**
- PHP - The main programming language for the website logic
- XAMPP - Local development server (like having a mini web server on my computer)
- PHP Sessions - Keeps track of your cart and login status

**Frontend (What users see):**
- HTML5/CSS3 - Makes the website look good and work on phones
- JavaScript - Handles payments and makes forms interactive

**Security Tools I Integrated:**
- Google Authenticator library - Generates those 6-digit security codes
- PHPMailer - Sends verification emails safely
- Stripe's official PHP library - Handles credit card payments securely

**External Services:**
- Google reCAPTCHA - Protects against bots and spam
- QR Code generator - Creates scannable codes for phone apps
- Payment APIs from Stripe, PayPal, etc.


## 🔧 How to Run This Project

### What You Need First
- XAMPP (free download - gives you a local web server)
- Nothing else! Everything is included

*Note: This runs on your computer for testing - no need for fancy hosting*

### Easy Setup Steps

1. **Download the code**
   - Download this repository or clone it
   - Put it in your `xampp/htdocs/` folder

2. **Start XAMPP**
   - Open XAMPP Control Panel
   - Click "Start" next to Apache
   - That's it! MySQL not required for basic testing

3. **Try it out**
   - Go to `http://localhost/secure-ecommerce-platform/`
   - Start shopping and testing features!

### For Full Features (Optional)
- Set up payment gateway accounts (all free for testing)
- Configure email settings for OTP verification
- Get reCAPTCHA keys from Google (also free)

## 🔐 What I Learned About Security

### Real Security Problems I Addressed
- **Stopping Bad Input**: Check everything users type before using it
- **Better Passwords**: Made login safer with extra verification steps
- **Session Safety**: Keep track of who's logged in securely
- **Permission Control**: Make sure users can only do what they should
- **Safe Settings**: Configure everything to be secure by default
- **Protecting Data**: Handle sensitive information carefully

## 🧪 Testing

All payment gateways work in sandbox/test mode - no real money involved! Try different security features:
- Register account to enable 2FA and email OTP
- Add $100+ items to cart to test high-security checkout
- Test all 4 payment methods

---

**This is a university project demonstrating secure web development practices.**
