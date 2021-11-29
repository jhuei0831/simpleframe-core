<?php

namespace Kerwin\Core\Router\Middleware;

use Kerwin\Core\Router\Middleware\Middleware;
use Symfony\Component\HttpFoundation\Request;

class MiddlewareStack
{
    protected $start;

    public function __construct($handle) {
        $this->start = $handle;
    }
    
    public function add(Middleware $middleware)
    {
        $next = $this->start;
        $request = Request::createFromGlobals();
        $this->start = function() use ($middleware, $request, $next) {
            return $middleware($request, $next);
        };
    }

    public function handle()
    {
        return call_user_func($this->start);
    }
}
