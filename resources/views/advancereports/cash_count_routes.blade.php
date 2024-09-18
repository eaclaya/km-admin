@extends('adminlte::page')

@section('title', 'Reporte')

@section('content_header')
    <h1>Reportes Avanzados</h1>
@stop



@section('content')
    <hr>
    <div class="row">
        <div class="col-md-12">
            <form class="filter-form" method="POST" action="{{ route('advancereports.routes') }}">
                <div class="col-md-2">
                    <p># Factura</p>
                    <input type="text" class="form-control" name="no_factura" />
                </div>

                <div class="col-md-2">
                    <p>Fecha inicio</p>
                    <input type="date" class="form-control" name="from_date" value="<?php echo date('Y-m-d'); ?>" />
                </div>
                <div class="col-md-2">
                    <p>Fecha fin</p>
                    <input type="date" class="form-control" name="to_date" value="<?php echo date('Y-m-d'); ?>" />
                </div>

                <div class="col-md-2">
                    <p>Tienda</p>
                    <select name="store" id="store" class="control-form form-control">
                        <option value="0">Todas</option>
                        @foreach ($stores as $store)
                            <option value="{{ $store->id }}">{{ $store->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-2">
                    <p>Tipos De Reporte</p>
                    <select name="reporte" id="reporte" class="control-form form-control">
                        <option value="">Seleccionar</option>
                        <option value="meta_vendido">Meta y Vendido</option>
                        <option value="contado">Facturas contado</option>
                        <option value="credito">Facturas credito</option>
                        <option value="abono">Abonos de clientes</option>
                        <option value="otros">Otros ingresos, Sobrantes y Comi POS</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <p>Tipos De Pagos</p>
                    <select name="pay_id" id="pay_id" class="control-form form-control">
                        <option value="">Todas</option>
                        @foreach ($paymentTypes as $payment)
                            <option value="{{ $payment->id }}">{{ $payment->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <p>&nbsp;</p>
                    <select name="export" class="control-form form-control">
                        <option value="0">Ver Resultados</option>
                        <option value="1">Exportar</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <p>&nbsp;</p>
                    <button type="submit" class="btn btn-primary btn-block">CONTINUAR</button>
                </div>
            </form>
        </div>
      </div>
      <br>
      <p>Si escoje la opcion "Meta y Vendido" en tipo de reporte tendrá en cuenta solo la tienda y la fecha</p>
    <hr>
    @if (isset($data['reporte']) && $data['reporte'] = 'meta_vendido')
    <div class="row">
        <div class="col-md-12">
           
            <table class="table">
                <thead>
                    <tr>
                        <td>Ruta</td>
                        <td>Fechas</td>
                        <td>Meta Actual</td>
                        <td>Meta Diaria /15</td>
                        <td>Contado Pagadas + Creditos</td>
                        <td>Contado Pendiente 15na pasada</td>
                        <td>Contado Pendiente 15na actual</td>
                        <td>Contado Pendiente Mes Pasado</td>
                        <td>Total Facturado</td>
                        <td>Cumplido</td>
                        <td>Pendiente</td>

                    </tr>
                </thead>
                <tbody>
                    @foreach ($result as $key => $item)
                    <?php 

                       // Fecha actual
                       if(isset($item['fecha_in'])){
                        $now = Carbon\Carbon::parse($item['fecha_in']);
                       }else{
                        $now = Carbon\Carbon::now();
                       };

                        if ($now->day <= 15) {
                            $primerDiaQuincenaActual = $now->copy()->startOfMonth();
                            $primerDiaQuincenaPasada = $now->copy()->subMonth()->startOfMonth()->addDays(15);
                        } else {
                            $primerDiaQuincenaActual = $now->copy()->startOfMonth()->addDays(15);
                            $primerDiaQuincenaPasada = $now->copy()->startOfMonth();

                        }

                            $meta =  $item['goal']??0;
                            $meta = is_numeric($meta) ? floatval($meta) : 0; 
                            $total = $item["total_all"]->sum('total');
                            $porcentaje = $meta>0 && $total > 0? number_format( $total / $meta  *  100, 2 ) : 0;
                            $pending = $meta>0?  $meta- $total : 0;
                            $actual_debe = 0;
                            $actual_pago = 0;
                            $pasada_debe = 0;
                            $pasado_antes = 0;

                            foreach($item["total_all"] as $total_all){
                                foreach($total_all['invoices'] as $invoice){
                                    if($invoice['is_credit'] == false){
                                        //actual
                                        //1 es igual a borrador o pendiente de pago
                                        if($invoice["invoice_date"] >= $primerDiaQuincenaActual->format('Y-m-d') ){
                                            if($invoice["invoice_status_id"] == '1' 
                                            || ($invoice["invoice_status_id"] != '1' && $invoice["last_payment_date"] > $item["fecha_fin"] ) ){
                                                $actual_debe += $invoice['amount'];
                                            }elseif($invoice["invoice_status_id"] == '6' && $invoice["last_payment_date"] <= $item["fecha_fin"] ){
                                                //si es contado pagado menor a hoy se suma a pagado
                                                $actual_pago += $invoice['amount'];
                                            }
                                        }
                                    }else{
                                        //si es credito se suma a pagado
                                        $actual_pago += $invoice['amount'];
                                    }

                                }
                            }

                            foreach($item['invoices_old'] as $old){
                                if($old["invoice_date"] >= $primerDiaQuincenaPasada->format('Y-m-d') ){
                                    //si es contado de la quincena pasada se suma
                                    $pasada_debe += $old['amount'];
                                }else{
                                    //si es contado mas antiguo se suma
                                    $pasado_antes += $old['amount'];
                                }
                            }

                            ?>
                        <tr>
                            <td>{{ $key }}</td>
                            <td>{{ $item["fecha_in"] . ' a ' .$item["fecha_fin"] }}</td>
                            <td>{{ number_format($meta , 2, '.', ',') }}</td>
                            <td>{{ number_format($meta / 15, 2, '.', ',') }}</td>

                            <td>{{ number_format($actual_pago, 2, '.', ',') }}
                                <div class="hidden">"total_all"{{ json_encode($item["total_all"]??'') }}</div>
                            </td>
                            <td>{{ number_format($pasada_debe , 2, '.', ',')}}</td>
                            <td>{{ number_format($actual_debe , 2, '.', ',')}}</td>
                            <td>{{ number_format($pasado_antes , 2, '.', ',')}}
                                <div class="hidden">'invoices_old'{{ json_encode($item['invoices_old']??'') }}</div>
                            </td>

                            <td>{{ number_format($total , 2, '.', ',')  }}</td>
                            <td>{{ $porcentaje . '%' }}</td>
                            <td>{{ $pending }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @else
        <div class="row">
            <div class="col-md-12">
                <table class="table">
                    <thead>
                        <tr>
                            <td>ID</td>
                            <td>Ruta</td>
                            <td>Tienda</td>
                            <td>Numero</td>
                            <td>Fecha</td>
                            <td>Tipo Pago</td>
                            <td>Usuario</td>

                            @if(isset($result[0]->ca_name))
                                <td>Categoria</td>
                                <td>Descripcion</td>
                            @else
                                <td>Estado</td>
                                <td>Dto %</td>
                                <td>Dto puntos</td>
                                <td>Dto Vales</td>
                                <td>Dto Total</td>
                                <td>Balance (debe)</td>
                            @endif

                            <td>Total</td>
                            <td>ID Cierre</td>
                            <td>Empleado</td>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($result as $item)
                            <tr>
                                <td>{{ $item->id }}</td>
                                <td>{{ $item->route_name??'' }}</td>
                                <td>{{ $item->account_name }}</td>
                                <td>{{ $item->number }}</td>
                                <td>{{ $item->payment_date}}</td>
                                <td>{{ substr($item->fat_name??'', 0, 20) }}</td>
                                <td>{{ $item->user_name .' '.$item->user_name2 }}</td>

                                @if(isset($result[0]->ca_name))
                                    <td>{{$item->ca_name??''}}</td>
                                    <td>{{ $item->description??''}}</td>

                                @else

                                    <td>
                                        @if(isset($item->payment_status_id))
                                            @if ($item->payment_status_id == 1)
                                                PENDING
                                            @elseif ($item->payment_status_id == 2)
                                                VOIDED
                                            @elseif ($item->payment_status_id == 3)
                                                FAILED
                                            @elseif ($item->payment_status_id == 4)
                                                COMPLETED
                                            @elseif ($item->payment_status_id == 5)
                                                PARTIALLY_REFUNDED
                                            @elseif ($item->payment_status_id == 6)
                                                REFUNDED
                                            @endif
                                        @endif
                                    </td>
                                    <td>{{ $item->discount_percent??'' }}</td>
                                    <td>{{ $item->discount_points??'' }}</td>
                                    <td>{{ $item->discount_vouchers??'' }}</td>
                                    <td>{{ $item->discount??'' }}</td>
                                    <td>{{ $item->balance??'' }}</td>

                                @endif
                                <td>{{ $item->amount??'' }}</td>
                                <td>{{ $item->cash_count_id }}</td>
                                <td>{{ $item->emp_name.' '.$item->emp_name2}}</td>
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
                "pageLength": 50,
                "order": [
                    [2, "desc"]
                ]
            });
        });
        $("#pay_id").chosen({
            disable_search_threshold: 10,
            no_results_text: "Oops, nothing found!",
            width: "100%"
        });
        $("#store").chosen({
            disable_search_threshold: 10,
            no_results_text: "Oops, nothing found!",
            width: "100%"
        });
    </script>
@stop
