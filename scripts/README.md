# 📜 Scripts Documentation

This directory contains utility scripts for the WC Invoice plugin.

## 🔢 Version Management

### Manual Version Bump

Use the `bump-version.php` script to manually increment the version:

```bash
# Bump patch version (0.0.1 -> 0.0.2)
php scripts/bump-version.php patch

# Bump minor version (0.0.1 -> 0.1.0)
php scripts/bump-version.php minor

# Bump major version (0.0.1 -> 1.0.0)
php scripts/bump-version.php major
```

The script will:
- ✅ Update version in `wc-invoice.php` (header and constant)
- ✅ Update `CHANGELOG.md` with new version entry
- ✅ Display instructions for committing changes

### Automatic Version Bump

#### Option 1: Git Pre-Push Hook (Recommended)

Install the pre-push hook to automatically bump version when pushing to main/master:

```bash
bash scripts/install-hooks.sh
```

The hook will:
- ✅ Automatically detect if plugin code changed
- ✅ Bump patch version (0.0.1 -> 0.0.2)
- ✅ Update version files
- ✅ Stage changes for commit
- ✅ Ask for confirmation before pushing

#### Option 2: GitHub Actions

The GitHub Actions workflow (`.github/workflows/bump-version.yml`) automatically:
- ✅ Runs on push to main/master branch
- ✅ Bumps patch version
- ✅ Updates version files
- ✅ Commits and pushes changes
- ✅ Creates a git tag

**Note:** Make sure to set `GITHUB_TOKEN` permissions in repository settings.

## 📋 Version Format

We follow [Semantic Versioning](https://semver.org/):
- **MAJOR**: Breaking changes
- **MINOR**: New features (backward compatible)
- **PATCH**: Bug fixes (backward compatible)

Current format: `0.0.1`

## 🔍 Version Check

The GitHub Actions workflow (`.github/workflows/version-check.yml`) verifies:
- ✅ Version in plugin header matches constant
- ✅ Version consistency across files

## 📝 Files Updated

When version is bumped, these files are updated:
1. `wc-invoice.php` - Plugin header and `WC_INVOICE_VERSION` constant
2. `CHANGELOG.md` - New version entry with date

## 🚀 Usage Examples

### Development Workflow

```bash
# Make your changes
git add .
git commit -m "feat: add new feature"

# Push to main (hook will auto-bump version)
git push origin main
```

### Manual Release

```bash
# Bump to new version
php scripts/bump-version.php minor

# Review changes
git diff

# Commit and tag
git add wc-invoice.php CHANGELOG.md
git commit -m "chore: bump version to 0.1.0"
git tag -a "v0.1.0" -m "Version 0.1.0"
git push origin main --tags
```

## ⚙️ Configuration

### Skip Auto-Bump

To skip automatic version bumping, include `[skip version]` in your commit message:

```bash
git commit -m "docs: update README [skip version]"
```

### Custom Version

For custom version numbers, manually edit:
- `wc-invoice.php` (line 7 and line 33)
- `CHANGELOG.md` (add new entry)

## 🐛 Troubleshooting

### Hook Not Working

1. Check if hook is installed: `ls -la .git/hooks/pre-push`
2. Check hook permissions: `chmod +x .git/hooks/pre-push`
3. Reinstall: `bash scripts/install-hooks.sh`

### Version Mismatch

If version numbers don't match:
1. Run version check: Check GitHub Actions
2. Manually sync versions in `wc-invoice.php`
3. Use bump script to fix: `php scripts/bump-version.php patch`

## 📚 Related Files

- `.github/workflows/bump-version.yml` - GitHub Actions workflow
- `.github/workflows/version-check.yml` - Version consistency check
- `.githooks/pre-push` - Git pre-push hook
- `CHANGELOG.md` - Version history

