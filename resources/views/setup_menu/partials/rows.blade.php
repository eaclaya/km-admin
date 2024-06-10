<tr>
    <td>
        {{$item->id}}
        <input type="hidden" name="item[{{$item->id}}][id]" value="{{$item->id}}">
    </td>
    <td>
        <div class="input-group fixed-input-group">
            @if($item->supra_menu_id !== null && $item->supra_menu_id !== 0)
                <div class="input-group-prepend">
                    <span class="input-group-text">
                        <i class="fa fa-arrow-circle-up" aria-hidden="true"></i>
                    </span>
                </div>
            @endif
            <input type="number" name="item[{{$item->id}}][supra_menu_id]" id="item-supra_menu_id-{{$item->id}}" value="{{$item->supra_menu_id}}" class="form-control col">
        </div>
    </td>
    <td>
            <input type="text" name="item[{{$item->id}}][url]" id="item-url-{{$item->id}}" value="{{$item->url}}">
    </td>
    <td>
            <input type="text" name="item[{{$item->id}}][text]" id="item-text-{{$item->id}}" value="{{$item->text}}">
    </td>
    <td>
            <input type="text" name="item[{{$item->id}}][icon]" id="item-icon-{{$item->id}}" value="{{$item->icon}}">
    </td>
    <td>
            <input type="text" name="item[{{$item->id}}][can]" id="item-can-{{$item->id}}" value="{{$item->can}}">
    </td>
    <td>
            <input type="text" name="item[{{$item->id}}][label]" id="item-label-{{$item->id}}" value="{{$item->label}}">
    </td>
    <td>
            <input type="text" name="item[{{$item->id}}][label_color]" id="item-label_color-{{$item->id}}" value="{{$item->label_color}}">
    </td>
    <td>
        @if(is_null($item->url) || trim($item->url) == '' || trim($item->url) == '#')
            <a class="btn btn-success btn-sm" onclick="showCreate('{{$item->id}}')">
                <i class="fa fa-plus-circle" aria-hidden="true"></i>
            </a>
        @endif
        <a class="btn btn-danger btn-sm" href="{{route('setup_menu.destroy',['id' => $item->id])}}" onclick="return confirmDelete('{{$item->text}}')">
            <i class="fa fa-trash" aria-hidden="true"></i>
        </a>
    </td>
</tr>
@if(isset($item->subItems) && count($item->subItems) > 0)
    @foreach ($item->subItems as $subItem)
        @include('setup_menu.partials.rows',['item' => $subItem, 'ml' => $ml + 1])
    @endforeach
@endif
