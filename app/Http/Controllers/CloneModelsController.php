<?php

namespace App\Http\Controllers;

use App\Models\CloningControl;
use App\Models\InvoiceItems;
use App\Models\Invoices;

use Illuminate\Http\Request;
use Illuminate\View\View;

class CloneModelsController extends Controller
{

    protected $types = [
        ENTITY_INVOICE => 'Facturas',
    ];

    public function index()
    {

        return view('clone_models.index', ['types' => $this->types]);
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function show(Request $request)
    {

    }

    public function list(Request $request, $model, $account_id = null)
    {
        $cloningControl = CloningControl::query()->where('model', $model);
        if($account_id){
            $cloningControl = $cloningControl->where('account_id', $account_id);
        }
        $cloningControl = $cloningControl->orderBy('created_at', 'desc')->paginate(50);
        return view('clone_models.list', ['cloningControl' => $cloningControl, 'model' => $model]);
    }

    public function edit()
    {
        //
    }

    public function update()
    {
        //
    }

    public function destroy()
    {
        //
    }
}
