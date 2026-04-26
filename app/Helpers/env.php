<?php

/**
 * env() — portable environment variable reader.
 *
 * Checks $_ENV first (populated by public/index.php .env loader when running
 * locally), then falls back to getenv() which is how Railway / Docker / any
 * PaaS injects OS-level environment variables into the process.
 *
 * Usage: env('MAIL_HOST', 'localhost')
 *
 * @param  string $key     The environment variable name.
 * @param  mixed  $default Returned when the key is absent or empty.
 * @return mixed
 */
if (!function_exists('env')) {
    function env(string $key, $default = null) {
        // $_ENV is populated by the custom .env file loader in public/index.php
        if (isset($_ENV[$key]) && $_ENV[$key] !== '') {
            return $_ENV[$key];
        }

        // getenv() is populated by the OS / Railway / Docker / Apache SetEnv etc.
        $val = getenv($key);
        if ($val !== false && $val !== '') {
            return $val;
        }

        return $default;
    }
}
