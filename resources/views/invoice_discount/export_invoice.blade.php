@extends("adminlte::page")

@section("title", "index")

@section("content_header")
    <h1>Exportar Facturas</h1>
@stop

@section("content")
    <div class="container">
        <hr>
        <div class="row">
            <p>
                Se a clonado dentro del sistema las facturas desde la fecha: {{$dateFromControl}} hasta la fecha: {{$dateToControl}}
            </p>
        </div>
        <div class="row">
            <div class="col-md-12">
                <form class="filter-form row" method="POST" action="{{route('invoice_discount.export_invoice')}}">
                    @csrf
                    <div class="col-md-3">
                        <p>Fecha inicio</p>
                        <input type="date" class="form-control" name="from_date" />
                    </div>
                    <div class="col-md-3">
                        <p>Fecha fin</p>
                        <input type="date" class="form-control" name="to_date" />
                    </div>
                    <div class="col-md-3">
                        <p>Tiendas</p>
                        <select id="store" name="store" class="control-form form-control" required>
                            <option value="all">Todas</option>
                            @foreach ($accounts as $store)
                                <option value="{{$store->id}}">{{$store->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <p>CAI</p>
                        <select  id="filter" name="filter" class="select-group control-form form-control">
                            <option value="all">todos</option>
                            <option value="1">Solo con CAI</option>
                            <option value="2">Solo sin CAI</option>
                        </select>
                        <button type="submit" class="btn btn-primary btn-block">CONTINUAR</button>
                    </div>
                </form>
            </div>
        </div>
        <hr>
        @if(isset($reportProcess))
            <div class="row">
                <div class="col-md-12">
                    <table class="table">
                        <thead>
                        <tr>
                            <td>Id</td>
                            <td>Archivo</td>
                            <td>Estatus</td>
                            <td>Porcentaje</td>
                            <td>Fecha Creacion</td>
                            <td>Fecha Finalización</td>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($reportProcess as $item)
                            <tr>
                                <td>
                                    {{$item->id}}
                                </td>
                                <td>
                                    @if ($item->status == 0)
                                        {{$item->file}}
                                    @else
                                        <a href="{{asset($item->file)}}">{{$item->file}}</a>
                                    @endif
                                </td>
                                <td>
                                    @if ($item->status == 0)
                                        En Proceso
                                        <br>
                                        <a href="{{route('reports.finish_report',['id' => $item->id])}}" class="btn btn-success btn-sm">Marcar Finalizado</a>
                                    @else
                                        Finalizado
                                    @endif
                                </td>
                                @php
                                    $item->count_rows = (is_null($item->count_rows) || $item->count_rows == 0) ? 0 : $item->count_rows;
                                    $item->rows = (is_null($item->rows) || $item->rows == 0) ? 1 : $item->rows;

                                    $porcentCompleting = ($item->count_rows * 100) / $item->rows;
                                    $porcentCompleting = round($porcentCompleting, 0);
                                    $porcentCompleting = ($porcentCompleting == 0 || $porcentCompleting == 1) ? 'Por Procesar el ' : ceil($porcentCompleting);
                                @endphp
                                <td>
                                    <strong>{{$porcentCompleting}}%</strong>
                                    @if (intval($porcentCompleting) < 100)
                                        <a href="{{url()->full()}}" class="btn btn-sm btn-primary">Recargar</a>
                                    @endif
                                </td>
                                <td>{{$item->created_at}}</td>
                                <td>{{$item->updated_at}}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
    <script src="//cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready( function () {
            $('table').DataTable({
                "order": [[ 2, "desc" ]]
            });
        } );
        $("#store").chosen({
            disable_search_threshold: 10,
            no_results_text: "Oops, nothing found!",
            width: "100%"
        });
    </script>
@stop

@section("css")
    {{-- Add here extra stylesheets --}}
@stop

@section("js")
    {{-- Add here extra javascript --}}
@stop
