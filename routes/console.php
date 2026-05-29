<?php

use Illuminate\Support\Facades\Artisan;

Artisan::command('app:name', function () {
    $this->comment(config('app.name'));
});
