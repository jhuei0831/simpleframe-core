<?php

namespace Kerwin\Core\Router\Middleware;

use Symfony\Component\HttpFoundation\Request;

interface Middleware
{
    public function __invoke(Request $request, callable $next);
}