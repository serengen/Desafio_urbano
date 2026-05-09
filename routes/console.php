<?php

use Illuminate\Support\Facades\Artisan;

Artisan::command('about-app', function () {
    $this->info('Sistema basico de gestion de usuarios.');
})->purpose('Muestra informacion corta de la app');
