@extends("adminlte::page")

@section("title", "index")

@section("content_header")
    <h1>Clonado de Modelos</h1>
@stop

@section("content")
    <div class="container">
        @foreach(array_chunk($types, 3, true) as $chunk)
            <div class="row">
                @foreach($chunk as $key => $type)
                    <div class="col-md-4 text-center">
                        <div class="card">
                            <div class="card-body">
                                <a class="btn btn-success" href="{{route('clone_models.list',['model'=> $key])}}">{{$type}}</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endforeach
    </div>
@stop

@section("css")
    {{-- Add here extra stylesheets --}}
@stop

@section("js")
    {{-- Add here extra javascript --}}
@stop
