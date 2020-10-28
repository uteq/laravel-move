<?php

use Illuminate\Routing\Middleware\ValidateSignature;
use Illuminate\Support\Facades\Route;
use Uteq\Move\Controllers\DownloadController;
use Uteq\Move\Controllers\MoveJavaScriptAssets;
use Uteq\Move\Controllers\MoveStyleAssets;
use Uteq\Move\Controllers\PreviewFileController;
use Uteq\Move\Facades\Move;
use Uteq\Move\Livewire\ResourceForm;
use Uteq\Move\Livewire\ResourceShow;
use Uteq\Move\Livewire\ResourceTable;

Route::get('/move/move.js', [MoveJavaScriptAssets::class, 'source']);
Route::get('/move/move.css', [MoveStyleAssets::class, 'source']);

Route::bind('model', function ($value) {
    $resource = Move::resolveResource(request()->route()->parameter('resource'));

    return $resource->model()::find($value) ?: $resource::newModel();
});

Route::group(['middleware' => Move::routeMiddlewares()], function () {

    Route::get('preview-file/{filename}', PreviewFileController::class)
        ->name('move.preview-file');

    // Download
    Route::get('download', DownloadController::class)
        ->name('move.download')
        ->middleware(ValidateSignature::class);

    // Resources
    Route::get('{resource}/create', ResourceForm::class)
        ->where('resource', '([^0-9]*)')
        ->name('move.create');

    Route::get('{resource}/{model}/edit', ResourceForm::class)
        ->where('resource', '([^0-9]*)')
        ->name('move.edit');

    Route::get('{resource}/{model}/show', ResourceShow::class)
        ->where('resource', '([^0-9]*)')
        ->name('move.show');

    Route::get('{resource}', ResourceTable::class)
        ->where('resource', '(.*)')
        ->name('move.index');
});
