<?php
/**
 * Version Bump Helper
 * 
 * Helper function to bump version in plugin files
 * This will be called automatically when making changes
 */

function bump_version_patch() {
    $pluginFile = __DIR__ . '/../wc-invoice.php';
    $changelogFile = __DIR__ . '/../CHANGELOG.md';
    
    if (!file_exists($pluginFile)) {
        return false;
    }
    
    // Get current version
    $content = file_get_contents($pluginFile);
    preg_match("/Version:\s*([0-9]+\.[0-9]+\.[0-9]+)/", $content, $matches);
    if (empty($matches[1])) {
        return false;
    }
    
    $currentVersion = $matches[1];
    
    // Increment patch version
    $parts = explode('.', $currentVersion);
    $major = (int)$parts[0];
    $minor = (int)$parts[1];
    $patch = (int)$parts[2];
    $patch++;
    $newVersion = "$major.$minor.$patch";
    
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
    
    // Update CHANGELOG.md
    if (file_exists($changelogFile)) {
        $changelog = file_get_contents($changelogFile);
        $date = date('Y-m-d');
        $newEntry = "## [Unreleased]\n\n## [$newVersion] - $date\n\n### Changed\n- 🔢 Auto-bumped version to $newVersion\n\n";
        $changelog = preg_replace('/## \[Unreleased\]\n\n/', $newEntry, $changelog, 1);
        file_put_contents($changelogFile, $changelog);
    }
    
    return $newVersion;
}

// If called directly from command line
if (php_sapi_name() === 'cli' && basename(__FILE__) === basename($_SERVER['PHP_SELF'])) {
    $newVersion = bump_version_patch();
    if ($newVersion) {
        echo "Version bumped to: $newVersion\n";
    } else {
        echo "Failed to bump version\n";
        exit(1);
    }
}

