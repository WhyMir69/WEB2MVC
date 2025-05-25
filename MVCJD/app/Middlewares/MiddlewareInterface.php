<?php

namespace Middlewares;

interface MiddlewareInterface {
    public function handle($request, $next);
}