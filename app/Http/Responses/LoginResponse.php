<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use Symfony\Component\HttpFoundation\Response;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request): Response
    {
        $path = $request->user()->dashboardPath();

        return $request->wantsJson()
            ? new JsonResponse(['two_factor' => false, 'redirect' => $path])
            : redirect()->intended($path);
    }
}
