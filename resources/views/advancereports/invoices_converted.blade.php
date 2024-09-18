@extends('adminlte::page')

@section('title', 'Reporte')

@section('content_header')
    <h1>Reportes Avanzados</h1>
@stop



@section('content')
 	<hr> 
	<div class="row">
		<div class="col-md-12">
			<form class="filter-form" method="POST" action="{{route('advancereports.invoices_converted')}}">
              @csrf
				 <div class="col-md-3">
					<p>Fecha inicio</p>
                                        <input type="date" class="form-control" name="from_date" />
                                </div>
				<div class="col-md-3">
					<p>Fecha find</p>
                                        <input type="date" class="form-control" name="to_date" />
				</div>
				<div class="col-md-3">
					 <p>&nbsp;</p>
                                        <select  name="export" class="select-group control-form form-control">
                                                <option value="1">Exportar</option>
                                        </select>
                                </div>
                                <div class="col-md-3">
                                        <p>&nbsp;</p>
                                        <button type="submit" class="btn btn-primary btn-block">CONTINUAR</button>
                                </div>
			</form>
		</div>
	</div>
	<hr>
    <script>
	$(document).ready( function () {
            $('table').DataTable({
                "order": [[ 0, "desc" ]]
	    });

        } );
    </script>
@stop


