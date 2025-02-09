<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TrackingController extends Controller
{
    public function index()
    {
        return view('tracking.index');
    }

    public function view(Request $request, $model, $model_id = null)
    {
        return view('tracking.show', ['model' => $model, 'id' => $id]);
    }
}
