@extends('adminlte::page')

@section('title', 'Reporte')

@section('content_header')
    <h1>Reportes Avanzados</h1>
@stop



@section('content')
    <hr>
    <div class="row">
        <div class="col-md-12">
            <form class="filter-form" method="POST" action="{{ route('advancereports.saleitems_by_vendor') }}">
                <div class="col-md-6">
                    <p>Fecha inicio</p>
                    <input type="date" class="form-control" name="from_date" value="<?php echo date('Y-m-d') ?>" />
                    <p>Fecha fin</p>
                    <input type="date" class="form-control" name="to_date" value="<?php echo date('Y-m-d') ?>" />
                </div>
                <div class="col-md-3">
                    <p>&nbsp;</p>
                    <select id="vendor_id" name="vendor_id" class="control-form form-control">
                        <option value="0">Todos los proveedores</option>
                        @foreach ($vendors as $vendor)
                            <option value="{{ $vendor->id }}">{{ $vendor->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <p>&nbsp;</p>
                    <select name="group" class="control-form form-control">
                        <option value="1">Agrupar por codigo</option>
                        <option value="0">No agrupar por codigo</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <p>&nbsp;</p>
                    <select name="export" class="select-group control-form form-control">
                        <option value="1">Exportar</option>
                    </select>
                    <button type="submit" class="btn btn-primary btn-block">CONTINUAR</button>
                </div>
            </form>
        </div>
    </div>
    <hr>
    @if (isset($result))
        <div class="row">
            <div class="col-md-12">
                <table class="table">
                    <thead>
                        <tr>
                            <td>Codigo</td>
                            <td>Producto</td>
							<td>Equivalencia</td>
                            <td>Proveedor</td>
                            <td>Cliente</td>
                            <td>Factura</td>
                            <td>Vendedor</td>
                            <td>Tipo de pago</td>
                            <td>Costo</td>
                            <td>Precio</td>
                            <td>Unidades disponible</td>
                            <td>Cantidad en Bodega</td>
                            <td>Equivalencias en Bodega</td>
                            <td>Unidades Vendidas</td>
                            <td>Unidades Devueltas</td>
                            <td>Costo Total</td>
                            <td>Monto Total</td>
                            <td>Monto Reembolsado</td>
                            <td>Monto Final</td>
                            <td>Tienda</td>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($result as $item)
                            <tr>
                                <td>{{ $item['product_key'] }}</td>
                                <td>{{ $item['notes'] }}</td>
                                <td>{{ $item['relation_id'] }}</td>
                                <td>{{ $item['vendor'] }}</td>
                                <td>{{ $item['client'] }}</td>
                                <td>{{ $item['invoice_number'] }}</td>
                                <td>{{ $item['seller'] }}</td>
                                <td>{{ $item['payment_type'] }}</td>
                                <td>{{ $item['cost'] }}</td>
                                <td>{{ $item['price'] }}</td>
                                <td>{{ $item['availableQty'] }}</td>
                                <td>{{ $item['qtyInWarehouse'] }}</td>
                                <td>{{ $item['relation_qty'] }}</td>
                                <td>{{ $item['qty'] }}</td>
                                <td>{{ $item['qty_refunded'] }}</td>
                                <td>{{ $item['total_cost'] }}</td>
                                <td>{{ $item['total'] }}</td>
                                <td>{{ $item['total_refunded'] }}</td>
                                <td>{{ $item['total'] - $item['total_refunded'] }}</td>
                                <td>{{ implode(',', $item['accounts']) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
    <script src="//cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            $('table').DataTable({
                "order": [[0, "desc"]]
            });
        });
        
        $("#vendor_id").chosen({
            disable_search_threshold: 10,
            no_results_text: "Oops, nothing found!",
            width: "100%"
        });
    </script>
@stop
