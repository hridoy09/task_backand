<?php

use Illuminate\Support\Facades\Route;

Route::fallback(function () {
    return response()->json([
        'status'  => 'error',
        'message' => 'API endpoint not found.',
        'code'    => 404,
    ], 404);
});
