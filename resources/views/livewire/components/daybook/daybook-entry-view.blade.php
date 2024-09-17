<div x-data="{ open: false }">
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Pda</th>
                <th>Fecha</th>
                <th>Codigo</th>
                <th>Descripcion</th>
                <th>Parcial</th>
                <th>Debe</th>
                <th>Haber</th>
            </tr>
        </thead>
        <tbody>
        @if(isset($primaries) && count($primaries) > 0)
            @foreach($primaries as $item)
                <tr>
                    <td>{{$item->entry->id}}</td>
                    <td>{{$item->created_at}}</td>
                    <td>{{isset($item->catalogueItem) ? $item->catalogueItem->number : 'error en carga de catalogo'}}</td>
                    <td>{{$item->description}}</td>
                    <td >
                        @if($item->partial > 0)
                            <strong>
                                {{$item->partial}}
                            </strong>
                        @else
                            {{$item->partial}}
                        @endif
                    </td>
                    <td >
                        @if($item->debit > 0)
                            <strong>
                                {{$item->debit}}
                            </strong>
                        @else
                            {{$item->debit}}
                        @endif
                    </td>
                    <td >
                        @if($item->havings > 0)
                            <strong>
                                {{$item->havings}}
                            </strong>
                        @else
                            {{$item->havings}}
                        @endif
                    </td>
                </tr>
            @endforeach
        @endif
        </tbody>
    </table>
    <button class="btn btn-sm btn-outline-primary" x-on:click="open = !open">Ver mas</button>

    <hr>

    <table x-show="open" class="table table-bordered table-striped">
        <thead>
        <tr>
            <th>Pda</th>
            <th>Fecha</th>
            <th>Codigo</th>
            <th>Descripcion</th>
            <th>Parcial</th>
            <th>Debe</th>
            <th>Haber</th>
        </tr>
        </thead>
        <tbody>
        @if(isset($secondaries) && count($secondaries) > 0)
            @foreach($secondaries as $item)
                <tr>
                    <td>{{$item->entry->id}}</td>
                    <td>{{$item->created_at}}</td>
                    <td>{{isset($item->catalogueItem) ? $item->catalogueItem->number : 'error en carga de catalogo'}}</td>
                    <td>{{$item->description}}</td>
                    <td >
                        @if($item->partial > 0)
                            <strong>
                                {{$item->partial}}
                            </strong>
                        @else
                            {{$item->partial}}
                        @endif
                    </td>
                    <td >
                        @if($item->debit > 0)
                            <strong>
                                {{$item->debit}}
                            </strong>
                        @else
                            {{$item->debit}}
                        @endif
                    </td>
                    <td >
                        @if($item->havings > 0)
                            <strong>
                                {{$item->havings}}
                            </strong>
                        @else
                            {{$item->havings}}
                        @endif
                    </td>
                </tr>
            @endforeach
        @endif
        </tbody>
    </table>
</div>

