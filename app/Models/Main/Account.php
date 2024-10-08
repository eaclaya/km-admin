<?php

namespace App\Models\Main;

use Illuminate\Database\Eloquent\Model;

class Account extends ModelDBMain
{
    protected $table = 'accounts';

    protected $fillable = [
        'id',
        'name',
        'id_number',
        'vat_number',
        'work_email',
        'website',
        'work_phone',
        'address1',
        'address2',
        'city',
        'state',
        'postal_code',
        'country_id',
        'size_id',
        'industry_id',
        'email_footer',
        'timezone_id',
        'date_format_id',
        'datetime_format_id',
        'currency_id',
        'language_id',
        'military_time',
        'invoice_taxes',
        'invoice_item_taxes',
        'show_item_taxes',
        'default_tax_rate_id',
        'enable_second_tax_rate',
        'include_item_taxes_inline',
        'start_of_week',
        'financial_year_start',
        'enable_client_portal',
        'enable_client_portal_dashboard',
        'enable_portal_password',
        'send_portal_password',
        'enable_buy_now_buttons',
        'show_accept_invoice_terms',
        'show_accept_quote_terms',
        'require_invoice_signature',
        'require_quote_signature',
        'fast_pos',
        'change_product_price',
        'show_billing_in_invoice',
        'Matrix_address',
        'Matrix_name',
        'company_zones_id',
        'organization_company_id'
    ];
}
