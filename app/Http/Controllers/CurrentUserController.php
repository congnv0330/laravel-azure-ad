<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CurrentUserController extends Controller
{
    public function show(Request $request)
    {
        return $this->response($request->user());
    }
}
