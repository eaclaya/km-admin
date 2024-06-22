@extends('adminlte::page')

@section('css')
  <link rel="stylesheet" type="text/css" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.4/css/bootstrap-select.min.css">
   <style>
	table td, table th{
		border: 1px solid black !important;
		padding: 10px;
		max-width: fit-content;
	}
	.row-error {
		background: crimson;
		color: white;
	}
	.text-error {
		border: red;
    	border-style: double;
	}
   </style>
@stop

@section('content_header')
    <h1>
        Libro Diario
    </h1>
@stop

@section('content')
    @livewire('components.select2-component', $bodySelectAccount)
    <br>
    @livewire('components.select2-component', $bodySelectEmployee)
    <br>
    @livewire('components.select2-component', $bodySelectUsers)
@stop

@section('js')
@stop
