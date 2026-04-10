<?php

use Illuminate\Support\Facades\Route;

Route::get('/debug-route', function () {
    return 'Debug Route Works!';
});
