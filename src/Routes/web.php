<?php

Route::namespace('Amstaff\Generator\Controllers')->as('generator::')->middleware('web')->group(function () {
    // Routes defined here have the web middleware applied
    // like the web.php file in a laravel project
    // They also have an applied controller namespace and a route names.

    Route::middleware('generator')->group(function () {
        // Routes defined here have the self-assigned middleware applied.
        // By default this middleware is empty.
    });
});
