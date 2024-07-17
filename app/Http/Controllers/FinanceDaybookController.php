<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessDaybookImport;
use App\Jobs\ReportExportInvoices;
use App\Models\Main\Account;
use App\Models\Main\Invoice;
use App\Services\DaybookService;
use App\Services\ReportProcessServices;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Log;
use Redirect;
use Auth;
use Session;
use Carbon;
use DB;

use App\Models\FinanceCatalogueClassification;
use App\Models\FinanceCatalogueItem;
use App\Models\FinanceDaybookEntry;
use App\Models\FinanceDaybookEntryItem;

class FinanceDaybookController extends Controller
{
    Protected $models = [
        'accounts' => 'Tienda',
        'organization_company' => 'Empresas',
    ];

    protected $types = [
        'invoices' => 'Facturas'
    ];

    protected ReportProcessServices $reportProcessServices;
    protected DaybookService $daybookService;

    public function __construct(ReportProcessServices $reportProcessServices, DaybookService $daybookService)
    {
        $this->reportProcessServices = $reportProcessServices;
        $this->daybookService = $daybookService;
    }

    public function index(Request $request,$type = null,$id = null)
    {
        return view('finance_daybook.list', [
            'type' => $type,
            'id' => $id,
        ]);
    }

    public function showClassifications()
    {
        $items = FinanceCatalogueClassification::orderBy('sort', 'ASC')->get()->keyBy('id');
        return view('finance_catalogue.list_class', ['items' => $items, 'models' => $this->models]);
    }

    private function export($dates,$displayData, $reportType)
	{
		$items = FinanceCatalogueItem::orderBy('sort', 'ASC')->whereNull('sub_item_id')->with('subItems')->get();
        $columns = FinanceCatalogueClassification::orderBy('sort', 'ASC')->pluck('name')->toArray();

		$date = date('Y-m-d');
		$fileName = $reportType.'_'.$date.".csv";
		/* ------------------------ */

		$fp = fopen($fileName, 'w');
		$columns = array_merge($columns, [
            'Clasificación',
            'Nombre de la Cuenta',
        ]);

        dd($columns);

        fputcsv($fp, $columns, ';');
        foreach($displayData as &$account){
            $fields = [];
            foreach ($account as $key => $value) {
                if ($key == 'name') {
                    $fields[] = $value;
                } else {
                    if (!is_null($value)) {
                        $fields[] = 'Si';
                    } else {
                        $fields[] = 'No';
                    }

                }
            };
			fputcsv($fp, $fields, ';');
		}
		fclose($fp);
		return redirect('/'.$fileName);
	}

    public function getModels(Request $request)
    {
        $data = $request->all();

        $model = isset($data['model']) ? $data['model'] : null;
        $model_id = isset($data['model_id']) ? $data['model_id'] : null;

        $responces = DB::table($model)->select('id','name as text')->get();

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

    public function process(Request $request){
        $type = 'payments';
        $account = Account::pluck('id')->toArray();
        $date = '2017-08-10';
        $this->daybookService->initProcess($type, $account, $date);
        dd('listo');
        $name = 'create_daybook';
        /*$data = $request->all();
        if(count($data) > 0){
            $currentAccountId = $data['store'];
            $accounts = Account::where('accounts.exclude', 0)->pluck('id')->toArray();
            $currentStores = ($currentAccountId == 'all') ? array_keys($accounts) : [(int)$currentAccountId];
            $type = $data['type'];
            $date = $data['date'];

            $rows = count($currentStores);
            $chunkLimit = 4;
            $data = [
                'name' => $name,
                'rows' => $rows,
                'chunkLimit' => $chunkLimit
            ];

            $reportProcess = $this->reportProcessServices->processReportCsv($data);
            $reportProcessId = $reportProcess->id;

            if($rows == 1){
                dispatch((new ProcessDaybookImport($this->reportProcessServices->getRepository(), $reportProcessId, $currentStores, $date, $type))->delay(60));
            }else{
                $count = 1;
                foreach (array_chunk($currentStores, $chunkLimit) as $chunkStores){
                    dispatch((new ProcessDaybookImport($this->reportProcessServices->getRepository(), $reportProcessId, $chunkStores, $date, $type))->delay(60 * $count));
                    $count = $count+1;
                };
            };
        }*/
        $bodySelectAccount = [
            'model' => "App\\Models\\Main\\Account",
            'filters'=> ['name'],
            'columnText'=> ['name'],
            'name' => 'store',
            'all' => true
        ];
        $bodySelectType = [
            'array' => $this->types,
            'name' => 'type',
            'all' => true
        ];
        return view('finance_daybook.process', [
            'name' => $name,
            'bodySelectAccount' => $bodySelectAccount,
            'bodySelectType' => $bodySelectType
        ]);
    }
}
