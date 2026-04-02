# Mini Shop — Secure E-Commerce Platform

> A full-stack e-commerce application built with a security-first architecture, demonstrating multi-factor authentication, payment gateway integration, and protection against common web vulnerabilities.

![PHP](https://img.shields.io/badge/PHP-777BB4?style=flat&logo=php&logoColor=white)
![HTML5](https://img.shields.io/badge/HTML5-E34F26?style=flat&logo=html5&logoColor=white)
![CSS3](https://img.shields.io/badge/CSS3-1572B6?style=flat&logo=css3&logoColor=white)
![JavaScript](https://img.shields.io/badge/JavaScript-F7DF1E?style=flat&logo=javascript&logoColor=black)
![Stripe](https://img.shields.io/badge/Stripe-008CDD?style=flat&logo=stripe&logoColor=white)

---

## Overview

Mini Shop is a university capstone project exploring secure web development practices in the context of a real-world e-commerce scenario. The platform implements a layered security model — combining multi-factor authentication, bot protection, and risk-based checkout flows — while integrating four production-grade payment gateways.

---

## Features

### Security Architecture

| Layer | Implementation |
|---|---|
| Multi-Factor Authentication | TOTP via Google Authenticator (RFC 6238) |
| Email OTP | PHPMailer with time-limited verification codes |
| Bot Protection | Google reCAPTCHA v2 (checkbox) + v3 (invisible, behavior scoring) |
| Session Management | Secure PHP sessions with login state tracking |
| Input Validation | Server-side sanitization to prevent injection attacks |
| Risk-Based Checkout | Elevated security requirements triggered at high cart values |

### Payment Gateway Integration

- **Stripe** — Credit and debit card processing via Stripe Elements
- **PayPal** — PayPal Checkout SDK integration
- **Google Pay** — Mobile-first payments via Google Pay API
- **Midtrans** — Regional payment gateway (Indonesia)

> All gateways operate in **sandbox/test mode** — no real transactions are processed.

### Storefront

- Product catalog featuring Indonesian food items with images and descriptions
- Persistent cart using PHP sessions (guest and authenticated users)
- Responsive UI supporting desktop and mobile browsers
- Dual checkout flow: guest checkout and authenticated member checkout

---

## Tech Stack

**Backend**
- PHP (core application logic and session handling)
- XAMPP (Apache local development server)
- PHPMailer (transactional email delivery)
- Google Authenticator PHP library (TOTP generation and validation)
- Stripe PHP SDK

**Frontend**
- HTML5 / CSS3
- Vanilla JavaScript (payment UI, form interactivity)

**External Services**
- Google reCAPTCHA v2 & v3
- Stripe, PayPal, Google Pay, and Midtrans APIs
- QR code generation (for Authenticator app setup)

---

## Getting Started

### Prerequisites

- [XAMPP](https://www.apachefriends.org/) (Apache only — MySQL not required for core features)

### Installation

```bash
# 1. Clone the repository
git clone https://github.com/your-username/secure-ecommerce-platform.git

# 2. Move to XAMPP web root
cp -r secure-ecommerce-platform/ /path/to/xampp/htdocs/

# 3. Start Apache via the XAMPP Control Panel

# 4. Open in browser
http://localhost/secure-ecommerce-platform/
```

### Optional Configuration

To enable the full feature set, configure the following in the project's config file:

| Feature | Requirement |
|---|---|
| Email OTP | SMTP credentials (e.g. Gmail App Password) |
| Google reCAPTCHA | API keys from [Google reCAPTCHA Console](https://www.google.com/recaptcha/admin) |
| Stripe | Test API keys from [Stripe Dashboard](https://dashboard.stripe.com/) |
| PayPal | Sandbox credentials from [PayPal Developer](https://developer.paypal.com/) |
| Midtrans | Sandbox keys from [Midtrans Dashboard](https://dashboard.sandbox.midtrans.com/) |

---

## Security Concepts Demonstrated

This project was built to practically apply the following web security principles:

- **Input validation & output encoding** — Preventing XSS and SQL injection
- **Authentication hardening** — MFA via TOTP and email-based OTP
- **Session security** — Proper session lifecycle management and access control
- **Risk-based authentication** — Dynamic security requirements based on transaction value
- **Secure-by-default configuration** — Minimal exposure of sensitive data and server internals
- **Bot mitigation** — Layered reCAPTCHA integration (challenge + behavioral scoring)

---

## Testing Guide

| Scenario | How to Test |
|---|---|
| 2FA setup | Register an account → scan QR code with Google Authenticator |
| Email OTP | Add items over the high-value threshold → proceed to checkout |
| Payment flows | Use test card numbers provided in each gateway's sandbox docs |
| Bot protection | Observe reCAPTCHA behavior at checkout and registration |

**Stripe test card:** `4242 4242 4242 4242` — any future expiry, any CVC

---

## Project Structure

```
secure-ecommerce-platform/
├── index.php               # Product catalog / homepage
├── cart.php                # Cart management
├── checkout.php            # Checkout flow with risk-based auth
├── auth/
│   ├── login.php
│   ├── register.php
│   └── totp.php            # Google Authenticator verification
├── payments/
│   ├── stripe.php
│   ├── paypal.php
│   ├── googlepay.php
│   └── midtrans.php
├── includes/
│   ├── config.php          # API keys and configuration
│   └── session.php         # Session helpers
└── assets/
    ├── css/
    ├── js/
    └── images/
```

---

## Academic Context

**Course:** Web Security & E-Commerce Development  
**Institution:** [Your University]  
**Purpose:** Demonstrating secure full-stack development practices in a realistic application context

---

## License

This project is intended for educational purposes. See [LICENSE](LICENSE) for details.
