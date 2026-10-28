<?php

namespace App\Http\Controllers;

abstract class Controller
{
    //

    protected function keepOrderAlert(\Illuminate\Http\RedirectResponse $response): \Illuminate\Http\RedirectResponse
    {
        return $response->with('order_flash', true);
    }
}
