<?php

namespace App\System\Middleware;

use App\System\Http\RequestBundle;
use App\System\Http\ResponseBundle;

interface MiddlewareInterface
{
    public function handle(RequestBundle $request, callable $next): ResponseBundle;
}