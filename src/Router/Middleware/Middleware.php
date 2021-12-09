<?php

namespace Kerwin\Core\Router\Middleware;

use Kerwin\Core\Request;

interface Middleware
{    
    /**
     * __invoke
     *
     * @param  Kerwin\Core\Request $request
     * @param  callable $next
     * @return callable
     */
    public function __invoke(Request $request, callable $next);
}