<table class="table">
        <thead>
                <tr>
                        <td>Proveedor</td>
                        {{-- <td>Costo</td> --}}
                        <td>Cantidad</td>
                        <td>Costo Total</td>
                </tr>
        </thead>
        <tbody>
                @foreach($result as $item)
                <tr>
                        <td>{{$item['vendor']}}</td>
                        {{-- <td>{{$item['cost']}}</td> --}}
                        <td>{{$item['qty']}}</td>
                        <td>{{$item['total']}}</td>
                </tr>
                @endforeach
        </tbody>
</table>
