<?php

return [
    'enabled' => env('CLOUDFLARE_TURNSTILE_ENABLED', true),
    'site_key' => env('CLOUDFLARE_TURNSTILE_SITE_KEY'),
    'secret_key' => env('CLOUDFLARE_TURNSTILE_SECRET_KEY'),
];
