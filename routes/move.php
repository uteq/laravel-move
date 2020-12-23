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
        ->name(Move::getPrefix() . '.preview-file');

    // Download
    Route::get('download', DownloadController::class)
        ->name(Move::getPrefix() . '.download')
        ->middleware(ValidateSignature::class);

    if (config(Move::getPrefix() . '.load_resource_routes') === true) {

        // Resources
        Route::get('{resource}/create', ResourceForm::class)
            ->where('resource', '([^0-9]*)')
            ->name(Move::getPrefix() . '.create');

        Route::get('{resource}/{model}/edit', ResourceForm::class)
            ->where('resource', '([^0-9]*)')
            ->name(Move::getPrefix() . '.edit');

        Route::get('{resource}/{model}/show', ResourceShow::class)
            ->where('resource', '([^0-9]*)')
            ->name(Move::getPrefix() . '.show');

        Route::get('{resource}', ResourceTable::class)
            ->where('resource', '(.*)')
            ->name(Move::getPrefix() . '.index');
    }
});
