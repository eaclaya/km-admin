<?php

use App\Http\Controllers\AdvancereportsController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth','checkPermission'])->group(function () {
    Route::post('advancereports/sales-by-client-type', [AdvancereportsController::class, 'salesByClientType'])->name('advancereports.sales_by_client_type');
    Route::get('advancereports/sales-by-client-type', [AdvancereportsController::class, 'salesByClientType'])->name('advancereports.sales_by_client_type');

    Route::post('advancereports/invoices-converted', [AdvancereportsController::class, 'invoicesConverted'])->name('advancereports.invoices_converted');
    Route::get('advancereports/invoices-converted', [AdvancereportsController::class, 'invoicesConverted'])->name('advancereports.invoices_converted');

    Route::post('advancereports/refunds', [AdvancereportsController::class, 'refunds'])->name('advancereports.refunds');
    Route::get('advancereports/refunds', [AdvancereportsController::class, 'refunds'])->name('advancereports.refunds');

    Route::post('advancereports/available-cash', [AdvancereportsController::class, 'availableCash'])->name('advancereports.available_cash');
    Route::get('advancereports/available-cash', [AdvancereportsController::class, 'availableCash'])->name('advancereports.available_cash');

    Route::post('advancereports/compare-client-sales', [AdvancereportsController::class, 'compareClientSales'])->name('advancereports.compare_client_sales');
    Route::get('advancereports/compare-client-sales', [AdvancereportsController::class, 'compareClientSales'])->name('advancereports.compare_client_sales');

    Route::post('advancereports/monthly-client-sales', [AdvancereportsController::class, 'monthlyClientSales'])->name('advancereports.monthly_client_sales');
    Route::get('advancereports/monthly-client-sales', [AdvancereportsController::class, 'monthlyClientSales'])->name('advancereports.monthly_client_sales');

    Route::post('advancereports/monthly-salaries', [AdvancereportsController::class, 'monthlySalaries'])->name('advancereports.monthly_salaries');
    Route::get('advancereports/monthly-salaries', [AdvancereportsController::class, 'monthlySalaries'])->name('advancereports.monthly_salaries');

    Route::post('advancereports/related-product-sales', [AdvancereportsController::class, 'relatedProductSales'])->name('advancereports.related_product_sales');
    Route::get('advancereports/related-product-sales', [AdvancereportsController::class, 'relatedProductSales'])->name('advancereports.related_product_sales');

    Route::post('advancereports/packing-to-invoice', [AdvancereportsController::class, 'packingToInvoice'])->name('advancereports.packing_to_invoice');
    Route::get('advancereports/packing-to-invoice', [AdvancereportsController::class, 'packingToInvoice'])->name('advancereports.packing_to_invoice');

    Route::post('advancereports/packing-to-transfer', [AdvancereportsController::class, 'packingToTransfer'])->name('advancereports.packing_to_transfer');
    Route::get('advancereports/packing-to-transfer', [AdvancereportsController::class, 'packingToTransfer'])->name('advancereports.packing_to_transfer');

    Route::post('advancereports/export/clients', [AdvancereportsController::class, 'exportClients'])->name('advancereports.export_clients');
    Route::get('advancereports/export/clients', [AdvancereportsController::class, 'exportClients'])->name('advancereports.export_clients');

    Route::post('advancereports/export/invoices', [AdvancereportsController::class, 'exportInvoices'])->name('advancereports.export_invoices');
    Route::get('advancereports/export/invoices', [AdvancereportsController::class, 'exportInvoices'])->name('advancereports.export_invoices');

    Route::get('advancereports/export/inventory-not-location', [AdvancereportsController::class, 'exportInventoryNotLocations'])->name('advancereports.export_inventory_not_location');

    Route::post('advancereports/billing', [AdvancereportsController::class, 'billing'])->name('advancereports.billing');
    Route::get('advancereports/billing', [AdvancereportsController::class, 'billing'])->name('advancereports.billing');

    Route::post('advancereports/account_settings', [AdvancereportsController::class, 'accountSettings'])->name('advancereports.account_settings');
    Route::get('advancereports/account_settings', [AdvancereportsController::class, 'accountSettings'])->name('advancereports.account_settings');

    Route::post('advancereports/promises_pay', [AdvancereportsController::class, 'promisesPay'])->name('advancereports.promises_pay');
    Route::get('advancereports/promises_pay', [AdvancereportsController::class, 'promisesPay'])->name('advancereports.promises_pay');

    Route::post('advancereports/category_clients', [AdvancereportsController::class, 'categoryClients'])->name('advancereports.category_clients');
    Route::get('advancereports/category_clients', [AdvancereportsController::class, 'categoryClients'])->name('advancereports.category_clients');

    Route::post('advancereports/documentation_clients', [AdvancereportsController::class, 'documentationClients'])->name('advancereports.documentation_clients');
    Route::get('advancereports/documentation_clients', [AdvancereportsController::class, 'documentationClients'])->name('advancereports.documentation_clients');

    Route::post('advancereports/refund_settings', [AdvancereportsController::class, 'refund_settings'])->name('advancereports.refund_settings');
    Route::get('advancereports/refund_settings', [AdvancereportsController::class, 'refund_settings'])->name('advancereports.refund_settings');

    Route::post('advancereports/tasks-by-employee', [AdvancereportsController::class, 'tasksByEmployee'])->name('advancereports.tasks_by_employee');
    Route::get('advancereports/tasks-by-employee', [AdvancereportsController::class, 'tasksByEmployee'])->name('advancereports.tasks_by_employee');

    Route::post('advancereports/devices', [AdvancereportsController::class, 'devices'])->name('advancereports.devices');
    Route::get('advancereports/devices', [AdvancereportsController::class, 'devices'])->name('advancereports.devices');

    Route::get('advancereports/export-inventory-general', [AdvancereportsController::class, 'exportInventoryGeneral'])->name('advancereports.export_inventory_general');
    Route::get('advancereports/export-inventory', [AdvancereportsController::class, 'exportInventory'])->name('advancereports.export_inventory');

    Route::post('advancereports/sales-by-sac', [AdvancereportsController::class, 'salesBySac'])->name('advancereports.sales_by_sac');
    Route::get('advancereports/sales-by-sac', [AdvancereportsController::class, 'salesBySac'])->name('advancereports.sales_by_sac');

    Route::post('advancereports/routes-visits', [AdvancereportsController::class, 'routesVisits'])->name('advancereports.routes_visits');
    Route::get('advancereports/routes-visits', [AdvancereportsController::class, 'routesVisits'])->name('advancereports.routes_visits');

    Route::post('advancereports/routes-visits-day', [AdvancereportsController::class, 'routesVisitsDay'])->name('advancereports.routes_visits_day');
    Route::get('advancereports/routes-visits-day', [AdvancereportsController::class, 'routesVisitsDay'])->name('advancereports.routes_visits_day');

    Route::post('advancereports/clients-passed', [AdvancereportsController::class, 'clientsPassed'])->name('advancereports.clients_passed');
    Route::get('advancereports/clients-passed', [AdvancereportsController::class, 'clientsPassed'])->name('advancereports.clients_passed');

    Route::post('advancereports/clients-unvisited', [AdvancereportsController::class, 'clientsUnvisited'])->name('advancereports.clients_unvisited');
    Route::get('advancereports/clients-unvisited', [AdvancereportsController::class, 'clientsUnvisited'])->name('advancereports.clients_unvisited');


    Route::post('advancereports/orders', [AdvancereportsController::class, 'orders'])->name('advancereports.orders');
    Route::get('advancereports/orders', [AdvancereportsController::class, 'orders'])->name('advancereports.orders');

    Route::post('advancereports/receivables-by-route', [AdvancereportsController::class, 'receivablesByRoute'])->name('advancereports.receivables_by_route');
    Route::get('advancereports/receivables-by-route', [AdvancereportsController::class, 'receivablesByRoute'])->name('advancereports.receivables_by_route');

    Route::post('advancereports/carts', [AdvancereportsController::class, 'carts'])->name('advancereports.carts');
    Route::get('advancereports/carts', [AdvancereportsController::class, 'carts'])->name('advancereports.carts');

    Route::post('advancereports/supplies', [AdvancereportsController::class, 'supplies'])->name('advancereports.supplies');
    Route::get('advancereports/supplies', [AdvancereportsController::class, 'supplies'])->name('advancereports.supplies');

    Route::post('advancereports/order-items', [AdvancereportsController::class, 'orderItems'])->name('advancereports.order_items');
    Route::get('advancereports/order-items', [AdvancereportsController::class, 'orderItems'])->name('advancereports.order_items');

    Route::post('advancereports/imports/pending', [AdvancereportsController::class, 'importsPending'])->name('advancereports.imports_pending');
    Route::get('advancereports/imports/pending', [AdvancereportsController::class, 'importsPending'])->name('advancereports.imports_pending');

    Route::post('advancereports/invoices/draft', [AdvancereportsController::class, 'invoicesDraft'])->name('advancereports.invoices_draft');
    Route::get('advancereports/invoices/draft', [AdvancereportsController::class, 'invoicesDraft'])->name('advancereports.invoices_draft');

    Route::post('advancereports/transfers', [AdvancereportsController::class, 'transfers'])->name('advancereports.transfers');
    Route::get('advancereports/transfers', [AdvancereportsController::class, 'transfers'])->name('advancereports.transfers');

    Route::post('advancereports/transfer-items/pending', [AdvancereportsController::class, 'transferItemsPending'])->name('advancereports.transfer_items_pending');
    Route::get('advancereports/transfer-items/pending', [AdvancereportsController::class, 'transferItemsPending'])->name('advancereports.transfer_items_pending');

    Route::post('advancereports/transfer-items/accepted', [AdvancereportsController::class, 'transferItemsAccepted'])->name('advancereports.transfer_items_accepted');
    Route::get('advancereports/transfer-items/accepted', [AdvancereportsController::class, 'transferItemsAccepted'])->name('advancereports.transfer_items_accepted');

    Route::post('advancereports/transfer/items/remain', [AdvancereportsController::class, 'transferItemsRemain'])->name('advancereports.transferitems_remain');
    Route::get('advancereports/transfer/items/remain', [AdvancereportsController::class, 'transferItemsRemain'])->name('advancereports.transferitems_remain');

    Route::get('advancereports/customers/points', [AdvancereportsController::class, 'customersPoints'])->name('advancereports.customers_points');
    Route::post('advancereports/customers/points', [AdvancereportsController::class, 'customersPoints'])->name('advancereports.customers_points');

    Route::post('advancereports/invoice-points', [AdvancereportsController::class, 'invoicePoints'])->name('advancereports.invoice_points');
    Route::get('advancereports/invoice-points', [AdvancereportsController::class, 'invoicePoints'])->name('advancereports.invoice_points');

    Route::post('advancereports/customer-purchases-frequency', [AdvancereportsController::class, 'customerPurchasesFrequency'])->name('advancereports.customer_purchases_frequency');
    Route::get('advancereports/customer-purchases-frequency', [AdvancereportsController::class, 'customerPurchasesFrequency'])->name('advancereports.customer_purchases_frequency');

    Route::post('advancereports/clients/visit', [AdvancereportsController::class, 'clientVisits'])->name('advancereports.visits');
    Route::get('advancereports/clients/visit', [AdvancereportsController::class, 'clientVisits'])->name('advancereports.visits');

    Route::post('advancereports/clients-by-date', [AdvancereportsController::class, 'clientsByDate'])->name('advancereports.clients_by_date');
    Route::get('advancereports/clients-by-date', [AdvancereportsController::class, 'clientsByDate'])->name('advancereports.clients_by_date');

    Route::post('advancereports/customers-inactive', [AdvancereportsController::class, 'customersInactive'])->name('advancereports.customers_inactive');
    Route::get('advancereports/customers-inactive', [AdvancereportsController::class, 'customersInactive'])->name('advancereports.customers_inactive');

    Route::post('advancereports/products-underwholesaleprice', [AdvancereportsController::class, 'productsUnderWholesalePrice'])->name('advancereports.products_underwholesaleprice');
    Route::get('advancereports/products-underwholesaleprice', [AdvancereportsController::class, 'productsUnderWholesalePrice'])->name('advancereports.products_underwholesaleprice');

    Route::post('advancereports/products-undernormalprice', [AdvancereportsController::class, 'productsUnderNormalPrice'])->name('advancereports.products_undernormalprice');
    Route::get('advancereports/products-undernormalprice', [AdvancereportsController::class, 'productsUnderNormalPrice'])->name('advancereports.products_undernormalprice');

    Route::post('advancereports/stock-entries', [AdvancereportsController::class, 'stockEntries'])->name('advancereports.stock_entries');
    Route::get('advancereports/stock-entries', [AdvancereportsController::class, 'stockEntries'])->name('advancereports.stock_entries');

    Route::post('advancereports/sales-by-time-period', [AdvancereportsController::class, 'salesByTimePeriod'])->name('advancereports.sales_by_time_period');
    Route::get('advancereports/sales-by-time-period', [AdvancereportsController::class, 'salesByTimePeriod'])->name('advancereports.sales_by_time_period');

    Route::post('advancereports/sales-per-client', [AdvancereportsController::class, 'salesPerClient'])->name('advancereports.sales_per_client');
    Route::get('advancereports/sales-per-client', [AdvancereportsController::class, 'salesPerClient'])->name('advancereports.sales_per_client');

    Route::post('advancereports/client-notes', [AdvancereportsController::class, 'clientNotes'])->name('advancereports.client_notes');
    Route::get('advancereports/client-notes', [AdvancereportsController::class, 'clientNotes'])->name('advancereports.client_notes');

    Route::post('advancereports/most-selled-products', [AdvancereportsController::class, 'mostSelledProducts'])->name('advancereports.most_selled_products');
    Route::get('advancereports/most-selled-products', [AdvancereportsController::class, 'mostSelledProducts'])->name('advancereports.most_selled_products');

    Route::post('advancereports/less-selled-products', [AdvancereportsController::class, 'lessSelledProducts'])->name('advancereports.less_selled_products');
    Route::get('advancereports/less-selled-products', [AdvancereportsController::class, 'lessSelledProducts'])->name('advancereports.less_selled_products');

    Route::post('advancereports/unselled-products', [AdvancereportsController::class, 'unselledProducts'])->name('advancereports.unselled_products');
    Route::get('advancereports/unselled-products', [AdvancereportsController::class, 'unselledProducts'])->name('advancereports.unselled_products');

    Route::post('advancereports/most-requested-products', [AdvancereportsController::class, 'mostRequestedProducts'])->name('advancereports.most_requested_products');
    Route::get('advancereports/most-requested-products', [AdvancereportsController::class, 'mostRequestedProducts'])->name('advancereports.most_requested_products');

    Route::post('advancereports/sale-transfer-products', [AdvancereportsController::class, 'saleTransferProducts'])->name('advancereports.sale_transfer_products');
    Route::get('advancereports/sale-transfer-products', [AdvancereportsController::class, 'saleTransferProducts'])->name('advancereports.sale_transfer_products');

    Route::get('advancereports/tutorials', [AdvancereportsController::class, 'tutorials'])->name('advancereports.tutorials');

    Route::post('advancereports/input-entries', [AdvancereportsController::class, 'inputEntries'])->name('advancereports.input_entries');
    Route::get('advancereports/input-entries', [AdvancereportsController::class, 'inputEntries'])->name('advancereports.input_entries');

    Route::post('advancereports/stock/vendor', [AdvancereportsController::class, 'stockByVendor'])->name('advancereports.stock_by_vendor');
    Route::get('advancereports/stock/vendor', [AdvancereportsController::class, 'stockByVendor'])->name('advancereports.stock_by_vendor');

    Route::post('advancereports/sales/vendor', [AdvancereportsController::class, 'salesByVendor'])->name('advancereports.sales_by_vendor');
    Route::get('advancereports/sales/vendor', [AdvancereportsController::class, 'salesByVendor'])->name('advancereports.sales_by_vendor');

    Route::post('advancereports/products/vendor', [AdvancereportsController::class, 'productsByVendor'])->name('advancereports.products_by_vendor');
    Route::get('advancereports/products/vendor', [AdvancereportsController::class, 'productsByVendor'])->name('advancereports.products_by_vendor');

    Route::post('advancereports/sales/client', [AdvancereportsController::class, 'salesByClient'])->name('advancereports.sales_by_client');
    Route::get('advancereports/sales/client', [AdvancereportsController::class, 'salesByClient'])->name('advancereports.sales_by_client');

    Route::post('advancereports/quoted-products', [AdvancereportsController::class, 'quotedProducts'])->name('advancereports.quoted_products');
    Route::get('advancereports/quoted-products', [AdvancereportsController::class, 'quotedProducts'])->name('advancereports.quoted_products');

    Route::post('advancereports/proform-products', [AdvancereportsController::class, 'proformProducts'])->name('advancereports.proform_products');
    Route::get('advancereports/proform-products', [AdvancereportsController::class, 'proformProducts'])->name('advancereports.proform_products');

    Route::post('advancereports/no-updated-products', [AdvancereportsController::class, 'noUpdatedProducts'])->name('advancereports.no_updated_products');
    Route::get('advancereports/no-updated-products', [AdvancereportsController::class, 'noUpdatedProducts'])->name('advancereports.no_updated_products');

    Route::post('advancereports/sales/client-type', [AdvancereportsController::class, 'salesByClientType'])->name('advancereports.sales_by_client_type');
    Route::get('advancereports/sales/client-type', [AdvancereportsController::class, 'salesByClientType'])->name('advancereports.sales_by_client_type');

    Route::get('advancereports/sales/mechanic', [AdvancereportsController::class, 'salesByMechanic'])->name('advancereports.sales_by_mechanic');
    Route::post('advancereports/sales/mechanic', [AdvancereportsController::class, 'salesByMechanic'])->name('advancereports.sales_by_mechanic');

    Route::post('advancereports/sales/seller', [AdvancereportsController::class, 'salesBySeller'])->name('advancereports.sales_by_seller');
    Route::get('advancereports/sales/seller', [AdvancereportsController::class, 'salesBySeller'])->name('advancereports.sales_by_seller');

    Route::post('advancereports/payments/seller', [AdvancereportsController::class, 'paymentsBySeller'])->name('advancereports.payments_by_seller');
    Route::get('advancereports/payments/seller', [AdvancereportsController::class, 'paymentsBySeller'])->name('advancereports.payments_by_seller');

    Route::get('advancereports/net-sales/seller', [AdvancereportsController::class, 'netSalesBySeller'])->name('advancereports.net_sales_by_seller');
    Route::post('advancereports/net-sales/seller', [AdvancereportsController::class, 'netSalesBySeller'])->name('advancereports.net_sales_by_seller');

    Route::post('advancereports/sales/utility', [AdvancereportsController::class, 'salesUtilityByDate'])->name('advancereports.sales_utility_by_date');
    Route::get('advancereports/sales/utility', [AdvancereportsController::class, 'salesUtilityByDate'])->name('advancereports.sales_utility_by_date');

    Route::post('advancereports/sales/date', [AdvancereportsController::class, 'salesByDate'])->name('advancereports.sales_by_date');
    Route::get('advancereports/sales/date', [AdvancereportsController::class, 'salesByDate'])->name('advancereports.sales_by_date');

    Route::post('advancereports/sales/month', [AdvancereportsController::class, 'salesByMonth'])->name('advancereports.sales_by_month');
    Route::get('advancereports/sales/month', [AdvancereportsController::class, 'salesByMonth'])->name('advancereports.sales_by_month');

    Route::post('advancereports/old-price-product-date', [AdvancereportsController::class, 'oldPriceProductDate'])->name('advancereports.old_price_product_date');
    Route::get('advancereports/old-price-product-date', [AdvancereportsController::class, 'oldPriceProductDate'])->name('advancereports.old_price_product_date');

    Route::post('advancereports/traces-request', [AdvancereportsController::class, 'tracesRequest'])->name('advancereports.traces_request');
    Route::get('advancereports/traces-request', [AdvancereportsController::class, 'tracesRequest'])->name('advancereports.traces_request');

    Route::post('advancereports/cash-count-net-sales', [AdvancereportsController::class, 'cashCountNetSales'])->name('advancereports.cash_count_net_sales');
    Route::get('advancereports/cash-count-net-sales', [AdvancereportsController::class, 'cashCountNetSales'])->name('advancereports.cash_count_net_sales');

    Route::get('advancereports/commission_old_products', [AdvancereportsController::class, 'commission_old_products'])->name('advancereports.commission_old_products');
    Route::post('advancereports/commission_old_products', [AdvancereportsController::class, 'commission_old_products'])->name('advancereports.commission_old_products');

    Route::get('advancereports/stock_in_stores', [AdvancereportsController::class, 'stock_in_stores'])->name('advancereports.stock_in_stores');
    Route::post('advancereports/stock_in_stores', [AdvancereportsController::class, 'stock_in_stores'])->name('advancereports.stock_in_stores');

    Route::post('advancereports/transfer-items-accepted-by-time-period', [AdvancereportsController::class, 'transferItemsAcceptedByTimePeriod'])->name('advancereports.transfer_items_accepted_by_time_period');
    Route::get('advancereports/transfer-items-accepted-by-time-period', [AdvancereportsController::class, 'transferItemsAcceptedByTimePeriod'])->name('advancereports.transfer_items_accepted_by_time_period');

    Route::get('advancereports/salesrange', [AdvancereportsController::class, 'salesByDateSum'])->name('advancereports.sales_by_date_sum');
    Route::post('advancereports/salesrange', [AdvancereportsController::class, 'salesByDateSum'])->name('advancereports.sales_by_date_sum');

    Route::post('advancereports/saleitems/vendor', [AdvancereportsController::class, 'saleItemsByVendor'])->name('advancereports.saleitems_by_vendor');
    Route::get('advancereports/saleitems/vendor', [AdvancereportsController::class, 'saleItemsByVendor'])->name('advancereports.saleitems_by_vendor');

    Route::post('advancereports/sales/category', [AdvancereportsController::class, 'salesByCategory'])->name('advancereports.sales_by_category');
    Route::get('advancereports/sales/category', [AdvancereportsController::class, 'salesByCategory'])->name('advancereports.sales_by_category');

    Route::post('advancereports/sales/product', [AdvancereportsController::class, 'salesByProduct'])->name('advancereports.sales_by_product');
    Route::get('advancereports/sales/product', [AdvancereportsController::class, 'salesByProduct'])->name('advancereports.sales_by_product');

    Route::post('advancereports/stock/product', [AdvancereportsController::class, 'stockByProduct'])->name('advancereports.stock_by_product');
    Route::get('advancereports/stock/product', [AdvancereportsController::class, 'stockByProduct'])->name('advancereports.stock_by_product');

    Route::get('advancereports/finish-report/{id}', [AdvancereportsController::class, 'finishReport'])->name('advancereports.finish_report');

    Route::post('advancereports/expenses_by_category', [AdvancereportsController::class, 'expensesByCategories'])->name('advancereports.expenses_by_category');
    Route::get('advancereports/expenses_by_category', [AdvancereportsController::class, 'expensesByCategories'])->name('advancereports.expenses_by_category');

    Route::post('advancereports/sales_subcategory_by_month', [AdvancereportsController::class, 'salesSubcategoryByMonth'])->name('advancereports.sales_subcategory_by_month');
    Route::get('advancereports/sales_subcategory_by_month', [AdvancereportsController::class, 'salesSubcategoryByMonth'])->name('advancereports.sales_subcategory_by_month');

    Route::post('advancereports/vouchers_discount', [AdvancereportsController::class, 'vouchersDiscounts'])->name('advancereports.vouchers_discount');
    Route::get('advancereports/vouchers_discount', [AdvancereportsController::class, 'vouchersDiscounts'])->name('advancereports.vouchers_discount');

    Route::post('advancereports/expenses_cash_count', [AdvancereportsController::class, 'expensesCashCount'])->name('advancereports.expenses_cash_count');
    Route::get('advancereports/expenses_cash_count', [AdvancereportsController::class, 'expensesCashCount'])->name('advancereports.expenses_cash_count');

    Route::post('advancereports/expenses_all_cash_count', [AdvancereportsController::class, 'expensesAllCashCount'])->name('advancereports.expenses_all_cash_count');
    Route::get('advancereports/expenses_all_cash_count', [AdvancereportsController::class, 'expensesAllCashCount'])->name('advancereports.expenses_all_cash_count');

    Route::post('advancereports/transfers_cash_count', [AdvancereportsController::class, 'transfersCashCount'])->name('advancereports.transfers_cash_count');
    Route::get('advancereports/transfers_cash_count', [AdvancereportsController::class, 'transfersCashCount'])->name('advancereports.transfers_cash_count');

    Route::post('advancereports/bank_transfers_cash_count', [AdvancereportsController::class, 'bankTransfersCashCount'])->name('advancereports.bank_transfers_cash_count');
    Route::get('advancereports/bank_transfers_cash_count', [AdvancereportsController::class, 'bankTransfersCashCount'])->name('advancereports.bank_transfers_cash_count');

    Route::post('advancereports/sales_cash_count', [AdvancereportsController::class, 'salesCashCount'])->name('advancereports.sales_cash_count');
    Route::get('advancereports/sales_cash_count', [AdvancereportsController::class, 'salesCashCount'])->name('advancereports.sales_cash_count');

    Route::post('advancereports/incomes_cash_count', [AdvancereportsController::class, 'incomesCashCount'])->name('advancereports.incomes_cash_count');
    Route::get('advancereports/incomes_cash_count', [AdvancereportsController::class, 'incomesCashCount'])->name('advancereports.incomes_cash_count');

    Route::post('advancereports/routes', [AdvancereportsController::class, 'routes'])->name('advancereports.routes');
    Route::get('advancereports/routes', [AdvancereportsController::class, 'routes'])->name('advancereports.routes');

    Route::post('advancereports/per_diem', [AdvancereportsController::class, 'per_diem'])->name('advancereports.per_diem');
    Route::get('advancereports/per_diem', [AdvancereportsController::class, 'per_diem'])->name('advancereports.per_diem');

    Route::post('advancereports/invoices_deleted', [AdvancereportsController::class, 'invoices_deleted'])->name('advancereports.invoices_deleted');
    Route::get('advancereports/invoices_deleted', [AdvancereportsController::class, 'invoices_deleted'])->name('advancereports.invoices_deleted');

    Route::post('advancereports/subcategory_group_by_category', [AdvancereportsController::class, 'SubcategoryGroupByCategory'])->name('advancereports.subcategory_group_by_category');
    Route::get('advancereports/subcategory_group_by_category', [AdvancereportsController::class, 'SubcategoryGroupByCategory'])->name('advancereports.subcategory_group_by_category');

    Route::post('advancereports/products_with_images', [AdvancereportsController::class, 'productsWithImages'])->name('advancereports.products_with_images');
    Route::get('advancereports/products_with_images', [AdvancereportsController::class, 'productsWithImages'])->name('advancereports.products_with_images');

    Route::get('advancereports/export-error-report/{id}', [AdvancereportsController::class, 'ExportErrorReport'])->name('advancereports.export_error_report');
    Route::get('advancereports/updated-data-products-account', [AdvancereportsController::class, 'UpdatedDataProductsAccount'])->name('advancereports.updated_data_products_account');
    Route::get('advancereports/product-relations', [AdvancereportsController::class, 'ProductRelations'])->name('advancereports.product_relations');
    Route::get('advancereports/count-total-relation-id', [AdvancereportsController::class, 'CountTotalRelationId'])->name('advancereports.count_total_relation_id');
    Route::get('advancereports/count-total-product-key', [AdvancereportsController::class, 'CountTotalProductKey'])->name('advancereports.count_total_product_key');

    Route::resource('advancereports', AdvancereportsController::class);
});
