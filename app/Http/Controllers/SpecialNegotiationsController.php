<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SpecialNegotiationsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        /*

        tengo que crear estos modelos.!
        Schema::create('special_negotiations', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->unsignedInteger('account_id')->unsigned();
            $table->integer('employee_id');
            $table->unsignedInteger('client_id')->unsigned();
            $table->unsignedInteger('invoice_id')->unsigned();
            $table->decimal('amount', 12,2)->nullable();
            $table->decimal('overdue_balance', 12,2)->nullable();
            $table->decimal('due_balance', 12,2)->nullable();
            $table->integer('status')->default(0);
            $table->integer('is_document')->default(0);
            $table->decimal('negotiations_discount', 12,2)->default(0);
            $table->timestamps();
            $table->softDeletes()->nullable();

            $table->foreign('account_id')->references('id')->on('accounts');
            $table->foreign('employee_id')->references('id')->on('employees');
            $table->foreign('client_id')->references('id')->on('clients');
            $table->foreign('invoice_id')->references('id')->on('invoices');
        });

        Schema::create('quotas', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->unsignedInteger('special_negotiations_id')->unsigned();
            $table->unsignedInteger('account_id')->unsigned();
            $table->integer('employee_id');
            $table->unsignedInteger('client_id')->unsigned();
            $table->unsignedInteger('invoice_id')->unsigned();
            $table->integer('days_exceeded')->default(0);
            $table->decimal('initial_balance', 12,2)->nullable();
            $table->decimal('monthly_payment', 12,2)->nullable();
            $table->integer('status')->default(0);
            $table->date('credit_start_at')->nullable();
            $table->date('credit_payment_at')->nullable();
            $table->decimal('discount_applied', 12,2)->nullable();
            $table->timestamps();
            $table->softDeletes()->nullable();

            $table->foreign('special_negotiations_id')->references('id')->on('special_negotiations');
            $table->foreign('account_id')->references('id')->on('accounts');
            $table->foreign('employee_id')->references('id')->on('employees');
            $table->foreign('client_id')->references('id')->on('clients');
            $table->foreign('invoice_id')->references('id')->on('invoices');
        });

        Schema::create('payment_quotas', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->unsignedInteger('special_negotiations_id')->unsigned();
            $table->unsignedInteger('quota_id')->unsigned();
            $table->unsignedInteger('account_id')->unsigned();
            $table->integer('employee_id');
            $table->unsignedInteger('client_id')->unsigned();
            $table->unsignedInteger('invoice_id')->unsigned();
            $table->unsignedInteger('payment_id')->unsigned();

            $table->decimal('mount_balance', 12,2)->nullable();
            $table->decimal('mount_balance_total', 12,2)->nullable();
            $table->decimal('overdue_balance', 12,2)->nullable();
            $table->decimal('final_balance', 12,2)->nullable();

            $table->date('payment_at')->nullable();

            $table->timestamps();
            $table->softDeletes()->nullable();

            $table->foreign('special_negotiations_id')->references('id')->on('special_negotiations');
            $table->foreign('quota_id')->references('id')->on('quotas');
            $table->foreign('account_id')->references('id')->on('accounts');
            $table->foreign('employee_id')->references('id')->on('employees');
            $table->foreign('client_id')->references('id')->on('clients');
            $table->foreign('invoice_id')->references('id')->on('invoices');
            $table->foreign('payment_id')->references('id')->on('payments');
        });

        Schema::create('discount_quotas', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->unsignedInteger('special_negotiations_id')->unsigned();
            $table->unsignedInteger('quota_id')->unsigned();
            $table->unsignedInteger('account_id')->unsigned();
            $table->integer('employee_id');
            $table->unsignedInteger('client_id')->unsigned();
            $table->unsignedInteger('invoice_id')->unsigned();
            $table->unsignedInteger('payment_quota_id')->unsigned();

            $table->decimal('discount_applied', 12,2)->nullable();
            $table->decimal('porcent_quotas_discount', 12,2)->nullable();

            $table->timestamps();
            $table->softDeletes()->nullable();

            $table->foreign('special_negotiations_id')->references('id')->on('special_negotiations');
            $table->foreign('quota_id')->references('id')->on('quotas');
            $table->foreign('account_id')->references('id')->on('accounts');
            $table->foreign('employee_id')->references('id')->on('employees');
            $table->foreign('client_id')->references('id')->on('clients');
            $table->foreign('invoice_id')->references('id')->on('invoices');
            $table->foreign('payment_quota_id')->references('id')->on('payment_quotas');
        });

        */
        return view('special_negotiations.list');
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
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
