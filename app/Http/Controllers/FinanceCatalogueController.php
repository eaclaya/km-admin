<?php

namespace App\Http\Controllers;

use App\Models\Main\OrganizationCompany;
use App\Services\FilesServices;
use App\Services\FinanceCatalogueService;
use Illuminate\Http\Request;

use Redirect;
use Auth;
use Session;
use Carbon;
use DB;

use App\Models\FinanceCatalogueClassification;
use App\Models\FinanceCatalogueItem;



class FinanceCatalogueController extends Controller
{
    Protected $models = [
        'accounts' => 'Tienda',
        'organization_company' => 'Empresas',
        'expense_category' => 'Categorias de Gastos',
        'expense_subcategory' => 'Sub Categorias de Gastos',
    ];
    protected FilesServices $filesServices;
    protected FinanceCatalogueService $financeCatalogueService;

    public function __construct(FilesServices $filesServices, FinanceCatalogueService $financeCatalogueService)
    {
        $this->filesServices = $filesServices;
        $this->financeCatalogueService = $financeCatalogueService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request): \Illuminate\View\View
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

    public function showClassifications(): \Illuminate\Contracts\View\View
    {
        $items = FinanceCatalogueClassification::orderBy('sort', 'ASC')->get()->keyBy('id');
        return view('finance_catalogue.list_class', ['items' => $items, 'models' => $this->models]);
    }

    public function export(Request $request)
	{
        $filter = $request->input('filter');

		$items = FinanceCatalogueItem::orderBy('sort', 'ASC')->whereNull('sub_item_id')->with('subItems');
        if(isset($filter)){
            if ($filter !== 'all'){
                $items = $items->where('id', $filter);
            }
        }else{
            $items = $items->where('id', 1);
        }
        $items = $items->get();
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
        ]);
//        dd([$columns, $data]);

        fwrite($fp, $bom);
		fputcsv($fp, $columns, ';');
        foreach($data as $fields){
			fputcsv($fp, $fields, ';');
		}
		fclose($fp);
		return redirect('/'.$fileName);
	}

    public function reloadDataExport($item, $oldersClasificationIds, $clasifications): array
    {
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

    public function import(Request $request){
        $request->validate([
            'csv_file' => 'required|mimes:csv,txt',
        ]);
        $file = $request->file('csv_file');
        [,,$data] = $this->filesServices->readFileCsv($file);
        dd($data);
    }

    public function setGenerate(Request $request){
        $data = $request->all();
        $item = FinanceCatalogueItem::find($data['item_id']);
        if(isset($item)){
            $item->is_generated = $data['generate'];
            $item->save();
        }
        return response()->json(['result' =>'ok'], 200);
    }

    public function generateItems(Request $request){
        $data = $request->all();
        $item = FinanceCatalogueItem::find($data['item_id']);
        if(isset($item)){
            $item->is_generated = $data['is_generated'];
            $item->save();
            $this->financeCatalogueService->initGenerate($item);
        }
        return response()->json(['result' =>'ok'], 200);
    }
}
