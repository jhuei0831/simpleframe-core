<?php

namespace Kerwin\Core\Router\Middleware;

use Kerwin\Core\Request;
use Kerwin\Core\Router\Middleware\Middleware;

class MiddlewareStack
{
    protected $start;

    public function __construct(callable $handle) {
        $this->start = $handle;
    }
        
    /**
     * 堆疊新增middleware
     *
     * @param  Kerwin\Core\Router\Middleware\Middleware $middleware
     * @param  mixed $arg
     * @return callable
     */
    public function add(Middleware $middleware, $arg = NULL)
    {
        $next = $this->start;
        $request = Request::createFromGlobals();
        $this->start = function() use ($middleware, $request, $next, $arg) {
            return $middleware($request, $next, $arg);
        };
    }
    
    /**
     * 執行middleware
     *
     * @return mixed
     */
    public function handle()
    {
        return call_user_func($this->start);
    }
}
