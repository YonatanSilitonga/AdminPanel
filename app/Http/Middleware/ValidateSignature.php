<?php

namespace App\Http\Middleware;

use Illuminate\Routing\Middleware\ValidateSignature as Middleware;

class ValidateSignature extends Middleware
{
    /**
     * The names of the routes that should be excluded from signature validation.
     *
     * @var array<int, string>
     */
    protected $except = [
        // 'fbwebhook',
        // 'webhook',
    ];
}
