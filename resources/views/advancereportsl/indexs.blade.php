@extends("adminlte::page")

@section("title", "index")

@section("content_header")
    <h1>Reportes Avanzados</h1>
@stop

@section("content")
    <div class="container">
        <div class="row">
            <div class="col-md-4">
                <div class="card">
                        <a href="{{route('advancereports.stock_in_stores') }}">
                                <div class="icon-wrapper"><i class="fa fa-book fa-fw fa-5x"></i></div>
                                <div class="title">Total Inventario general plusss</div>
                        </a>
                </div>
            </div>
        </div>
    </div>
@stop
@section("css")

<style>
    .card {
      box-shadow: 0px 0px 5px #ddd;
      padding: 20px;
      margin-bottom: 20px;
    }
    .card a {
      text-decoration: none;
    }
    .card .icon-wrapper {
      text-align: center;
    }
    .card .title {
      margin-top: 10px;
      text-transform: uppercase;
      text-align: center;
    }
  </style>
@stop