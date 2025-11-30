# 🔒 Security Policy

## 🛡️ Supported Versions

We actively support the following versions with security updates:

| Version | Supported          |
| ------- | ------------------ |
| 0.0.x   | :white_check_mark: |
| < 0.0.1 | :x:                |

## 🚨 Reporting a Vulnerability

If you discover a security vulnerability, please **DO NOT** open a public issue. Instead, please follow these steps:

### 📧 How to Report

1. **Email us directly** at: `security@example.com`
2. **Include the following information:**
   - Description of the vulnerability
   - Steps to reproduce
   - Potential impact
   - Suggested fix (if you have one)

### ⏱️ Response Time

- We will acknowledge your report within **48 hours**
- We will provide a detailed response within **7 days**
- We will keep you informed of our progress

### 🎁 Recognition

We appreciate security researchers who help us keep WC Invoice secure. With your permission, we will:

- Credit you in our security advisories
- Add you to our security hall of fame
- Thank you publicly (if desired)

## 🔐 Security Best Practices

### For Users

- ✅ Keep WordPress, WooCommerce, and all plugins updated
- ✅ Use strong passwords
- ✅ Enable two-factor authentication
- ✅ Regularly backup your site
- ✅ Review user permissions regularly
- ✅ Use HTTPS/SSL certificates

### For Developers

- ✅ Always sanitize user input
- ✅ Use nonces for form submissions
- ✅ Validate and escape output
- ✅ Check user capabilities
- ✅ Use prepared statements for database queries
- ✅ Keep dependencies updated
- ✅ Follow WordPress coding standards

## 🔍 Known Security Considerations

### Input Validation

All user inputs are validated and sanitized using WordPress functions:
- `sanitize_text_field()`
- `wp_kses_post()`
- `absint()`
- `esc_html()`
- `esc_attr()`

### Output Escaping

All outputs are properly escaped:
- `esc_html()` for HTML content
- `esc_attr()` for HTML attributes
- `esc_url()` for URLs
- `wp_kses_post()` for allowed HTML

### Authentication & Authorization

- Nonce verification for all AJAX requests
- Capability checks (`current_user_can()`) for admin functions
- Proper user authentication checks

### Database Security

- Prepared statements for all database queries
- Data validation before database operations
- Proper table prefix usage

## 📋 Security Checklist

Before releasing a new version, we ensure:

- [ ] All inputs are validated and sanitized
- [ ] All outputs are escaped
- [ ] Nonces are verified
- [ ] Capabilities are checked
- [ ] SQL queries use prepared statements
- [ ] No sensitive data in error messages
- [ ] Dependencies are up to date
- [ ] Security headers are set (if applicable)

## 🔄 Security Updates

Security updates are released as soon as possible after a vulnerability is discovered and patched. We recommend:

- Enabling automatic updates for security patches
- Monitoring the changelog for security fixes
- Testing updates in a staging environment first

## 📚 Additional Resources

- [WordPress Security](https://wordpress.org/support/article/hardening-wordpress/)
- [WooCommerce Security](https://woocommerce.com/document/woocommerce-security/)
- [OWASP Top 10](https://owasp.org/www-project-top-ten/)

---

**Thank you for helping keep WC Invoice secure!** 🛡️

