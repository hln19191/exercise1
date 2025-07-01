<?php

namespace App\Http\Controllers\Settings;

use App\Abstracts\Http\Controller as BaseController;
//use App\Http\Controllers\Controller as BaseController;
use Inertia\Inertia;

class SettingController extends BaseController
{

    public function show() 
    {
        return $this->view();
    }

    public function view()
    {
        return Inertia::render('Settings/View');
    }
}