<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">

    <style>
        body{
            font-family: Arial;
            font-size: 10px;
            width: 300px;
        }
        img{
            width: 100px;
        }
        p{
            text-align: center;
        }
        table td, table th{
            font-size: 7px;
            border-bottom: 1px solid #ddd;
        }
        table thead{
            border: 1px solid black;
        }
        tfoot td{
            border: 0;

        }
        .float-right p{
            text-align: right;
        }
        @media print {
            .page-break {
                page-break-after: always;
            }
        }
    </style>
</head>
<body>
@foreach($dataArr as $data)
    @php
        $account = $data['account'];
        $client = $data['client'];
        $invoice = $data['invoice'];
        $employee = $data['employee'];
        $auxiliary = $data['auxiliary'];
        $entityType = $data['entityType'];
        $billing = $data['billing'];
    @endphp
    <div class="container">
        <p><strong>ORIGINAL</strong></p>
        <p><strong>CASA MATRIZ</strong></p>
        <p>{{strtoupper($account->Matrix_name)}} </p>
        <p>{{strtoupper($account->Matrix_address)}}</p>
        <hr>

{{--        <p><img src="{{ public_path().'/assets/logo/logo_negro.png' }}" alt=""></p>--}}
{{--        <p><img src="{{ base_path(). '/public/assets/logo/logo_negro.png' }}" alt=""></p>--}}
{{--        <p><img src="{{ storage_path('app/public/img/logo_menu.png') }}" alt=""></p>--}}
        <p><strong>{{$account->name}}</strong></p>
        <p>{{$account->address1}}</p>
        <p>{{$account->city}}, {{$account->state}}</p>
        <p>TEL:{{$account->work_phone}}</p>
        <p>EMAIL:{{$account->work_email}}</p>
        <p>RTN:{{$account->vat_number}}</p>
        <p>FECHA: {{$invoice->invoice_date}}</p>
        @if($invoice->end_date != '' || $invoice->end_date != null)
            <p>VENCIMIENTO: {{$invoice->end_date}}</p>
        @endif
        <br>
        @if($entityType == ENTITY_INVOICE && $account->show_billing_in_invoice)
            <p><strong>FACTURA:{{$invoice->invoice_number}}</strong></p>
        @else
            <p><strong>PROFORMA:{{$invoice->invoice_number}}</strong></p>
        @endif

        <p>TIPO VENTA: <strong>{{ !$invoice->is_credit?'CONTADO':'CREDITO' }}</strong></p>

        @if(isset($billing) && $account->show_billing_in_invoice)
            <p>CAI:{{$billing->cai}}</p>
            <p>FECHA LIMITE:{{$billing->limit_date}}</p>
            <p>RANGO AUTORIZADO:{{$billing->from_invoice}} al {{$billing->to_invoice}}</p>
        @endif
        <br>
        <p>CLIENTE: {{$client->company_name ? $client->company_name : $client->name}}</p>

        @if($client->vat_number)
            <p>RTN:{{$client->vat_number}}</p>
        @endif
        @if($client->points > 0)
            <div class="row">
                <p>Puntos anteriores: {{$client->points - $invoice->points}}</p>
                <p>Puntos de hoy: {{$invoice->points}}</p>

                <p>Puntos acumulados: {{$client->points}}</p>
                <p>Vencimiento de puntos: {{date('Y-m-d', strtotime($invoice->invoice_date." + 3 months"))}}</p>
            </div>
        @endif

        @if(!$invoice->is_credit)
            @if( isset($client->vouchers_discount) && $client->vouchers_discount > 0)
                <div class="row">
                    <br>
                    <p><strong>Vale de descuento </strong></p>
                    <p>Compras KMS hasta hoy: {{$client->amount_vouchers_kms}}</p>
                    <p>Descuento: -{{$client->percentage_vouchers}}%</p>
                    <p><strong>Total vale: </strong>{{$client->vouchers_discount}}</p>
                    <p>Vencimiento de vale: el dia 15 y el ultimo dia de cada mes.</p>
                    <p>Lista de descuento</p>
                    <p>de 10.000 a 24.999  -3 %</p>
                    <p>de 25.000 a 49.999  -5 %</p>
                    <p>mas de 50.000 -10 %</p>
                    <p><strong>PROMOCIÓN POR TIEMPO LIMITADO </strong></p>
                    <br>
                </div>
            @elseif(isset($client->amount_vouchers_kms) && $client->amount_vouchers_kms < 10000)
                <div class="row">
                    <br>
                    <p><strong>Vale de descuento por compra de productos KMS</strong></p>
                    <p>Compras KMS hasta hoy: {{$client->amount_vouchers_kms}}</p>
                    <p>Vencimiento de vale: el dia 15 y el ultimo dia de cada mes.</p>
                    <p>Lista de descuento</p>
                    <p>de 10.000 a 24.999  -3 %</p>
                    <p>de 25.000 a 49.999  -5 %</p>
                    <p>mas de 50.000 -10 %</p>
                    <p><strong>PROMOCIÓN POR TIEMPO LIMITADO </strong></p>
                    <br>
                </div>
            @endif
        @endif

        @if($employee)
            <p>Vendedor: {{$employee->first_name}} {{$employee->last_name}}</p>
        @endif
        @if($auxiliary)
            <p>Auxiliar SAC: {{$auxiliary->first_name}} {{$auxiliary->last_name}}</p>
        @endif
        <table style="margin: 0 auto">
            <thead>

            <tr>
                <th>Codigo</th>
                <th>Producto</th>
                <th>Cantidad</th>
                <th>Precio normal</th>
                <th style="font-weight:bold">Descuentos y Rebajas</th>
                <th>Precio unitario</th>
                <th>Total</th>
            </tr>
            </thead>
            <tbody>
            @foreach($invoice->items as $item)
                <tr>
                    <td>{{$item->product_key}}</td>
                    <td>{{$item->notes}}</td>
                    <td>{{$item->qty}}</td>
                    <td>{{number_format($item->product->price,2,'.','')}}</td>
                    <td style="font-weight:bold">{{number_format(($item->product->price - $item->cost) * $item->qty,2,'.','')}}</td>
                    <td>{{number_format($item->cost,2,'.','')}}</td>
                    <td>{{number_format((($item->cost) * $item->qty), 2, '.', ',')}}</td>
                </tr>
            @endforeach

            </tbody>
            <tfoot>
            <tr>
                <td colspan="4">No Orden Compra Exenta:</td>
                <td colspan="2">Subtotal Gravado 15%</td>
                @if($invoice->tax_rate1 > 0)
                    <td>{{number_format(($invoice->amount/(1 + floatval($invoice->tax_rate1/100))) , 2, '.', ',')}}</td>
                @else
                    <td>0.0</td>
                @endif
            </tr>
            <tr>
                <td colspan="4"></td>
                <td colspan="2">Subtotal Exento</td>
                @if($invoice->tax_rate1 == 0)
                    <td>{{$invoice->amount}}</td>
                @else
                    <td>0.00</td>
                @endif
            </tr>
            <tr>
                <td colspan="4">No Constancia de Registro Exonerado:</td>
                <td colspan="2">Subtotal Exonerado</td>
                @if($invoice->tax_rate1 == -1)
                    <td>{{$invoice->amount}}</td>
                @else
                    <td>0.00</td>
                @endif
            </tr>
            <tr>
                <td colspan="4"></td>
                <td colspan="2">Descuentos y Rebajas</td>
                <td>{{ number_format($invoice->discount, 2, '.', ',') }}</td>
            </tr>
            <tr>
                <td colspan="4">No de Registro S.A.G:</td>
                <td colspan="2">ISV 15%</td>
                @if($invoice->tax_rate1 > 0)
                    <td>{{ number_format((($invoice->amount/(1 + ($invoice->tax_rate1/100))) * 0.15), 2, '.', ',') }}</td>
                @else
                    <td>0.00</td>
                @endif
            </tr>
            <tr>
                <td colspan="4"></td>
                <td colspan="2"><strong style="font-size: 12px">Total</strong></td>
                <td><strong style="font-size: 12px">{{$invoice->amount}}</strong></td>
            </tr>
            @if($invoice->total_refunded > 0)
                <tr>
                    <td colspan="4"></td>
                    <td colspan="2">Reembolsado</td>
                    <td>{{number_format($invoice->total_refunded, 2, '.', ',')}}</td>
                </tr>

                <tr>
                    <td colspan="4"></td>
                    <td colspan="2">Total Neto</td>
                    <td>{{number_format($invoice->amount - $invoice->total_refunded, 2, '.', ',')}}</td>
                </tr>
            @endif
            </tfoot>
        </table>
        <p  style="border:none; padding: .2em; font-weight: bold; text-transform: uppercase">@if($invoice->items_discount > 0) En esta factura te ahorraste L. {{$invoice->items_discount}}  @endif</p>
        <p>No se hacen devoluciones de efectivo, solo  cambio de  productos.</p>

        <p>Partes electricas no tienen garantia.</p>

        @if($entityType != ENTITY_INVOICE)
            <h4><strong style="font-size: 12px; text-transform: uppercase;">Este no es un documento oficial no valido para garantias, reclamos ni ninguna otra gestion. Favor solicite su factura.</strong></h4>
        @endif

{{--        <img src="{{public_path() . '/assets/img/find_us.png'}}" style="display: block; margin: 0 auto; width: 200px;" />--}}
{{--        <img src="{{base_path(). '/public/assets/img/find_us.png'}}" style="display: block; margin: 0 auto; width: 200px;" />--}}
{{--        <img src="{{ storage_path('app/public/img/find_us.png') }}" style="display: block; margin: 0 auto; width: 200px;" />--}}
    </div>
    <div style="page-break-after: always;"></div>
@endforeach
</body>
</html>
