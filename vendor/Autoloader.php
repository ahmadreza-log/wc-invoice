<?php

namespace WC_Invoice;

defined('ABSPATH') || exit;

/**
 * Lightweight PSR-4-style autoloader.
 *
 * This is a micro implementation that doesn't require Composer.
 * The loader maps namespaces to directories and resolves class files on demand.
 */
class Autoloader
{
    /**
     * Map of namespace prefixes to base directories.
     *
     * @var array<string, string>
     */
    protected array $prefixes = [];

    /**
     * Register this loader with SPL so PHP routes class lookups to {@see Autoloader::load()}.
     *
     * @return void
     */
    public function register(): void
    {
        spl_autoload_register([$this, 'load']);
    }

    /**
     * Configure a namespace prefix → directory mapping.
     *
     * @param string $prefix    Namespace prefix, e.g. `WC_Invoice`.
     * @param string $directory Absolute or plugin-relative path.
     *
     * @return void
     */
    public function namespace($prefix, $directory): void
    {
        $prefix = trim($prefix, '\\') . '\\';
        $directory = rtrim($directory, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

        $this->prefixes[$prefix] = $directory;
    }

    /**
     * Attempt to resolve a class name into a file path using registered prefixes.
     *
     * @param string $class Fully qualified class name.
     *
     * @return void
     */
    public function load($class): void
    {
        foreach ($this->prefixes as $prefix => $directory) {
            $length = strlen($prefix);

            if (strncmp($prefix, $class, $length) !== 0) {
                continue;
            }

            $relative = substr($class, $length);
            $file = $directory . str_replace('\\', DIRECTORY_SEPARATOR, $relative) . '.php';

            if (file_exists($file)) {
                require $file;
                return;
            }
        }
    }
}

