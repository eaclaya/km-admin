<?php

namespace App\Http\Controllers;

use App\Models\InvoicesDiscount;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InvoicesDiscountController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
//            'invoicesDiscount' => InvoicesDiscount::all(),
        return view('invoices_discount.index', []);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(InvoicesDiscountController $invoicesDiscount)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(InvoicesDiscountController $invoicesDiscount)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, InvoicesDiscountController $invoicesDiscount)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(InvoicesDiscountController $invoicesDiscount)
    {
        //
    }
}
