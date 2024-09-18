@extends('adminlte::page')

@section('title', 'Reporte')

@section('content_header')
    <h1>Reportes Avanzados</h1>
@stop



@section('content')
  <hr>
  <div class="row">
    <div class="col-md-12">
      <form class="filter-form" method="POST" action="{{route('advancereports.sales_subcategory_by_month')}}">
              @csrf

        <div class="col-md-2">
            <p>Fecha inicio</p>
            <input type="date" class="form-control" name="from_date" />
        </div>
        <div class="col-md-2">
            <p>Fecha fin</p>
            <input type="date" class="form-control" name="to_date" />
        </div>

        <div class="col-md-2">
            <p>Mensual</p>
            <select  name="month_ago" class="select-group control-form form-control" style="display: block; width: 100%;">
                <option value="null">Escoger</option>
                <option value="0">Mes actual</option>
                <option value="1">Hace 1 mes</option>
                <option value="3">Hace 3 meses</option>
                <option value="6">Hace 6 meses</option>
                <option value="12">Hace 12 meses</option>
                <option value="24">Hace 24 meses</option>
            </select>
        </div>

        <div class="col-md-2">
          <p>Con Existencias Historicas Segun Tracking</p>
          <select  name="with_tracking" class="select-group control-form form-control" style="display: block; width: 100%;">
              <option value="0">Sin Existencias</option>
              <option value="1">Con Existencias</option>
          </select>
        </div>

        <div class="col-md-2">
            <p>Tienda</p>
            <select  name="store" class="select-group control-form form-control" style="display: block; width: 100%;">
                <option value="all">Todas</option>
                @foreach($stores as $store)
                    <option value="{{$store->id}}">{{$store->name}}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <p></p>
            <button type="submit" class="btn btn-primary">Exportar</button>
        </div>
      </form>
    </div>
  </div>
 
@stop
