<?php

namespace App\Http\Controllers;

use App\Models\FinanceCatalogueClassification;
use App\Models\FinanceCatalogueItem;
use Illuminate\Http\Request;

class SetupMenuController extends Controller
{
    public function index(Request $request)
    {
        $items = FinanceCatalogueItem::orderBy('sort', 'ASC')->whereNull('supra_menu_id')->orWhere('supra_menu_id',0)->with('subItems')->get();
        return view('setup_menu.list', ['items' => $items]);
    }
}
