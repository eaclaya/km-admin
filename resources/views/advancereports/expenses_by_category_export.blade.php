<table class="table">
    <thead>
        <tr>
            <td>Fecha</td>
            <td>Tienda</td>
            <td>Categoria</td>
            <td>Subcategoria</td>
            <td>Monto</td>
        </tr>
    </thead>
    <tbody>
        @foreach($result as $item)
        <tr>
            <td>{{$item['expense_date']}}</td>
            <td>{{$item['account_name']}}</td>
            <td>{{$item['category_name']}}</td>
            <td>{{$item['subcategory_name']}}</td>
            <td>{{$item['amount']}}</td>
        </tr>
        @endforeach
    </tbody>
</table>
