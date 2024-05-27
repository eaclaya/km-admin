@php
    $color = $clasifications[$item->finance_catalogue_classification_sort]->color;
    $oldersClasificationIds[$item->finance_catalogue_classification_sort] = $item->sort;
    $newColDNone = '';
    foreach ($clasifications as $clasification){
        if($clasification->id < $item->finance_catalogue_classification_sort){
            $newColDNone .= '-'.str_pad($oldersClasificationIds[$clasification->id], $clasification->items_qty, "0", STR_PAD_LEFT);
        }elseif($clasification->id == $item->finance_catalogue_classification_sort){
            $newColDNone .= '-'.str_pad($item->sort, $clasification->items_qty, "0", STR_PAD_LEFT);
        }
    }
@endphp
<tr style="background-color:{{$color}};" class="{{$newColDNone}}">
    @foreach ($clasifications as $clasification)
        <td>
            @if($clasification->id < $item->finance_catalogue_classification_sort)
                {{str_pad($oldersClasificationIds[$clasification->id], $clasification->items_qty, "0", STR_PAD_LEFT)}}
            @elseif($clasification->id == $item->finance_catalogue_classification_sort)
                @if(isset($item->subItems) && count($item->subItems) > 0)
                    <i class="far fa-eye {{$newColDNone}}-hide" onclick="dNoneColumns('{{$newColDNone}}','hide')"></i>
                    <i class="far fa-eye-slash {{$newColDNone}}-show" style="display: none" onclick="dNoneColumns('{{$newColDNone}}','show')"></i>
                @endif
                {{str_pad($item->sort, $clasification->items_qty, "0", STR_PAD_LEFT)}}
            @else
                {{str_pad(0, $clasification->items_qty, "0", STR_PAD_LEFT)}}
            @endif
        </td>
    @endforeach
    <td>
        {{$clasifications[$item->finance_catalogue_classification_sort]->name}}
    </td>
    <td>
        {{$item->finance_account_name}}
        @if(isset($item->model) && isset($item->model_id))
            <i class="fa fa-anchor text-danger" aria-hidden="true" data-toggle="popover" title="Anclado a:" data-content="{{$models[$item->model]}} - {{$item->getModel()->name}}"></i>
        @endif
    </td>
    <td>
        <a class="btn btn-info btn-sm" onclick="showEdit('{{$item->id}}*-*{{$item->finance_account_name}}*-*{{$item->finance_catalogue_classification_sort}}*-*{{$item->sort}}*-*{{$item->model}}*-*{{$item->model_id}}*-*{{intval($limitClassifications)}}')">
            <i class="fa fa-pencil-square-o" aria-hidden="true"></i> Editar
        </a>
        <a class="btn btn-success btn-sm" onclick="showCreate('{{$item->id}}*-*{{intval($item->finance_catalogue_classification_sort) + 1}}*-*{{(isset($item->subItems) && count($item->subItems) > 0) ? count($item->subItems) + 1 : 1}}')">
            <i class="fa fa-plus-square-o" aria-hidden="true"></i> Agregar
        </a>
    </td>
</tr>
@if(isset($item->subItems) && count($item->subItems) > 0)
    @foreach ($item->subItems as $subItem)
        @include('finance_catalogue.partials.rows',['clasifications' => $clasifications, 'item' => $subItem, 'oldersClasificationIds' => $oldersClasificationIds, 'models' => $models, 'limitClassifications' => count($item->subItems)])
    @endforeach
@endif
