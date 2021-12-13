<?php

namespace Kerwin\Core\Router\Middleware;

use Closure;
use Kerwin\Core\Request;

interface Middleware
{    
    /**
     * __invoke
     *
     * @param  Kerwin\Core\Request $request
     * @param  callable $next
     * @param  mixed $arg
     * @return callable
     */
    public function __invoke(Request $request, Closure $next, $arg = NULL);
}