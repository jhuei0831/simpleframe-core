<?php

namespace Kerwin\Core\Router\Middleware;

use Kerwin\Core\Router\Middleware\MiddlewareStack;
use Kerwin\Core\Router\Middleware\Middleware;

class MiddlewareRunner
{
    protected $middleware;

	public function __construct(MiddlewareStack $middleware) {
		$this->middleware = $middleware;
	}

	public function add(Middleware $middleware)
	{
		$this->middleware->add($middleware);
	}

	public function run()
	{
		$this->middleware->handle();
	}
}