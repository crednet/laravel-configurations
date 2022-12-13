<?php

use Credpal\Configurations\Http\Controllers\ConfigurationController;
use Illuminate\Support\Facades\Route;

Route::apiResource('configurations', ConfigurationController::class)
    ->except('update');
