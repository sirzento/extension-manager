<?php

use Illuminate\Support\Facades\Route;
use Pterodactyl\BlueprintFramework\Extensions\{identifier}\ExtensionDashboardController;

Route::post('/deleteExtension', [ExtensionDashboardController::class, 'deleteExtension']);
Route::post('/upload', [ExtensionDashboardController::class, 'upload']);


// TODO
// Prüfen was passiert wenn man sich mit root oder sudo user anmeldet
// Ladekreis beim Installieren/Löschen