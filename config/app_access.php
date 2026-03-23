<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Enforce sign-in for /app/* routes
    |--------------------------------------------------------------------------
    |
    | When true, guests are redirected to the login page for any URL under
    | app/ except those listed in guest_paths below.
    | Default is true (secure). Set APP_ENFORCE_AUTH_FOR_APP_ROUTES=false in .env to disable.
    |
    */
    'enforce' => env('APP_ENFORCE_AUTH_FOR_APP_ROUTES', true),

    /*
    |--------------------------------------------------------------------------
    | Guest-accessible paths under the application area
    |--------------------------------------------------------------------------
    |
    | Requests matching these patterns will skip the "must be logged in" check
    | applied to /app/* routes. Use for read-only pages you want public, or
    | leave empty to require sign-in for everything under app (recommended).
    |
    | Each rule:
    | - methods: list of HTTP methods (e.g. GET, HEAD) — only these may bypass auth
    | - patterns: passed to Request::is() — supports wildcards (e.g. prefix with * for subdirectory installs)
    |
    */
    'guest_paths' => [
        // Intentionally empty: all /app/* routes require authentication (including Zone).
        // Add patterns here only for URLs that must stay public without sign-in.
    ],

];
