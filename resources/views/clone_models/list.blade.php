@extends("adminlte::page")

@section("title", "index")

@section("content_header")
    <h1>Listado</h1>
@stop

@section("content")
    <div class="container">
        @if (isset($cloningControl) && count($cloningControl) > 0)
            <table class="table table-bordered table-hover table-striped">
                <thead class="text-center" style="border: 5px solid black">
                    <tr>
                        <th class="text-center"><strong> account_id </strong></th>
                        <th class="text-center"><strong> from_date </strong></th>
                        <th class="text-center"><strong> to_date </strong></th>
                        <th class="text-center"><strong> is_completed </strong></th>
                        <th class="text-center"><strong> created_at </strong></th>
                        <th class="text-center"><strong> updated_at </strong></th>
                    </tr>
                </thead>
                <tbody>
                @foreach($cloningControl as $item)
                    <tr style="border: 3px solid black !important;">
                        <td>
                            <a href="{{route('clone_models.list',['model'=> $model, 'model_id'=> $item->account_id])}}">{{$item->account()->name}}</a>
                        </td>
                        <td>{{$item->from_date}}</td>
                        <td>{{$item->to_date}}</td>
                        <td>{{$item->is_completed ? 'Si' : 'No'}}</td>
                        <td>{{$item->created_at}}</td>
                        <td>{{$item->updated_at}}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            @if ($cloningControl->hasPages())
                <div class="text-center"><h2 class="text-center">Ver Mas</h2>
                    <div>{{ $cloningControl->links('pagination::bootstrap-5') }}</div>
                </div>
            @endif
        @else
            <h4>No Existe Informacion de este Proceso</h4>
        @endif
    </div>
@stop

@section("css")
    {{-- Add here extra stylesheets --}}
@stop

@section("js")
    {{-- Add here extra javascript --}}
@stop
