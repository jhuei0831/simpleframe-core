<?php

namespace Kerwin\Core\Router\Middleware;

use Kerwin\Core\Router\Middleware\MiddlewareStack;
use Kerwin\Core\Router\Middleware\Middleware;

class MiddlewareDriver
{
    protected $middleware;

	public function __construct(MiddlewareStack $middleware) {
		$this->middleware = $middleware;
	}
	
	/**
	 * 已堆疊方式新增middleware
	 *
	 * @param  Middleware $middleware
	 * @param  mixed $arg
	 * @return void
	 */
	public function add(Middleware $middleware, $arg = NULL)
	{
        $this->middleware->add($middleware, $arg);
	}
	
	/**
	 * 執行middleware
	 *
	 * @return void
	 */
	public function run()
	{
		$this->middleware->handle();
	}
}