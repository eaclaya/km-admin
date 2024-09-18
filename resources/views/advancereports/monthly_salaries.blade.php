@extends('adminlte::page')

@section('title', 'Reporte')

@section('content_header')
    <h1>Reportes Avanzados</h1>
@stop



@section('content')
  <hr>
  <div class="row">
    <div class="col-md-12">
      <form class="filter-form" method="POST" action="{{route('advancereports.monthly_salaries')}}">
              @csrf
         <div class="col-md-4">
              <select  name="month_ago" class="select-group control-form form-control" style="display: block; width: 100%;">
                      <option value="0">Mes actual</option>
                      <option value="1">Hace 1 mes</option>
                      <option value="3">Hace 3 meses</option>
                      <option value="6">Hace 6 meses</option>
              </select>
        </div>

          <div class="col-md-4">
              <select  name="export" class="select-group control-form form-control" style="display: block; width: 100%;">
                      <option value="1">Exportar</option>
              </select>
          </div>
          <div class="col-md-4">
              <button type="submit" class="btn btn-primary btn-block">CONTINUAR</button>
          </div>
      </form>
    </div>
  </div>

@stop
