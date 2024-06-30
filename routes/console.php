<?php

use App\Console\Commands\CloneInvoiceTableCommand;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use \App\Console\Commands\MakeViewCommand;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

//Schedule::command(CloneInvoiceTableCommand::class)->everyMinute()
//    ->cron('*/40 * * * *');
