<?php

namespace App\Abstracts\Http;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Routing\Route;
use Illuminate\Support\Str;

abstract class Controller extends BaseController
{
    /**
     * Instantiate a new controller instance.
     */
    public function __construct()
    {
        $this->assignPermissionsToController();
    }

    private function assignPermissionsToController()
    {
         // No need to check for permission in console
        if (app()->runningInConsole()) {
            return;
        }

        $route = app(Route::class);
        $arr = array_reverse(explode('\\', explode('@', $route->getAction()['uses'])[0]));
        $controller = Str::kebab($arr[0]);
        $controller = str_replace('-controller','',$controller);

        if (! str_ends_with($controller, "s")) {
            $controller.='s';
        }

        // Map specific controllers to their permission groups, into 'masters' group
        $permissionMap = [
            'general-settings' => 'settings',
        ];

        $permissionGroup = $permissionMap[$controller] ?? $controller;
        
        //check if the permission exists
        $this->middleware("permission:create-{$permissionGroup}")->only('create', 'store', 'duplicate', 'import');
        $this->middleware("permission:read-{$permissionGroup}")->only('index', 'show', 'edit', 'export');
        $this->middleware("permission:update-{$permissionGroup}")->only('update', 'enable', 'disable');
        $this->middleware("permission:delete-{$permissionGroup}")->only('destroy');  

    }
}

