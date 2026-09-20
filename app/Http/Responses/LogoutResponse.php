<?php

namespace App\Http\Responses;
use Laravel\Fortify\Contracts\LogoutResponse as LogoutResponseContract;

class LogoutResponse implements LogoutResponseContract
{
    public function toResponse($request)
    {
        return redirect()->to('/'.app()->getLocale())->setStatusCode(303);
    }
}
