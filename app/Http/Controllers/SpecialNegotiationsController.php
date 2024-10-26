<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Main\SpecialNegotiation;
use App\Models\Main\DiscountQuota;
use App\Models\Main\PaymentQuota;
use App\Models\Main\Quota;

class SpecialNegotiationsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('special_negotiations.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('special_negotiations.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->all();
        $invoices_ids = $data['invoice_id'];
        unset($data['invoice_id']);
        unset($data['_token']);
        $negotiation = SpecialNegotiation::create($data);
        $negotiation->invoices()->attach($invoices_ids);
        return redirect()->route('special_negotiations.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $negotiation = SpecialNegotiation::find($id);
        if (!isset($negotiation)) {
            return redirect()->route('special_negotiations.index');
        }
        return view('special_negotiations.show', ['special_negotiation' => $negotiation]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $data = [];
        $negotiation = SpecialNegotiation::find($id);
        if (!isset($negotiation)) {
            return redirect()->route('special_negotiations.index');
        }
        $data['special_negotiation'] = $negotiation;
        $data['route_select'] = [
            'model' => "App\\Models\\Main\\Route",
            'filters'=> ['name'],
            'columnText'=> ['name'],
            'name' => 'route_id',
            'optionSelected' => [
                'id' => $negotiation->route_id
            ],
        ];
        $data['account_select'] = [
            'model' => "App\\Models\\Main\\Account",
            'filters'=> ['name'],
            'columnText'=> ['name'],
            'optionSelected' => [
                'id' => $negotiation->account_id
            ],
            'name' => 'account_id',
            'set_properties' => [
                [
                    'name' => 'employee_id',
                    'filters' => [
                        'account_id' => '$selected',
                    ],
                ],
                [
                    'name' => 'client_id',
                    'filters' => [
                        'account_id' => '$selected',
                    ],
                ],
                [
                    'name' => 'invoice_id',
                    'filters' => [
                        'account_id' => '$selected',
                    ],
                ],
            ],
        ];
        $data['employee_select'] = [
            'model' => "App\\Models\\Main\\Employee",
            'filters'=> ['first_name','last_name'],
            'columnText'=> ['first_name','last_name'],
            'optionSelected' => [
                'id' => $negotiation->employee_id
            ],
            'name' => 'employee_id',
        ];
        $data['client_select'] = [
            'model' => "App\\Models\\Main\\Client",
            'filters'=> ['name'],
            'columnText'=> ['name'],
            'name' => 'client_id',
            'optionSelected' => [
                'id' => $negotiation->client_id
            ],
        ];
        $data['invoice_select'] = [
            'model' => "App\\Models\\Main\\Invoice",
            'filters'=> ['invoice_number', 'created_at', 'amount'],
            'columnText'=> ['invoice_number', 'created_at', 'amount'],
            'name' => 'invoice_id',
            'is_multiple' => true,
            'optionSelected' => [
                'id' => $negotiation->invoices->pluck('id')->toArray(),
            ],
        ];
        $data['status_select'] = [
            'array' => [
                "0" => 'Activo',
                "1" => 'Vencido',
            ],
            'optionSelected' => [
                'id' => $negotiation->status,
            ],
            'name' => 'status',
        ];
        $data['is_document_select'] = [
            'array' => [
                "0" => 'No',
                "1" => 'Si',
            ],
            'optionSelected' => [
                'id' => $negotiation->is_document,
            ],
            'name' => 'is_document',
        ];
        return view('special_negotiations.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $data = $request->all();
        $invoices_ids = $data['invoice_id'];

        unset($data['invoice_id']);
        unset($data['_token']);
        $negotiation = SpecialNegotiation::find($id);
        $negotiation->update($data);
        $negotiation->invoices()->sync($invoices_ids);
        return redirect()->route('special_negotiations.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
