@extends('adminlte::page')

@section('title', 'Reporte')

@section('content_header')
    <h1>Reportes Avanzados</h1>
@stop



@section('content')
	<div class="row">
		<div class="col-md-12">
			<form class="filter-form" method="POST" action="{{route('advancereports.packing_to_transfer')}}">
              @csrf
				 <div class="col-md-4">
                        <p>Fecha inicio</p>
                        <input type="date" class="form-control" name="from_date" />
                </div>
                <div class="col-md-4">
                        <p>Fecha fin</p>
                        <input type="date" class="form-control" name="to_date" />
				</div>
				
                <div class="col-md-4">
                        <p>&nbsp;</p>
                        <select  name="export" class="select-group control-form form-control">
                                <option value="1">Exportar</option>
                        </select>
                        <button type="submit" class="btn btn-primary btn-block">CONTINUAR</button>
                </div>
			</form>
		</div>
	</div>
@stop
