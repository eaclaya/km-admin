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

                    @if (isset($result[0]->ca_name))
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
                </tr>
            </thead>
            <tbody>
                @foreach ($result as $item)
                    <tr>
                        <td>{{ $item->id }}</td>
                        <td>{{ $item->route_name??'' }}</td>
                        <td>{{ $item->account_name }}</td>
                        <td>{{ $item->number }}</td>
                        <td>{{ $item->payment_date }}</td>
                        <td>{{ substr($item->fat_name ?? '', 0, 20) }}</td>
                        <td>{{ $item->user_name . ' ' . $item->user_name2 }}</td>

                        @if (isset($result[0]->ca_name))
                            <td>{{ $item->ca_name ?? '' }}</td>
                            <td>{{ $item->description ?? '' }}</td>
                        @else
                            <td>
                                @if (isset($item->payment_status_id))
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
                            <td>{{ $item->discount_percent ?? '' }}</td>
                            <td>{{ $item->discount_points ?? '' }}</td>
                            <td>{{ $item->discount_vouchers ?? '' }}</td>
                            <td>{{ $item->discount ?? '' }}</td>
                            <td>{{ $item->balance ?? '' }}</td>
                        @endif
                        <td>{{ $item->amount ?? '' }}</td>
                        <td>{{ $item->cash_count_id }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
