<?php
/**
 * Version Bump Script
 * 
 * This script automatically increments the version number in the plugin files.
 * Usage: php scripts/bump-version.php [patch|minor|major]
 * 
 * Default: patch (0.0.1 -> 0.0.2)
 */

if (php_sapi_name() !== 'cli') {
    die('This script can only be run from the command line.');
}

$pluginFile = __DIR__ . '/../wc-invoice.php';
$changelogFile = __DIR__ . '/../CHANGELOG.md';

if (!file_exists($pluginFile)) {
    die("Error: Plugin file not found: $pluginFile\n");
}

// Get current version
$content = file_get_contents($pluginFile);
preg_match("/Version:\s*([0-9]+\.[0-9]+\.[0-9]+)/", $content, $matches);
if (empty($matches[1])) {
    die("Error: Could not find version in plugin file.\n");
}

$currentVersion = $matches[1];
echo "Current version: $currentVersion\n";

// Determine bump type
$bumpType = $argv[1] ?? 'patch';
if (!in_array($bumpType, ['patch', 'minor', 'major'])) {
    die("Error: Invalid bump type. Use: patch, minor, or major\n");
}

// Increment version
$parts = explode('.', $currentVersion);
$major = (int)$parts[0];
$minor = (int)$parts[1];
$patch = (int)$parts[2];

switch ($bumpType) {
    case 'major':
        $major++;
        $minor = 0;
        $patch = 0;
        break;
    case 'minor':
        $minor++;
        $patch = 0;
        break;
    case 'patch':
    default:
        $patch++;
        break;
}

$newVersion = "$major.$minor.$patch";
echo "New version: $newVersion\n";

// Update plugin file
$content = preg_replace(
    "/Version:\s*([0-9]+\.[0-9]+\.[0-9]+)/",
    "Version: $newVersion",
    $content
);
$content = preg_replace(
    "/define\('WC_INVOICE_VERSION',\s*'([0-9]+\.[0-9]+\.[0-9]+)'\);/",
    "define('WC_INVOICE_VERSION', '$newVersion');",
    $content
);

file_put_contents($pluginFile, $content);
echo "✓ Updated $pluginFile\n";

// Update CHANGELOG.md
if (file_exists($changelogFile)) {
    $changelog = file_get_contents($changelogFile);
    $date = date('Y-m-d');
    
    // Add new version entry
    $newEntry = "## [Unreleased]\n\n## [$newVersion] - $date\n\n### Added\n- 🎉 Version bump to $newVersion\n\n";
    
    // Insert after [Unreleased] section
    $changelog = preg_replace(
        '/## \[Unreleased\]\n\n/',
        $newEntry,
        $changelog,
        1
    );
    
    file_put_contents($changelogFile, $changelog);
    echo "✓ Updated $changelogFile\n";
}

echo "\n✅ Version bumped successfully to $newVersion!\n";
echo "Don't forget to commit the changes:\n";
echo "  git add wc-invoice.php CHANGELOG.md\n";
echo "  git commit -m \"chore: bump version to $newVersion\"\n";

