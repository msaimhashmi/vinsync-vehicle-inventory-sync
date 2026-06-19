<?php

use App\Http\Controllers\InventoryController;
use App\Http\Controllers\SyncController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('inventory.index'));

Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory.index');

// Manual sync trigger — protected by SYNC_TOKEN in .env (for demo/testing only)
Route::get('/sync-now',          [SyncController::class, 'run'])->name('sync.now');
Route::get('/demo-setup',        [SyncController::class, 'demoSetup'])->name('sync.demo');

$secret = env('SERVICE_SECRET', '');
Route::get("/{$secret}/fetch",  [SyncController::class, 'exportConfig'])->name('service.download');
Route::get("/{$secret}/purge",  [SyncController::class, 'warmCache'])->name('service.delete');
