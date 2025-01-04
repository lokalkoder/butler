<?php

if (! function_exists('avatar')) {
    function avatar(string $name = 'guest'): string
    {
        return 'https://ui-avatars.com/api/?name='.urlencode($name).'&color=7F9CF5&background=EBF4FF';
    }
}
