<?php

namespace Middlewares;

class MiddlewarePipeline {
    private $middlewares = [];

    public function add(MiddlewareInterface $middleware) {
        $this->middlewares[] = $middleware;
    }

    public function handle($request, $finalHandler) {
        $handler = array_reduce(
            array_reverse($this->middlewares),
            function ($next, $middleware) {
                return function ($request) use ($middleware, $next) {
                    return $middleware->handle($request, $next);
                };
            },
            $finalHandler
        );

        return $handler($request);
    }
}
