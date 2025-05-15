<?php

use Illuminate\Support\Facades\Route;
use Pterodactyl\BlueprintFramework\Extensions\{identifier}\ExtensionDashboardController;

Route::post('/deleteExtension', [ExtensionDashboardController::class, 'deleteExtension']);
Route::post('/upload', [ExtensionDashboardController::class, 'upload']);