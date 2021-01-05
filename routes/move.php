<?php

use Illuminate\Routing\Middleware\ValidateSignature;
use Illuminate\Support\Facades\Route;
use Uteq\Move\Controllers\DownloadController;
use Uteq\Move\Controllers\PreviewFileController;
use Uteq\Move\Facades\Move;
use Uteq\Move\Livewire\ResourceForm;
use Uteq\Move\Livewire\ResourceShow;
use Uteq\Move\Livewire\ResourceTable;

Route::bind('model', function ($value) {
    $resource = Move::activeResource();

    return $resource->model()::find($value) ?: $resource::newModel();
});

Route::group(['middleware' => Move::routeMiddlewares()], function () {

    Route::get('preview-file/{filename}', PreviewFileController::class)
        ->name(move()::getPrefix() . '.preview-file');

    // Download
    Route::get('download', DownloadController::class)
        ->name(move()::getPrefix() . '.download')
        ->middleware(ValidateSignature::class);

    if (config(move()::getPrefix() . '.load_resource_routes') === true) {

        // Resources
        Route::get('{resource}/create', ResourceForm::class)
            ->where('resource', '([^0-9]*)')
            ->name(move()::getPrefix() . '.create');

        Route::get('{resource}/{model}/edit', ResourceForm::class)
            ->where('resource', '([^0-9]*)')
            ->name(move()::getPrefix() . '.edit');

        Route::get('{resource}/{model}/show', ResourceShow::class)
            ->where('resource', '([^0-9]*)')
            ->name(move()::getPrefix() . '.show');

        Route::get('{resource}', ResourceTable::class)
            ->where('resource', '(.*)')
            ->name(move()::getPrefix() . '.index');
    }
});
