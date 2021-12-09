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
	 * @param  Kerwin\Core\Router\Middleware\Middleware $middleware
	 * @return void
	 */
	public function add(Middleware $middleware)
	{
		$this->middleware->add($middleware);
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