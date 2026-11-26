<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CustomException extends Exception
{
    public function render(Request $request): Response
    {
        return response()->view('errors.custom', [
            'message' => $this->getMessage()
        ], 500);
    }
}
