<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MakeViewCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:view {folder} {name?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new view in base to Layout AdminLTE';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $folder = $this->camelCaseToSnakeCase($this->argument('folder'));
        $name = $this->argument('name') ? $this->camelCaseToSnakeCase($this->argument('name')) : null;

        $pathStr = isset($name) ? $folder . '/' . $name : $folder;

        $path = resource_path('views/' . $pathStr . '.blade.php');

        $directory = dirname($path);
        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        if (File::exists($path)) {
            $this->error('View already exists!');
            return 1;
        }
        $viewName = $name ?? $folder;
        File::put($path, $this->getView($viewName));

        $pathStr = str_replace('/', '.', $pathStr);
        $this->info($pathStr . ' created successfully.');

        return 0;
    }
    private function getView($name): string
    {
        return '@extends("adminlte::page")

        @section("title", "'.$name.'")

        @section("content_header")
            <h1>'.$name.'</h1>
        @stop

        @section("content")
            <p>'.$name.'</p>
        @stop

        @section("css")
            {{-- Add here extra stylesheets --}}
        @stop

        @section("js")
            {{-- Add here extra javascript --}}
        @stop';
    }

    private function camelCaseToSnakeCase($input): string
    {
        $str = strtolower(preg_replace_callback('/[A-Z]/', function ($match) {
            return "_" . strtolower($match[0]);
        }, $input));
        return ltrim($str, '_');
    }
}
