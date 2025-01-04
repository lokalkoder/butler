<?php

if (! function_exists('current_filename')) {
    function current_filename(string $filename): string
    {
        return strstr(basename($filename), '.php', true);
    }
}

if (! function_exists('include_all')) {
    function include_all(string $path)
    {
        collect(glob($path))
            ->each(function ($path) {
                if (basename($path) !== basename(__FILE__)) {
                    require $path;
                }
            });
    }
}
