<?php

return [
    'middleware_group' => 'auth',
    'middleware' => 'auth',
    'prefix' => 'livewire',
    'class_namespace' => 'App\\Livewire',
    'view_path' => resource_path('views/livewire'),
    'layout' => 'adminlte::page',
];
