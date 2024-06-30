@extends('adminlte::page')

@section('css')

@stop

@section('content_header')
    <h1>
        Asiento Diario
    </h1>
@stop

@section('content')
    <div class="container">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
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
                        @foreach($items as $item)
                            <tr>
                                <td>{{$item->entry->id}}</td>
                                <td>{{$item->created_at}}</td>
                                <td>{{$item->catalogueItem->classificationNumber}}</td>
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
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
@stop
