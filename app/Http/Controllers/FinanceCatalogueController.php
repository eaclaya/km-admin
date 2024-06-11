<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;

use Redirect;
use Auth;
use Session;
use Carbon;
use DB;

use App\Models\FinanceCatalogueClassification;
use App\Models\FinanceCatalogueItem;

use App\Models\Employee;
use App\Models\CompanyAreas;
use App\Models\CompanyZones;
use App\Models\Account;
use OpenApi\Annotations\Get;

class FinanceCatalogueController extends Controller
{
    Protected $models = [
        'accounts' => 'Tienda',
        'organization_company' => 'Empresas',
        'expense_category' => 'Categorias de Gastos',
        'expense_subcategory' => 'Sub Categorias de Gastos',
    ];

    public function __construct()
    {
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $filter = $request->input('filter');
        $itemsFilter = FinanceCatalogueItem::orderBy('sort', 'ASC')->whereNull('sub_item_id')->get();
        $items = FinanceCatalogueItem::orderBy('sort', 'ASC')->whereNull('sub_item_id')->with('subItems');
        if(isset($filter)){
            if ($filter !== 'all'){
                $items = $items->where('id', $filter);
            }
        }else{
            $items = $items->where('id', 1);
            $filter = 1;
        }
        $items = $items->get();
        $clasifications = FinanceCatalogueClassification::orderBy('sort', 'ASC')->get()->keyBy('id');
        return view('finance_catalogue.list', ['items' => $items, 'clasifications' => $clasifications, 'models' => $this->models, 'filter' => $filter, 'itemsFilter' => $itemsFilter]);
    }

    public function showClassifications()
    {
        $items = FinanceCatalogueClassification::orderBy('sort', 'ASC')->get()->keyBy('id');
        return view('finance_catalogue.list_class', ['items' => $items, 'models' => $this->models]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create(Request $request)
    {
        $data = $request->all();

        if(!isset($data) || count($data) == 0){
            Session::flash('message', "No se proporciono informacion necesaria para crear este proceso de evaluacion");
            return Redirect::back();
        }

        unset($data['_token']);
        $cycle = $data['cycle'];
        unset($data['cycle']);
        $finance_catalogue_type = $data['finance_catalogue_type'];
        unset($data['finance_catalogue_type']);

        if((!isset($cycle) || trim($cycle) == '' || is_null($cycle)) || (!isset($finance_catalogue_type) || trim($finance_catalogue_type) == '' || is_null($finance_catalogue_type))){
            Session::flash('message', "No se proporciono informacion necesaria para crear este proceso de evaluacion");
            return Redirect::back();
        }

        $model = null;
        $account_id = null;
        $zone_id = null;
        $employee_id = null;

        $checkToday = FinanceCatalogue::query()->where('finance_catalogue_type', $finance_catalogue_type)->where('cycle', $cycle);

        if(count($data) > 0){
            foreach ($data as $key => $value) {
                switch ($finance_catalogue_type) {
                    case 0:
                        if(!isset($value) || trim($value) == ''){
                            Session::flash('message', "No se proporciono informacion necesaria para crear este proceso de evaluacion");
                            return Redirect::back();
                        }
                        $model = Employee::select('id', DB::raw('CONCAT(first_name, " ", last_name) as name'))->find($value);
                        $employee_id = $value;
                        $checkToday = $checkToday->where('evaluation_employee_id',$value);
                        break;

                    case 1:
                        if(!isset($value) || trim($value) == ''){
                            Session::flash('message', "No se proporciono informacion necesaria para crear este proceso de evaluacion");
                            return Redirect::back();
                        }
                        $model = Employee::select('id', DB::raw('CONCAT(first_name, " ", last_name) as name'))->where('account_id',$value)->get();
                        $account_id = $value;
                        $checkToday = $checkToday->where('evaluation_account_id',$value);
                        break;

                    case 2:
                        if(!isset($value) || trim($value) == ''){
                            Session::flash('message', "No se proporciono informacion necesaria para crear este proceso de evaluacion");
                            return Redirect::back();
                        }
                        $model = CompanyZones::select('id', 'name')->with('accounts')->find($value)->accounts;
                        $zone_id = $value;
                        $checkToday = $checkToday->where('evaluation_zone_id',$value);
                        break;

                    default:
                        $model = null;
                        break;
                }
            }
        }else{
            switch ($finance_catalogue_type) {
                case 0:
                case 1:
                case 2:
                    Session::flash('message', "No se proporciono informacion necesaria para crear este proceso de evaluacion");
                    return Redirect::back();
                    break;
                case 3:
                    $model = CompanyZones::select('id', 'name')->get();
                    break;

                default:
                    $model = null;
                    break;
            }
        }

        $checkToday = $checkToday->orderBy('created_at', 'desc')->first();

        $fields = FieldsFinanceCatalogue::where('finance_catalogue_type',$finance_catalogue_type)->whereNull('sub_concept_id')->with('subFields')->get();

        if (isset($checkToday) && $checkToday !== null) {
            $checkYear = Carbon::parse($checkToday->created_at)->year;
            $currentYear = Carbon::now()->year;
            if($checkYear == $currentYear){
                Session::flash('message', "Ya se a creado un FinanceCatalogue de esta tipo por este ciclo");
                return view('finance_catalogue.edit', ['fields' => $fields, 'FinanceCatalogue' => $checkToday, 'model' => $model]);
            }
        }
        $user = Auth::user();

        $financeCatalogue = new FinanceCatalogue();
        $financeCatalogue->cycle = $cycle;
        $financeCatalogue->finance_catalogue_type = $finance_catalogue_type;
        $financeCatalogue->user_id = $user->id;
        $financeCatalogue->real_user_id = $user->realUser()->id;
        $financeCatalogue->evaluation_employee_id = ($finance_catalogue_type == 0) ? $employee_id : null;
        $financeCatalogue->evaluation_account_id = ($finance_catalogue_type == 1) ? $account_id : null;
        $financeCatalogue->evaluation_zone_id = ($finance_catalogue_type == 2) ? $zone_id : null;
        $financeCatalogue->evaluation_superviser_employee_id = null;
        $financeCatalogue->save();

        $items = [];
        foreach ($model as $currentModel) {
            foreach ($fields as $field) {
                $items[] = [
                    'finance_catalogue_id' => $financeCatalogue->id,
                    'user_id' => $user->id,
                    'real_user_id' => $user->realUser()->id,
                    'evaluation_employee_id' => ($finance_catalogue_type == 0) ? $employee_id : (($finance_catalogue_type == 1) ? $currentModel->id : null),
                    'evaluation_account_id' => ($finance_catalogue_type == 1) ? $account_id : (($finance_catalogue_type == 2) ? $currentModel->id : null),
                    'evaluation_zone_id' => ($finance_catalogue_type == 2) ? $zone_id : (($finance_catalogue_type == 3) ? $currentModel->id : null),
                    'evaluation_superviser_employee_id' => null,
                    'finance_catalogue_type' => $finance_catalogue_type,
                    'fields_finance_catalogue_id' => $field->id,
                    'percentage' => 0,
                    'created_at' => $financeCatalogue->created_at,
                ];
                if(isset($field->subFields) && count($field->subFields) > 0) {
                    foreach ($field->subFields as $subField) {
                        $items[] = [
                            'finance_catalogue_id' => $financeCatalogue->id,
                            'user_id' => $user->id,
                            'real_user_id' => $user->realUser()->id,
                            'evaluation_employee_id' => ($finance_catalogue_type == 0) ? $employee_id : (($finance_catalogue_type == 1) ? $currentModel->id : null),
                            'evaluation_account_id' => ($finance_catalogue_type == 1) ? $account_id : (($finance_catalogue_type == 2) ? $currentModel->id : null),
                            'evaluation_zone_id' => ($finance_catalogue_type == 2) ? $zone_id : (($finance_catalogue_type == 3) ? $currentModel->id : null),
                            'evaluation_superviser_employee_id' => null,
                            'finance_catalogue_type' => $finance_catalogue_type,
                            'fields_finance_catalogue_id' => $subField->id,
                            'percentage' => 0,
                            'created_at' => $financeCatalogue->created_at,
                        ];
                    }
                }
            }
        }

        $financeCatalogue->items()->insert($items);
        $financeCatalogue->save();

        return view('finance_catalogue.edit', ['fields' => $fields,'FinanceCatalogue' => $financeCatalogue]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        dd('no');
        $fields = $request->fields;

        $financeCatalogueNew = new FinanceCatalogue;

        $financeCatalogueNew->user_id = Auth::user()->realUser()->id;
        $financeCatalogueNew->account_id = Auth::user()->account_id;
        $financeCatalogueNew->is_verify = 0;
        $financeCatalogueNew->notes = null;
        $financeCatalogueNew->is_complete = 0;
        $financeCatalogueNew->comments = null;

        $financeCatalogueNew->save();

        $newFields = array();

        $itemsTracesRequestFields = array();

        foreach ($fields as $key => &$value) {
            $newFields[$key]['finance_catalogue_id'] = $financeCatalogueNew->id;
            $newFields[$key]['fields_finance_catalogue_id'] = $value['id'];
            $newFields[$key]['is_check'] = ($value['check'] == 'true') ? 1 : 0;
            $newFields[$key]['comments'] = (trim($value['comments']) !== '') ? trim($value['comments']) : null;
            $newFields[$key]['user_id'] = $financeCatalogueNew->user_id;
            $newFields[$key]['account_id'] = $financeCatalogueNew->account_id;

            if (isset($value['report']) && $value['report'] == 'true') {
                if(trim($value['comments']) == ''){ continue; };
                if(!isset($value['area']) || trim($value['area']) == ''){ continue; };

                $itemsTracesRequestFields[$key]['fields'][0]['description'] = (trim($value['comments']) !== '') ? trim($value['comments']) : null;
                $itemsTracesRequestFields[$key]['fields'][0]['img'] = $value['img'];
                $itemsTracesRequestFields[$key]['area'] = $value['area'];
            }
        }
        ItemsFinanceCatalogue::insert($newFields);

        foreach ($itemsTracesRequestFields as $item) {
            $this->storeTracesRequest($item);
        }

        Session::flash('message', "FinanceCatalogue Creado");
        return redirect()->route('FinanceCatalogue.edit',$financeCatalogueNew->id);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        /* --- */
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit(Request $request, $id)
    {
        dd($request->all());
        $financeCatalogue = FinanceCatalogue::with('items')->find($id);
        $model = null;
        $fields = FieldsFinanceCatalogue::where('finance_catalogue_type',$financeCatalogue->finance_catalogue_type)->whereNull('sub_concept_id')->with('subFields')->get();
        $user = Auth::user();

        // $financeCatalogue->items()->insert($items);
        $financeCatalogue->save();

        return view('finance_catalogue.edit', ['fields' => $fields,'FinanceCatalogue' => $financeCatalogue, 'model' => $model]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        dd($request->all());
        $items = isset($request->items) ? $request->items : [];
        $financeCatalogue = FinanceCatalogue::with('items')->find($id);
        $notes = isset($request->notes) ? $request->notes : '';

        foreach($financeCatalogue->items as &$item){
            $percentage = isset($items[$item->id]['percentage']) ? $items[$item->id]['percentage'] : 0;
            if($percentage == $item->percentage || $percentage == 0){
                continue;
            }
            $item->percentage = $percentage;
            $item->save();
        }

        if(trim($notes) !== ''){
            $financeCatalogue->notes = $notes;
            $financeCatalogue->save();
        }
        Session::flash('message', "FinanceCatalogue Actualizado");
        return Redirect::back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $list = FinanceCatalogue::with('items')->find($id);
        $list->items()->delete();
        $list->delete();
        Session::flash('message', "FinanceCatalogue Eliminado");
        return Redirect::back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        $list = FinanceCatalogue::with('items')->find($id);
        $list->items()->delete();
        $list->delete();
        Session::flash('message', "FinanceCatalogue Eliminado");
        return Redirect::back();
    }

    public function export()
	{
		$items = FinanceCatalogueItem::orderBy('sort', 'ASC')->whereNull('sub_item_id')->with('subItems')->get();
        $columns = FinanceCatalogueClassification::orderBy('sort', 'ASC')->get()->keyBy('id');

        $data = [];

        foreach ($items as $item) {
            $data = array_merge($data,$this->reloadDataExport($item, [], $columns));
        }

        $columns = $columns->pluck('name')->toArray();

		$date = date('Y-m-d');
		$fileName = 'finance_catalogue'.'_'.$date.".csv";
		$fp = fopen($fileName, 'w');
        $bom = "\xEF\xBB\xBF";

		$columns = array_merge($columns, [
            'Clasificación',
            'Nombre de la Cuenta',
            'Modelo Anclado',
            'Id Anclado',
        ]);
//        dd([$columns, $data]);

        fwrite($fp, $bom);
		fputcsv($fp, CSV_SEPARATOR, ';');
        fputcsv($fp, $columns, ';');
        foreach($data as $fields){
			fputcsv($fp, $fields, ';');
		}
		fclose($fp);
		return redirect('/'.$fileName);
	}

    public function reloadDataExport($item, $oldersClasificationIds, $clasifications){
        $data = [];
        $oldersClasificationIds[$item->finance_catalogue_classification_sort] = $item->sort;
        foreach ($clasifications as $classification){
            if($classification->id < $item->finance_catalogue_classification_sort){
                $data[] = str_pad($oldersClasificationIds[$classification->id], $classification->items_qty, "0", STR_PAD_LEFT);
            }elseif($classification->id == $item->finance_catalogue_classification_sort){
                $data[] = str_pad($item->sort, $classification->items_qty, "0", STR_PAD_LEFT);
            }else{
                $data[] = str_pad(0, $classification->items_qty, "0", STR_PAD_LEFT);
            }
        }
        $data[] = $clasifications[$item->finance_catalogue_classification_sort]->name;
        $data[] = $item->finance_account_name;

        if(isset($item->model)){
            $data[] = $item->model;
            $data[] = $item->getModel()->name;
        }else{
            $data[] = '';
            $data[] = '';
        }
        $dataReturn = [$data];
        if(isset($item->subItems) && count($item->subItems) > 0){
            foreach ($item->subItems as $subItem){
                $dataReturn = array_merge($dataReturn,$this->reloadDataExport($subItem, $oldersClasificationIds, $clasifications));
            }
        }
        return $dataReturn;

    }

    public function getModels(Request $request)
    {
        $data = $request->all();

        $model = isset($data['model']) ? $data['model'] : null;
        $model_id = isset($data['model_id']) ? $data['model_id'] : null;

        $responces = DB::connection('main')->table($model)->select('id','name as text')->get();

        foreach ($responces as &$key) {
            if($key->id == $model_id){
                $key->selected = true;
            }
        }

        return response()->json($responces, 200);
    }

    public function setClassifications(Request $request)
    {
        $data = $request->all();
        dd($data);

        $id = $data['id'];
        $name = $data['name'];
        $sort = $data['sort'];
        $items_qty = $data['items_qty'];
        $color = $data['color'];

        // return response()->json($responces, 200);
    }
}