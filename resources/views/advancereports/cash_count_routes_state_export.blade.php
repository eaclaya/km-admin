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

                            <td>{{ number_format($actual_pago, 2, '.', ',') }}</td>
                            <td>{{ number_format($pasada_debe , 2, '.', ',')}}</td>
                            <td>{{ number_format($actual_debe , 2, '.', ',')}}</td>
                            <td>{{ number_format($pasado_antes , 2, '.', ',')}}</td>

                            <td>{{ number_format($total , 2, '.', ',')  }}</td>
                            <td>{{ $porcentaje . '%' }}</td>
                            <td>{{ $pending }}</td>
                        </tr>
                    @endforeach
            </tbody>
        </table>
    </div>
</div>
