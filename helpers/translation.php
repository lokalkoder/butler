<?php

if (! function_exists('translator')) {
    function translator(string $locale, array $category = []): array
    {
        $source = collect(glob(resource_path('lang/'.$locale.'/*.json')));

        if (filled($category)) {
            $category = array_merge($category, [$locale]);

            $source = $source->reject(function ($path) use ($category) {
                $name = str_replace(['.json', '.php'], '', basename($path));

                return ! in_array($name, $category);
            });
        }

        return $source->mapWithKeys(function ($path) {
            $baseName = basename($path);
            $name = stristr($baseName, '.', true);
            $basePath = str($baseName);

            if ($basePath->contains('.json')) {
                $content = file_exists($path) ? json_decode(file_get_contents($path), true) : [];
            } else {
                $content = require_once $path;
            }

            return [$name => $content];

        })->toArray();
    }
}

if (! function_exists('localize')) {
    function localize(string $key, ?string $category = null): string
    {
        if ($category === null) {
            return trans($key);
        }

        $translation = translator(app()->getLocale(), config('locale.translation', []));

        return data_get(data_get($translation, $category), $key, $key);
    }
}
