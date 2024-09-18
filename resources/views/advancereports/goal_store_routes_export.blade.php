<table class="table">
    <thead>
        <tr>
            <td>Tienda</td>
            <td>Venta Repuesto</td>
            <td>Devoluciones (Todas)</td>
            <td>Venta Lubricante Mayorista</td>
            <td>Venta Lubricante Detalle</td>
            <td>Venta Total</td>
            <td>Meta</td>
            <td>% Meta</td>
            <td>DIARIO</td>
            <td>PONDERACION EFECTIVO</td>
            <td>PONDERACION %</td>
            <td>META PARA HOY</td>
            <td>DEBERIA IR</td>
            <td>DEFECIT %</td>
            <td>DEBERIA LLEVAR VENDIDO</td>
            <td>DEFECIT EFECTIVO</td>
            <td>DEFICIT MAS META DIARIA</td>
            <td>Fecha Inicio</td>
            <td>Fecha Fin</td>

            <td>CTERA CDT GLOBAL</td>
            <td>CDT POR VENCER</td>
            <td>CDT VENCIDOS</td>
            <td>CDT POR FECHA</td>
            <td>CDT EN TRANSITO</td>
        </tr>
    </thead>
    <tbody>
        @foreach ($result as $item)
            <tr>
                <td>{{ $item->account }}</td>
                <td>{{ $item->sale_amount }}</td>
                <td>{{ $item->refunds }}</td>
                <td>{{ $item->oil_wholesaler }}</td>
                <td>{{ $item->total_oil }}</td>
                <td>{{ $item->total_sale }}</td>
                <td>{{ $item->goal }}</td>
                <td>{{ $item->total_goal }}</td>
                <td>{{ $item->goal_daily }}</td>
                <td>{{ $item->pond_cash }}</td>
                <td>{{ $item->pond_avg }}</td>
                <td>{{ $item->amount_daily }}</td>
                <td>{{ $item->goal_ideal }}</td>
                <td>{{ $item->deficit_avg }}</td>
                <td>{{ $item->amount_ideal }}</td>
                <td>{{ $item->deficit_cash }}</td>
                <td>{{ $item->deficit_plus_goal_daily }}</td>
                <td>{{ $item->from_date }}</td>
                <td>{{ $item->to_date }}</td>

                <td>{{ $item->total_balance }}</td>
                <td>{{ $item->total_not_venced }}</td>
                <td>{{ $item->total_venced }}</td>
                <td>{{ $item->total_monts }}</td>
                <td>{{ $item->total_in_transit_balance }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
