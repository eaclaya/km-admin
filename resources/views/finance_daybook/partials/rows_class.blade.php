<tr >
    <form action="{{route('finance_catalogue.set_classifications')}}" method="post">
        <td>
            <input type="hidden" name="id" value="{{$item->id}}">
            {{$item->id}}
        </td>
        <td>
            <input name='name' type="text" value="{{$item->name}}">
        </td>
        <td>
            <input name='sort' type="number" value="{{$item->sort}}">
        </td>
        <td>
            <input name='items_qty' type="number" value="{{$item->items_qty}}">
        </td>
        <td>
            <input name='color' type="text" value="{{$item->color}}">
            <div id="changeColor_{{$item->id}}" style="display:inline-block; width: 20px; height: 20px; background-color: {{$item->color}}"></div>
        </td>
        <td>
            <button class="btn btn-success btn-sm" type="submit">
                Guardar
            </button>
        </td>
    </form>
</tr>
@if(isset($item->subItems) && count($item->subItems) > 0)
    @foreach ($item->subItems as $subItem)
        @include('finance_catalogue.partials.rows',['item' => $item, 'models' => $models])
    @endforeach
@endif