@extends('adminlte::page')

@section('title', 'Reporte')

@section('content_header')
    <h1>Reportes Avanzados</h1>
@stop



@section('content')
  <hr>
  <div class="row">
    <div class="col-md-12">
      <form class="filter-form" method="POST" action="{{route('advancereports.related_product_sales')}}">
              @csrf
         <div class="col-md-3">
              <p>Fecha Inicial:</p>
              <input type="date" name="from_date" class="form-control" />
        </div>
        <div class="col-md-3">
        	<p>Fecha Final:</p>
              <input type="date" name="to_date" class="form-control" />
	</div>
	 <div class="col-md-2">
                <p>&nbsp;</p>
	      <select id="account_id" name="account_id" class="select-group control-form form-control" style="display: block; width: 100%;">
			<option value="0">Todas las tiendas</option>
			@foreach($accounts as $account)
		      <option value="{{$account->id}}">{{$account->name}}</option>
			@endforeach
              </select>
          </div>
	  <div class="col-md-2">
		<p>&nbsp;</p>
              <select  name="export" class="select-group control-form form-control" style="display: block; width: 100%;">
                      <option value="1">Exportar</option>
              </select>
          </div>
	  <div class="col-md-2">
		<p>&nbsp;</p>
              <button type="submit" class="btn btn-primary btn-block">CONTINUAR</button>
          </div>
      </form>
    </div>
  </div>

  <script>
    $("#account_id").chosen({
        disable_search_threshold: 10,
        no_results_text: "Oops, nothing found!",
        width: "100%"
    });
  </script>

@stop
