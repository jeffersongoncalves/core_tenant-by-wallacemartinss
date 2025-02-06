<?php

use App\Livewire\QrCodeModal;

use App\Http\Controllers\EvolutionWebhookController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StripeWebhookController;

Route::get('/', function () {
    return redirect('/app');
});

//Rota do webhook custom stripe
Route::post('/stripe/webhook', [StripeWebhookController::class, 'handle']);

//Rota do webhook custom evolution
Route::post('/evolution/webhook', [EvolutionWebhookController::class, 'handle']);


