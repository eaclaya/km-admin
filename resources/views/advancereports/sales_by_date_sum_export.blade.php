<table class="table">
        <thead>
            <tr>
                <td style="border: 1px solid black">Tienda</td>
                @foreach ($result['columns']??[] as $code => $value)
                    <td colspan="4" style="text-align: center; border: 1px solid black">{{ $code }}</td>
                @endforeach
                <td></td>
                <td colspan="5" style="text-align: center; border: 1px solid black">Totales</td>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td></td>
                @foreach ($result['columns']??[] as $code => $values)
                    <td style="background: #eee">Repuestos</td>
                    <td style="background: #eee">Lubricantes Detalle</td>
                    <td style="background: #eee">Lubricantes Mayoreo</td>
                    <td style="background: #eee">Devoluciones</td>
                @endforeach
                <td></td>
                <td style="background: #eee">Total Repuestos</td>
                <td style="background: #eee">Total Lubri Detal</td>
                <td style="background: #eee">Total Lubri Mayor</td>
                <td style="background: #eee">Total Devoluciones</td>
                <td style="background: #eee">Total</td>
            </tr>
    
            <?php $total = 0; ?>
            @foreach ($result['values']??[] as $key => $items)
                <tr>
                    <?php 
                        $repu = 0;
                        $lubrid = 0;
                        $lubrim = 0;
                        $devo = 0;
                        ?>
                    <td>{{ $key }}</td>
                    @foreach ($items as $date => $item)
                        <td>{{ $item['total']??0 }}</td>
                        <td>{{ $item['oil'] - $item['oil_wholesaler'] }}</td>
                        <td>{{ $item['oil_wholesaler']??0 }}</td>
                        <td>{{ $item['total_refunded']??0 }}</td>
                        <?php 
                        $repu += $item['total']??0;
                        $lubrid += $item['oil'] - $item['oil_wholesaler'];
                        $lubrim += $item['oil_wholesaler']??0;
                        $devo += $item['total_refunded']??0;
                        ?>
                    @endforeach
                    <td></td>
                    <td>{{ $repu }}</td>
                    <td>{{ $lubrid }}</td>
                    <td>{{ $lubrim }}</td>
                    <td>{{ $devo }}</td>
                    <td>{{ $repu + $lubrid + $lubrim - $devo }}</td>
                    <?php $total += $repu + $lubrid + $lubrim - $devo; ?>
                </tr>
            @endforeach
    
            <tr>
                <td></td>
                <td>Venta Total - Devoluciones</td>
                <td>{{ $total }}</td>
            </tr>
        </tbody>
    </table>
    