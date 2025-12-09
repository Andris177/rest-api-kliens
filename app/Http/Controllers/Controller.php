<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    protected ?string $token = null;

    public function __construct()
    {
        $this->token = session('api_token');
    }

    public function isAuthenticated(): bool
    {
        return session()->has('api_token');
    }
}
