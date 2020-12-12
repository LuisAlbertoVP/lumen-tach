<?php

namespace App\Providers;

use App\Models\User;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    public function register() {}

    public function boot() {
        $this->app['auth']->viaRequest('api', function ($request) {
            if ($jwt = $request->bearerToken()) {
                $decoded = JWT::decode($jwt, env('TOKEN'), array('HS256'));
                $user = new User();
                $user->auth = $decoded;
                return $user;
            }
        });
    }
}