@extends('adminlte::page')

@section('title', 'index')

@section('content_header')
    <h1>Reportes Avanzados</h1>
@stop

@section('content')

    <div class="container">

        <div class="row">
            <div class="mt-2" style="margin-top: 10px;">
                @if (
                    (isset(Auth::user()->realUser()->role->name) && Auth::user()->realUser()->role->name == 'Usuario especial') ||
                        (isset(Auth::user()->realUser()->role->name) &&
                            Auth::user()->realUser()->role->name == 'finanzas especial') /* || Auth::user()->_can('finance')  */)
                    <a href="/all-commands" class="btn btn-success">
                        Botones de Ejecucion
                        <i class="fa fa-unlook"></i>
                    </a>
                @endif
            </div>

            @if (!isset($roles['export_inventory_general']))
                <div class="col-md-3">
                    <div class="card">
                        <a href="{{ route('advancereports.export_inventory_general') }}">
                            <div class="icon-wrapper"><i class="fa fa-book fa-2x"></i></div>
                            <div class="title">Exportar inventario general</div>
                        </a>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <a href="{{ route('advancereports.stock_in_stores') }}">
                                <div class="icon-wrapper"><i class="fa fa-book fa-2x"></i></div>
                            <div class="title">Total Inventario general plus</div>
                        </a>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <a href="{{ route('advancereports.commission_old_products') }}">
                            <div class="icon-wrapper"><i class="fa fa-book fa-2x"></i></div>
                            <div class="title">Comisión Productos Antiguos 4%</div>
                        </a>
                    </div>
                </div>
            @endif

            @if (!isset($roles['deleted_invoices']))
                <div class="col-md-3">
                    <div class="card">
                        <a href="{{ route('advancereports.invoices_deleted') }}">
                            <div class="icon-wrapper"><i class="fa fa-book fa-2x"></i></div>
                            <div class="title">Facturas y productos eliminados</div>
                        </a>
                    </div>
                </div>
            @endif
            @if (!isset($roles['export_inventory']))
                <div class="col-md-3">
                    <div class="card">
                        <a href="{{ route('advancereports.export_inventory') }}">
                            <div class="icon-wrapper"><i class="fa fa-book fa-2x"></i></div>
                            <div class="title">Exportar inventario de tienda</div>
                        </a>
                    </div>
                </div>
            @endif
            @if (!isset($roles['carts']))
                <div class="col-md-3">
                    <div class="card">
                        <a href="{{ route('advancereports.carts') }}">
                            <div class="icon-wrapper"><i class="fa fa-book fa-2x"></i></div>
                            <div class="title">Ventas Perdidas</div>
                        </a>
                    </div>
                </div>
            @endif
            @if (!isset($roles['export_clients']))
                <div class="col-md-3">
                    <div class="card">
                        <a href="{{ route('advancereports.export_clients') }}">
                            <div class="icon-wrapper"><i class="fa fa-book fa-2x"></i></div>
                            <div class="title">Exportar clientes</div>
                        </a>
                    </div>
                </div>
            @endif
            @if (!isset($roles['sales_by_client']))
                <div class="col-md-3">
                    <div class="card">
                        <a href="{{ route('advancereports.sales_by_client') }}">
                            <div class="icon-wrapper"><i class="fa fa-book fa-2x"></i></div>
                            <div class="title">Ventas agrupadas por cliente</div>
                        </a>
                    </div>
                </div>
            @endif
            @if (!isset($roles['compare_client_sales']))
                <div class="col-md-3">
                    <div class="card">
                        <a href="{{ route('advancereports.compare_client_sales') }}">
                            <div class="icon-wrapper"><i class="fa fa-book fa-2x"></i></div>
                            <div class="title">Ventas historicas por cliente</div>
                        </a>
                    </div>
                </div>
            @endif
            @if (!isset($roles['monthly_client_sales']))
                <div class="col-md-3">
                    <div class="card">
                        <a href="{{ route('advancereports.monthly_client_sales') }}">
                            <div class="icon-wrapper"><i class="fa fa-book fa-2x"></i></div>
                            <div class="title">Ventas mensuales por cliente</div>
                        </a>
                    </div>
                </div>
            @endif
            @if (!isset($roles['monthly_salaries']))
                <div class="col-md-3">
                    <div class="card">
                        <a href="{{ route('advancereports.monthly_salaries') }}">
                            <div class="icon-wrapper"><i class="fa fa-book fa-2x"></i></div>
                            <div class="title">Salarios mensuales</div>
                        </a>
                    </div>
                </div>
            @endif
            @if (!isset($roles['related_product_sales']))
                <div class="col-md-3">
                    <div class="card">
                        <a href="{{ route('advancereports.related_product_sales') }}">
                            <div class="icon-wrapper"><i class="fa fa-book fa-2x"></i></div>
                            <div class="title">Ventas de equivalencias</div>
                        </a>
                    </div>
                </div>
            @endif
            @if (!isset($roles['sales_by_date']))
                <div class="col-md-3">
                    <div class="card">
                        <a href="{{ route('advancereports.sales_by_date') }}">
                            <div class="icon-wrapper"><i class="fa fa-book fa-2x"></i></div>
                            <div class="title">Ventas diarias</div>
                        </a>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <a href="{{ route('advancereports.sales_by_date_sum') }}">
                            <div class="icon-wrapper"><i class="fa fa-book fa-2x"></i></div>
                            <div class="title">Ventas diarias por rango</div>
                        </a>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <a href="{{ route('advancereports.sales_by_month') }}">
                            <div class="icon-wrapper"><i class="fa fa-book fa-2x"></i></div>
                            <div class="title">Ventas Mensuales</div>
                        </a>
                    </div>
                </div>
            @endif
            @if (!isset($roles['sales_utility_by_date']))
                <div class="col-md-3">
                    <div class="card">
                        <a href="{{ route('advancereports.sales_utility_by_date') }}">
                            <div class="icon-wrapper"><i class="fa fa-book fa-2x"></i></div>
                            <div class="title">Ventas diarias + utilidad</div>
                        </a>
                    </div>
                </div>
            @endif
            @if (!isset($roles['refunds']))
                <div class="col-md-3">
                    <div class="card">
                        <a href="{{ route('advancereports.refunds') }}">
                            <div class="icon-wrapper"><i class="fa fa-book fa-2x"></i></div>
                            <div class="title">Devoluciones</div>
                        </a>
                    </div>
                </div>
            @endif
            @if (!isset($roles['refund_settings']))
                <div class="col-md-3">
                    <div class="card">
                        <a href="{{ route('advancereports.refund_settings') }}">
                            <div class="icon-wrapper"><i class="fa fa-book fa-2x"></i></div>
                            <div class="title">Datos de devoluciones</div>
                        </a>
                    </div>
                </div>
            @endif
            @if (!isset($roles['sales_per_client']))
                <div class="col-md-3">
                    <div class="card">
                        <a href="{{ route('advancereports.sales_per_client') }}">
                            <div class="icon-wrapper"><i class="fa fa-book fa-2x"></i></div>
                            <div class="title">Promedio de ventas</div>
                        </a>
                    </div>
                </div>
            @endif
            @if (!isset($roles['devices']))
                <div class="col-md-3">
                    <div class="card">
                        <a href="{{ route('advancereports.devices') }}">
                            <div class="icon-wrapper"><i class="fa fa-book fa-2x"></i></div>
                            <div class="title">Dispositivos Conectados</div>
                        </a>
                    </div>
                </div>
            @endif

            @if (!isset($roles['sales_by_time_period']))
                <div class="col-md-3">
                    <div class="card">
                        <a href="{{ route('advancereports.sales_by_time_period') }}">
                            <div class="icon-wrapper"><i class="fa fa-book fa-2x"></i></div>
                            <div class="title">Entradas/Salidas por periodo de tiempo</div>
                        </a>
                    </div>
                </div>
            @endif
            @if (!isset($roles['sales_by_category']))
                <div class="col-md-3">
                    <div class="card">
                        <a href="{{ route('advancereports.sales_by_category') }}">
                            <div class="icon-wrapper"><i class="fa fa-book fa-2x"></i></div>
                            <div class="title">Ventas por categoria</div>
                        </a>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <a href="{{ route('advancereports.subcategory_group_by_category') }}">
                            <div class="icon-wrapper"><i class="fa fa-book fa-2x"></i></div>
                            <div class="title">Ventas por sub categoria agrupadas por categorias</div>
                        </a>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <a href="{{ route('advancereports.sales_subcategory_by_month') }}">
                            <div class="icon-wrapper"><i class="fa fa-book fa-2x"></i></div>
                            <div class="title">Ventas por sub categoria por mes</div>
                        </a>
                    </div>
                </div>
            @endif
            @if (!isset($roles['sales_by_client_type']))
                <div class="col-md-3">
                    <div class="card">
                        <a href="{{ route('advancereports.sales_by_client_type') }}">
                            <div class="icon-wrapper"><i class="fa fa-book fa-2x"></i></div>
                            <div class="title">Ventas por tipo de cliente</div>
                        </a>
                    </div>
                </div>
            @endif
            @if (!isset($roles['receivables_by_route']))
                <div class="col-md-3">
                    <div class="card">
                        <a href="{{ route('advancereports.receivables_by_route') }}">
                            <div class="icon-wrapper"><i class="fa fa-book fa-2x"></i></div>
                            <div class="title">Analisis de vencimiento por ruta</div>
                        </a>
                    </div>
                </div>
            @endif
            @if (!isset($roles['sales_by_vendor']))
                <div class="col-md-3">
                    <div class="card">
                        <a href="{{ route('advancereports.sales_by_vendor') }}">
                            <div class="icon-wrapper"><i class="fa fa-book fa-2x"></i></div>
                            <div class="title">Ventas por proveedor</div>
                        </a>
                    </div>
                </div>
            @endif
            @if (!isset($roles['sales_by_seller']))
                <div class="col-md-3">
                    <div class="card">
                        <a href="{{ route('advancereports.sales_by_seller') }}">
                            <div class="icon-wrapper"><i class="fa fa-book fa-2x"></i></div>
                            <div class="title">Ventas por vendedor</div>
                        </a>
                    </div>
                </div>
            @endif

            @if (!isset($roles['sales_by_sac']))
                <div class="col-md-3">
                    <div class="card">
                        <a href="{{ route('advancereports.sales_by_sac') }}">
                            <div class="icon-wrapper"><i class="fa fa-book fa-2x"></i></div>
                            <div class="title">Ventas por Aux SAC</div>
                        </a>
                    </div>
                </div>
            @endif

            @if (isset(Auth::user()->realUser()->role->name) && Auth::user()->realUser()->role->name != 'Mayoreo')
                <div class="col-md-3">
                    <div class="card">
                        <a href="{{ route('advancereports.routes_visits_day') }}">
                            <div class="icon-wrapper"><i class="fa fa-book fa-2x"></i></div>
                            <div class="title">Visitas por Rutas (Dia)</div>
                        </a>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card">
                        <a href="{{ route('advancereports.routes_visits') }}">
                            <div class="icon-wrapper"><i class="fa fa-book fa-2x"></i></div>
                            <div class="title">Visitas por Rutas (Global)</div>
                        </a>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card">
                        <a href="{{ route('advancereports.clients_passed') }}">
                            <div class="icon-wrapper"><i class="fa fa-book fa-2x"></i></div>
                            <div class="title">Clientes Nuevos > L2500</div>
                        </a>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card">
                        <a href="{{ route('advancereports.clients_unvisited') }}">
                            <div class="icon-wrapper"><i class="fa fa-book fa-2x"></i></div>
                            <div class="title">Clientes No Visitados</div>
                        </a>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card">
                        <a href="{{ route('advancereports.promises_pay') }}">
                            <div class="icon-wrapper"><i class="fa fa-book fa-2x"></i></div>
                            <div class="title">Promesas de pago (Rutas)</div>
                        </a>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card">
                        <a href="{{ route('advancereports.category_clients') }}">
                            <div class="icon-wrapper"><i class="fa fa-book fa-2x"></i></div>
                            <div class="title">Categorías de clientes (Rutas)</div>
                        </a>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card">
                        <a href="{{ route('advancereports.documentation_clients') }}">
                            <div class="icon-wrapper"><i class="fa fa-book fa-2x"></i></div>
                            <div class="title">Documentación de clientes (Rutas)</div>
                        </a>
                    </div>
                </div>
            @endif

            @if (!isset($roles['net_sales_by_seller']))
                <div class="col-md-3">
                    <div class="card">
                        <a href="{{ route('advancereports.net_sales_by_seller') }}">
                            <div class="icon-wrapper"><i class="fa fa-book fa-2x"></i></div>
                            <div class="title">Ventas netas por vendedor</div>
                        </a>
                    </div>
                </div>
            @endif
            @if (!isset($roles['sales_by_mechanic']))
                <div class="col-md-3">
                    <div class="card">
                        <a href="{{ route('advancereports.sales_by_mechanic') }}">
                            <div class="icon-wrapper"><i class="fa fa-book fa-2x"></i></div>
                            <div class="title">Ventas por mecanico</div>
                        </a>
                    </div>
                </div>
            @endif
            @if (!isset($roles['invoices_draft']))
                <div class="col-md-3">
                    <div class="card">
                        <a href="{{ route('advancereports.invoices_draft') }}">
                            <div class="icon-wrapper"><i class="fa fa-book fa-2x"></i></div>
                            <div class="title">Facturas en borrador</div>
                        </a>
                    </div>
                </div>
            @endif
            @if (!isset($roles['invoices_converted']))
                <div class="col-md-3">
                    <div class="card">
                        <a href="{{ route('advancereports.invoices_converted') }}">
                            <div class="icon-wrapper"><i class="fa fa-book fa-2x"></i></div>
                            <div class="title">Discrepancias en facturas convertidas</div>
                        </a>
                    </div>
                </div>
            @endif
            @if (!isset($roles['billing']))
                <div class="col-md-3">
                    <div class="card">
                        <a href="{{ route('advancereports.billing') }}">
                            <div class="icon-wrapper"><i class="fa fa-book fa-2x"></i></div>
                            <div class="title">Datos de facturacion</div>
                        </a>
                    </div>
                </div>
            @endif
            @if (!isset($roles['report_account_settings']))
                <div class="col-md-3">
                    <div class="card">
                        <a href="{{ route('advancereports.account_settings') }}">
                            <div class="icon-wrapper"><i class="fa fa-book fa-2x"></i></div>
                            <div class="title">Configuracion de Tiendas</div>
                        </a>
                    </div>
                </div>
            @endif
            @if (!isset($roles['payments_by_seller']))
                <div class="col-md-3">
                    <div class="card">
                        <a href="{{ route('advancereports.payments_by_seller') }}">
                            <div class="icon-wrapper"><i class="fa fa-book fa-2x"></i></div>
                            <div class="title">Pagos por vendedor</div>
                        </a>
                    </div>
                </div>
            @endif
            @if (!isset($roles['saleitems_by_vendor']))
                <div class="col-md-3">
                    <div class="card">
                        <a href="{{ route('advancereports.saleitems_by_vendor') }}">
                            <div class="icon-wrapper"><i class="fa fa-book fa-2x"></i></div>
                            <div class="title">Productos vendidos por proveedor</div>
                        </a>
                    </div>
                </div>
            @endif
            @if (!isset($roles['stock_by_vendor']))
                <div class="col-md-3">
                    <div class="card">
                        <a href="{{ route('advancereports.products_by_vendor') }}">
                            <div class="icon-wrapper"><i class="fa fa-book fa-2x"></i></div>
                            <div class="title">Existencias totales por proveedor</div>
                        </a>
                    </div>
                </div>
            @endif
            @if (!isset($roles['sales_by_product']))
                <div class="col-md-3">
                    <div class="card">
                        <a href="{{ route('advancereports.sales_by_product') }}">
                            <div class="icon-wrapper"><i class="fa fa-book fa-2x"></i></div>
                            <div class="title">Ventas por producto</div>
                        </a>
                    </div>
                </div>
            @endif
            @if (!isset($roles['stock_by_product']))
                <div class="col-md-3">
                    <div class="card">
                        <a href="{{ route('advancereports.stock_by_product') }}">
                            <div class="icon-wrapper"><i class="fa fa-book fa-2x"></i></div>
                            <div class="title">Existencias por producto</div>
                        </a>
                    </div>
                </div>
            @endif
            @if (!isset($roles['stock_by_vendor']))
                <div class="col-md-3">
                    <div class="card">
                        <a href="{{ route('advancereports.stock_by_vendor') }}">
                            <div class="icon-wrapper"><i class="fa fa-book fa-2x"></i></div>
                            <div class="title">Existencias por proveedor</div>
                        </a>
                    </div>
                </div>
            @endif
            @if (!isset($roles['supplies']))
                <div class="col-md-3">
                    <div class="card">
                        <a href="{{ route('advancereports.supplies') }}">
                            <div class="icon-wrapper"><i class="fa fa-book fa-2x"></i></div>
                            <div class="title">Surtidos</div>
                        </a>
                    </div>
                </div>
            @endif
            @if (!isset($roles['orders']))
                <div class="col-md-3">
                    <div class="card">
                        <a href="{{ route('advancereports.orders') }}">
                            <div class="icon-wrapper"><i class="fa fa-book fa-2x"></i></div>
                            <div class="title">Encargos</div>
                        </a>
                    </div>
                </div>
            @endif
            @if (!isset($roles['order_items']))
                <div class="col-md-3">
                    <div class="card">
                        <a href="{{ route('advancereports.order_items') }}">
                            <div class="icon-wrapper"><i class="fa fa-book fa-2x"></i></div>
                            <div class="title">Productos encargados</div>
                        </a>
                    </div>
                </div>
            @endif
            @if (!isset($roles['customer_purchases_frequency']))
                <div class="col-md-3">
                    <div class="card">
                        <a href="{{ route('advancereports.customer_purchases_frequency') }}">
                            <div class="icon-wrapper"><i class="fa fa-book fa-2x"></i></div>
                            <div class="title">Frecuencia de compra de clientes</div>
                        </a>
                    </div>
                </div>
            @endif
            @if (!isset($roles['customers_inactive']))
                <div class="col-md-3">
                    <div class="card">
                        <a href="{{ route('advancereports.customers_inactive') }}">
                            <div class="icon-wrapper"><i class="fa fa-book fa-2x"></i></div>
                            <div class="title">Clientes inactivos</div>
                        </a>
                    </div>
                </div>
            @endif
            @if (!isset($roles['clients_by_date']))
                <div class="col-md-3">
                    <div class="card">
                        <a href="{{ route('advancereports.clients_by_date') }}">
                            <div class="icon-wrapper"><i class="fa fa-book fa-2x"></i></div>
                            <div class="title">Clientes Recientes</div>
                        </a>
                    </div>
                </div>
            @endif
            @if (!isset($roles['invoice_points']))
                <div class="col-md-3">
                    <div class="card">
                        <a href="{{ route('advancereports.invoice_points') }}">
                            <div class="icon-wrapper"><i class="fa fa-book fa-2x"></i></div>
                            <div class="title">Puntos canjeados</div>
                        </a>
                    </div>
                </div>
            @endif
            @if (!isset($roles['customers_points']))
                <div class="col-md-3">
                    <div class="card">
                        <a href="{{ route('advancereports.customers_points') }}">
                            <div class="icon-wrapper"><i class="fa fa-book fa-2x"></i></div>
                            <div class="title">Puntos de clientes</div>
                        </a>
                    </div>
                </div>
            @endif
            @if (!isset($roles['markers']))
                {{-- <div class="col-md-3">
                    <div class="card">
                        <a href="{{ route('clients.markers') }}">
                            <div class="icon-wrapper"><i class="fa fa-book fa-2x"></i></div>
                            <div class="title">Mapa de clientes</div>
                        </a>
                    </div>
                </div> --}}
            @endif
            @if (!isset($roles['visits']))
                <div class="col-md-3">
                    <div class="card">
                        <a href="{{ route('advancereports.visits') }}">
                            <div class="icon-wrapper"><i class="fa fa-book fa-2x"></i></div>
                            <div class="title">Visitas a clientes</div>
                        </a>
                    </div>
                </div>
            @endif
            @if (!isset($roles['users_activity']))
                {{-- <div class="col-md-3">
                    <div class="card">
                        <a href="{{ route('usersmanagement.users_activity') }}">
                            <div class="icon-wrapper"><i class="fa fa-book fa-2x"></i></div>
                            <div class="title">Actividad de usuarios</div>
                        </a>
                    </div>
                </div> --}}
            @endif
            @if (!isset($roles['products_underwholesaleprice']))
                <div class="col-md-3">
                    <div class="card">
                        <a href="{{ route('advancereports.products_underwholesaleprice') }}">
                            <div class="icon-wrapper"><i class="fa fa-book fa-2x"></i></div>
                            <div class="title">Ventas menores al precio mayorista</div>
                        </a>
                    </div>
                </div>
            @endif
            @if (!isset($roles['products_undernormalprice']))
                <div class="col-md-3">
                    <div class="card">
                        <a href="{{ route('advancereports.products_undernormalprice') }}">
                            <div class="icon-wrapper"><i class="fa fa-book fa-2x"></i></div>
                            <div class="title">Ventas menores al precio taller</div>
                        </a>
                    </div>
                </div>
            @endif
            @if (!isset($roles['packing_to_invoice']))
                <div class="col-md-3">
                    <div class="card">
                        <a href="{{ route('advancereports.packing_to_invoice') }}">
                            <div class="icon-wrapper"><i class="fa fa-book fa-2x"></i></div>
                            <div class="title">Packing convertido</div>
                        </a>
                    </div>
                </div>
            @endif
            @if (!isset($roles['products_in_packing']))
                {{-- <div class="col-md-3">
                    <div class="card">
                        <a href="{{ route('packings.products_in_packing') }}">
                            <div class="icon-wrapper"><i class="fa fa-book fa-2x"></i></div>
                            <div class="title">Productos en packings</div>
                        </a>
                    </div>
                </div> --}}
                {{-- <div class="col-md-3">
                    <div class="card">
                        <a href="{{ route('verifyorder.products_in_verify') }}">
                            <div class="icon-wrapper"><i class="fa fa-book fa-2x"></i></div>
                            <div class="title">Productos en Verificacion</div>
                        </a>
                    </div>
                </div> --}}
            @endif
            @if (!isset($roles['transfers_in_packing']))
                {{-- <div class="col-md-3">
                    <div class="card">
                        <a href="{{ route('packings.transfers_in_packing') }}">
                            <div class="icon-wrapper"><i class="fa fa-book fa-2x"></i></div>
                            <div class="title">Transferencias en packings</div>
                        </a>
                    </div>
                </div> --}}
            @endif
            @if (!isset($roles['transfers_pending']))
                <div class="col-md-3">
                    <div class="card">
                        <a href="{{ route('advancereports.transfers') }}">
                            <div class="icon-wrapper"><i class="fa fa-book fa-2x"></i></div>
                            <div class="title">Transferencias pendientes y completadas</div>
                        </a>
                    </div>
                </div>
            @endif
            @if (!isset($roles['transfer_items_pending']))
                <div class="col-md-3">
                    <div class="card">
                        <a href="{{ route('advancereports.transfer_items_pending') }}">
                            <div class="icon-wrapper"><i class="fa fa-book fa-2x"></i></div>
                            <div class="title">Productos pendientes en transferencia</div>
                        </a>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <a href="{{ route('advancereports.transfer_items_accepted') }}">
                            <div class="icon-wrapper"><i class="fa fa-book fa-2x"></i></div>
                            <div class="title">Productos aceptados en transferencia</div>
                        </a>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <a href="{{ route('advancereports.transfer_items_accepted_by_time_period') }}">
                            <div class="icon-wrapper"><i class="fa fa-book fa-2x"></i></div>
                            <div class="title">Productos aceptados en transferencia en periodo de tiempo</div>
                        </a>
                    </div>
                </div>
            @endif
            @if (!isset($roles['imports_pending']))
                <div class="col-md-3">
                    <div class="card">
                        <a href="{{ route('advancereports.imports_pending') }}">
                            <div class="icon-wrapper"><i class="fa fa-book fa-2x"></i></div>
                            <div class="title">Importaciones pendientes</div>
                        </a>
                    </div>
                </div>
            @endif
            @if (!isset($roles['transferitems_remain']))
                <div class="col-md-3">
                    <div class="card">
                        <a href="{{ route('advancereports.transferitems_remain') }}">
                            <div class="icon-wrapper"><i class="fa fa-book fa-2x"></i></div>
                            <div class="title">Faltantes/sobrantes transferencias</div>
                        </a>
                    </div>
                </div>
            @endif
            @if (!isset($roles['goal_configuration']))
                {{-- <div class="col-md-3">
                    <div class="card">
                        <a href="{{ route('goal.goal_configuration') }}">
                            <div class="icon-wrapper"><i class="fa fa-book fa-2x"></i></div>
                            <div class="title">Meta Tienda/Vendedores</div>
                        </a>
                    </div>
                </div> --}}
            @endif
            @if (!isset($roles['client_notes']))
                <div class="col-md-3">
                    <div class="card">
                        <a href="{{ route('advancereports.client_notes') }}">
                            <div class="icon-wrapper"><i class="fa fa-book fa-2x"></i></div>
                            <div class="title">Notas de clientes</div>
                        </a>
                    </div>
                </div>
            @endif
            @if (!isset($roles['tasks_by_employee']))
                <div class="col-md-3">
                    <div class="card">
                        <a href="{{ route('advancereports.tasks_by_employee') }}">
                            <div class="icon-wrapper"><i class="fa fa-book fa-2x"></i></div>
                            <div class="title">Tareas de empleados</div>
                        </a>
                    </div>
                </div>
            @endif
            @if (!isset($roles['stock_entries']))
                <div class="col-md-3">
                    <div class="card">
                        <a href="{{ route('advancereports.stock_entries') }}">
                            <div class="icon-wrapper"><i class="fa fa-book fa-2x"></i></div>
                            <div class="title">Movimientos de inventario</div>
                        </a>
                    </div>
                </div>
            @endif
            @if (!isset($roles['most_selled_products']))
                <div class="col-md-3">
                    <div class="card">
                        <a href="{{ route('advancereports.most_selled_products') }}">
                            <div class="icon-wrapper"><i class="fa fa-book fa-2x"></i></div>
                            <div class="title">Productos mas vendidos</div>
                        </a>
                    </div>
                </div>
            @endif
            @if (!isset($roles['less_selled_products']))
                <div class="col-md-3">
                    <div class="card">
                        <a href="{{ route('advancereports.less_selled_products') }}">
                            <div class="icon-wrapper"><i class="fa fa-book fa-2x"></i></div>
                            <div class="title">Productos menos vendidos</div>
                        </a>
                    </div>
                </div>
            @endif
            @if (!isset($roles['unselled_products']))
                <div class="col-md-3">
                    <div class="card">
                        <a href="{{ route('advancereports.unselled_products') }}">
                            <div class="icon-wrapper"><i class="fa fa-book fa-2x"></i></div>
                            <div class="title">Productos inactivos</div>
                        </a>
                    </div>
                </div>
            @endif
            @if (!isset($roles['most_requested_products']))
                <div class="col-md-3">
                    <div class="card">
                        <a href="{{ route('advancereports.most_requested_products') }}">
                            <div class="icon-wrapper"><i class="fa fa-book fa-2x"></i></div>
                            <div class="title">Productos mas pedidos</div>
                        </a>
                    </div>
                </div>
            @endif

            @if (!isset($roles['sale_transfer_products']))
                <div class="col-md-3">
                    <div class="card">
                        <a href="{{ route('advancereports.sale_transfer_products') }}">
                            <div class="icon-wrapper"><i class="fa fa-book fa-2x"></i></div>
                            <div class="title">Productos vendidos y transferidos</div>
                        </a>
                    </div>
                </div>
            @endif
            @if (!isset($roles['store_report']))
               {{--  <div class="col-md-3">
                    <div class="card">
                        <a href="{{ route('goal.store_report') }}">
                            <div class="icon-wrapper"><i class="fa fa-book fa-2x"></i></div>
                            <div class="title">Meta de Tiendas plus</div>
                        </a>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <a href="{{ route('goal.store_routes_report') }}">
                            <div class="icon-wrapper"><i class="fa fa-book fa-2x"></i></div>
                            <div class="title">Meta de Rutas</div>
                        </a>
                    </div>
                </div> --}}
            @endif
            @if (!isset($roles['seller_report']))
                {{-- <div class="col-md-3">
                    <div class="card">
                        <a href="{{ route('goal.seller_report') }}">
                            <div class="icon-wrapper"><i class="fa fa-book fa-2x"></i></div>
                            <div class="title">Meta de Vendedores plus</div>
                        </a>
                    </div>
                </div> --}}
            @endif

            @if (!isset($roles['input_entries']))
                <div class="col-md-3">
                    <div class="card">
                        <a href="{{ route('advancereports.input_entries') }}">
                            <div class="icon-wrapper"><i class="fa fa-book fa-2x"></i></div>
                            <div class="title">Historial de compras</div>
                        </a>
                    </div>
                </div>
            @endif
            @if (!isset($roles['invoice_history']))
                {{-- <div class="col-md-3">
                    <div class="card">
                        <a href="{{ route('invoices.invoice_history') }}">
                            <div class="icon-wrapper"><i class="fa fa-book fa-2x"></i></div>
                            <div class="title">Historial de facturas</div>
                        </a>
                    </div>
                </div> --}}
            @endif
            @if (!isset($roles['import_history']))
                {{-- <div class="col-md-3">
                    <div class="card">
                        <a href="{{ route('imports.import_history') }}">
                            <div class="icon-wrapper"><i class="fa fa-book fa-2x"></i></div>
                            <div class="title">Historial de importaciones</div>
                        </a>
                    </div>
                </div> --}}
            @endif
            @if (!isset($roles['cv_requests']))
                {{-- <div class="col-md-3">
                    <div class="card">
                        <a href="{{ route('curriculums.cv_requests') }}">
                            <div class="icon-wrapper"><i class="fa fa-book fa-2x"></i></div>
                            <div class="title">Curriculums</div>
                        </a>
                    </div>
                </div> --}}
            @endif
            @if (!isset($roles['wholesaler_requests']))
                {{-- <div class="col-md-3">
                    <div class="card">
                        <a href="{{ route('wholesalers.wholesaler_requests') }}">
                            <div class="icon-wrapper"><i class="fa fa-book fa-2x"></i></div>
                            <div class="title">Solicitudes Mayoristas</div>
                        </a>
                    </div>
                </div> --}}
            @endif
            @if (!isset($roles['available_cash']))
                <div class="col-md-3">
                    <div class="card">
                        <a href="{{ route('advancereports.available_cash') }}">
                            <div class="icon-wrapper"><i class="fa fa-book fa-2x"></i></div>
                            <div class="title">Efectivo Disponible</div>
                        </a>
                    </div>
                </div>
            @endif
            @if (!isset($roles['cash_count_net_sales']))
                <div class="col-md-3">
                    <div class="card">
                        <a href="{{ route('advancereports.cash_count_net_sales') }}">
                            <div class="icon-wrapper"><i class="fa fa-book fa-2x"></i></div>
                            <div class="title">Ventas netas en cierre de caja</div>
                        </a>
                    </div>
                </div>
            @endif
            @if (!isset($roles['expenses_by_category']))
                <div class="col-md-3">
                    <div class="card">
                        <a href="{{ route('advancereports.expenses_by_category') }}">
                            <div class="icon-wrapper"><i class="fa fa-book fa-2x"></i></div>
                            <div class="title">Gastos por categoria</div>
                        </a>
                    </div>
                </div>
            @endif
            @if (!isset($roles['quoted_products']))
                <div class="col-md-3">
                    <div class="card">
                        <a href="{{ route('advancereports.quoted_products') }}">
                            <div class="icon-wrapper"><i class="fa fa-book fa-2x"></i></div>
                            <div class="title">Productos cotizados</div>
                        </a>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <a href="{{ route('advancereports.proform_products') }}">
                            <div class="icon-wrapper"><i class="fa fa-book fa-2x"></i></div>
                            <div class="title">Productos en Proforma</div>
                        </a>
                    </div>
                </div>
            @endif
            @if (!isset($roles['no_updated_products']))
                <div class="col-md-3">
                    <div class="card">
                        <a href="{{ route('advancereports.no_updated_products') }}">
                            <div class="icon-wrapper"><i class="fa fa-book fa-2x"></i></div>
                            <div class="title">Productos no actualizados</div>
                        </a>
                    </div>
                </div>
            @endif

            @if (!isset($roles['tutorials']))
                <div class="col-md-3 hide">
                    <div class="card">
                        <a href="{{ route('advancereports.tutorials') }}">
                            <div class="icon-wrapper"><i class="fa fa-book fa-2x"></i></div>
                            <div class="title">Tutoriales</div>
                        </a>
                    </div>
                </div>
            @endif

            {{-- <div class="col-md-3">
                <div class="card">
                    <a href="{{ url('settings/reports') }}">
                        <div class="icon-wrapper"><i class="fa fa-book fa-2x"></i></div>
                        <div class="title">Reportes Generales</div>
                    </a>
                </div>
            </div> --}}
            @if (!isset($roles['export_invoices']))
                <div class="col-md-3">
                    <div class="card">
                        <a href="{{ route('advancereports.export_invoices') }}">
                            <div class="icon-wrapper"><i class="fa fa-book fa-2x"></i></div>
                            <div class="title">Exportar Facturas</div>
                        </a>
                    </div>
                </div>
            @endif
            @if (!isset($roles['export_inventory']))
                <div class="col-md-3">
                    <div class="card">
                        <a href="{{ route('advancereports.export_inventory_not_location') }}">
                            <div class="icon-wrapper"><i class="fa fa-book fa-2x"></i></div>
                            <div class="title">Exportar productos de tienda sin ubicación asignada</div>
                        </a>
                    </div>
                </div>
            @endif
            @if (!isset($roles['tracking_history']))
                {{-- <div class="col-md-3">
                    <div class="card">
                        <a href="{{ route('tracking_history.index') }}">
                            <div class="icon-wrapper"><i class="fa fa-book fa-2x"></i></div>
                            <div class="title">Tracking e Historial de Cambios</div>
                        </a>
                    </div>
                </div> --}}
            @endif
            @if (!isset($roles['traces_request']))
                <div class="col-md-3">
                    <div class="card">
                        <a href="{{ route('advancereports.traces_request') }}">
                            <div class="icon-wrapper"><i class="fa fa-book fa-2x"></i></div>
                            <div class="title">Solicitudes de Seguimientos</div>
                        </a>
                    </div>
                </div>
            @endif

            @if (!isset($roles['report_per_diem']))
                <div class="col-md-3">
                    <div class="card">
                        <a href="{{ route('advancereports.per_diem') }}">
                            <div class="icon-wrapper"><i class="fa fa-book fa-2x"></i></div>
                            <div class="title">Viáticos</div>
                        </a>
                    </div>
                </div>
            @endif
            @if (!isset($roles['old_price_report']))
                <div class="col-md-3">
                    <div class="card">
                        <a href="{{ route('advancereports.old_price_product_date') }}">
                            <div class="icon-wrapper"><i class="fa fa-book fa-2x"></i></div>
                            <div class="title">Ultimo Cambio de precios</div>
                        </a>
                    </div>
                </div>
            @endif
            <div class="col-md-3">
                <div class="card">
                    <a href="{{ route('advancereports.products_with_images') }}">
                        <div class="icon-wrapper"><i class="fa fa-book fa-2x"></i></div>
                        <div class="title">Imagenes de Productos</div>
                    </a>
                </div>
            </div>
        </div>

    </div>

@stop

@section('css')

    <style>
        .card {
            box-shadow: 0px 0px 5px #ddd;
            padding: 20px;
            margin-bottom: 20px;
        }

        .card a {
            text-decoration: none;
        }

        .card .icon-wrapper {
            text-align: center;
        }

        .card .title {
            margin-top: 10px;
            text-transform: uppercase;
            text-align: center;
        }
    </style>
@stop
