<?php

namespace App\Http\Controllers;

use App\Jobs\CloneInvoiceTableJob;
use App\Models\CloningControl;
use Illuminate\Http\Request;
use App\permissionCategories;

class PermissionsController extends Controller
{

    public function index()
    {
        return view('permissions.list', ['categories' => collect(permissionCategories::cases())->pluck('name', 'value')]);
    }

    public function create()
    {
        return view('permissions.create', ['categories' => collect(permissionCategories::cases())]);
    }

    
    public function store(Request $request)
    {
        //
    }

    public function show(Request $request)
    {

    }

    /*public function list(Request $request, $model, $account_id = null)
    {
        $notIsCompleted = $request->get('not_is_completed')??null;
        return view('permissions.list', ['model' => $model, 'account_id' => $account_id, 'notIsCompleted' => $notIsCompleted]);
    }*/

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

    public function complete(Request $request, $clone_id)
    {
        $control = CloningControl::find($clone_id);
        if(!$control){
            return redirect()->route('permissions.list', ['model' => $control->model])->with('error', 'No se encontró el registro de clonación.');
        }
        if($control->is_completed){
            return redirect()->route('permissions.list', ['model' => $control->model])->with('error', 'El proceso de clonación ya ha sido completado.');
        }
        CloneInvoiceTableJob::dispatch($control->from_date, $control->to_date, $control->account_id, $control->id);
        return redirect()->route('permissions.list', ['model' => $control->model])->with('success', 'Se ha iniciado el proceso de clonación de facturas.');
    }
}
