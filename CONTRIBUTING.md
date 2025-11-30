# 🤝 Contributing to WC Invoice

First off, thank you for considering contributing to WC Invoice! It's people like you that make WC Invoice such a great tool.

## 📋 Table of Contents

- [Code of Conduct](#-code-of-conduct)
- [How Can I Contribute?](#-how-can-i-contribute)
- [Development Process](#-development-process)
- [Coding Standards](#-coding-standards)
- [Commit Messages](#-commit-messages)
- [Pull Request Process](#-pull-request-process)

## 📜 Code of Conduct

This project adheres to a Code of Conduct that all contributors are expected to follow. Please be respectful and considerate of others.

## 💡 How Can I Contribute?

### 🐛 Reporting Bugs

Before creating bug reports, please check the issue list as you might find out that you don't need to create one. When you are creating a bug report, please include as many details as possible:

- **Clear and descriptive title**
- **Steps to reproduce** - Be specific!
- **Expected behavior** - What should happen?
- **Actual behavior** - What actually happens?
- **Screenshots** - If applicable
- **Environment** - WordPress version, PHP version, WooCommerce version
- **Additional context** - Any other relevant information

### ✨ Suggesting Enhancements

Enhancement suggestions are tracked as GitHub issues. When creating an enhancement suggestion, please include:

- **Clear and descriptive title**
- **Detailed description** - Explain the use case
- **Possible implementation** - If you have ideas
- **Alternatives considered** - Other solutions you've thought about

### 🔧 Pull Requests

- Fill in the required template
- Do not include issue numbers in the PR title
- Include screenshots and animated GIFs in your pull request whenever possible
- Follow the coding standards
- Include thoughtfully-worded, well-structured tests
- Document new code based on the Documentation Styleguide
- End all files with a newline

## 🛠️ Development Process

1. **Fork the repository**
2. **Create your feature branch** (`git checkout -b feature/AmazingFeature`)
3. **Make your changes**
4. **Commit your changes** (`git commit -m 'Add some AmazingFeature'`)
5. **Push to the branch** (`git push origin feature/AmazingFeature`)
6. **Open a Pull Request**

## 📝 Coding Standards

### PHP Code Style

- Follow [WordPress Coding Standards](https://developer.wordpress.org/coding-standards/wordpress-coding-standards/)
- Use PSR-4 autoloading
- All user-facing strings must be translatable
- Use type hints where possible (PHP 7.4+)
- Add PHPDoc comments for all functions and classes

### File Structure

- One class per file
- Class names should match file names
- Use namespaces properly
- Keep functions focused and single-purpose

### Example

```php
<?php

namespace WC_Invoice;

defined('ABSPATH') || exit;

/**
 * Class description
 */
class Example
{
    /**
     * Method description
     *
     * @param string $param Parameter description
     * @return bool Return description
     */
    public function exampleMethod(string $param): bool
    {
        // Implementation
        return true;
    }
}
```

## 📝 Commit Messages

We follow the [Conventional Commits](https://www.conventionalcommits.org/) specification:

- **feat**: A new feature
- **fix**: A bug fix
- **docs**: Documentation only changes
- **style**: Code style changes (formatting, missing semi colons, etc)
- **refactor**: Code refactoring
- **perf**: Performance improvements
- **test**: Adding or updating tests
- **chore**: Maintenance tasks

### Format

```text
<type>(<scope>): <subject>

<body>

<footer>
```

### Examples

```text
feat(Generator): add PDF generation support

Add ability to generate PDF invoices using mPDF library.
Includes new template system for PDF formatting.

Closes #123
```

```text
fix(Database): correct invoice number generation

Fix issue where invoice numbers were not incrementing correctly
when multiple invoices were generated simultaneously.

Fixes #456
```

## 🔄 Pull Request Process

1. Update the README.md with details of changes if applicable
2. Update the CHANGELOG.md with your changes
3. Ensure all tests pass
4. The PR will be reviewed by maintainers
5. Address any feedback or requested changes
6. Once approved, your PR will be merged

### PR Checklist

- [ ] Code follows the style guidelines
- [ ] Self-review completed
- [ ] Comments added for complex code
- [ ] Documentation updated
- [ ] No new warnings generated
- [ ] Tests added/updated
- [ ] All tests pass
- [ ] CHANGELOG.md updated

## 🧪 Testing

Before submitting your PR, please ensure:

- All existing tests pass
- New tests are added for new features
- Code coverage is maintained or improved
- Manual testing is performed

## 📚 Documentation

- Update README.md if needed
- Add/update code comments
- Update CHANGELOG.md
- Keep documentation clear and concise

## ❓ Questions?

If you have any questions, feel free to:

- Open an issue for discussion
- Contact the maintainers
- Check existing documentation

Thank you for contributing! 🎉
