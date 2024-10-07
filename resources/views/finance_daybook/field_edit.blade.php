@extends('adminlte::page')

@section('title', 'Reporte')

@section('content_header')
    <h1>Reportes Avanzados</h1>
@stop


  @parent
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
	input[type="number"] {
		width: 50px;
	}
	.select_sub_consept{
		width: 75% !important;
	}
   </style>
@stop

@section('content')
  	@parent

  	@if (Session::has('message'))
		<div class="alert alert-info">{{ Session::get('message') }}</div>
	@endif
	
	<div class="">
		<div class="row">
			<div class="col-sm-12">
				<div class="text-center">
					<h2 style="text-align: center;">Campos del Proceso de Evaluacion</h2>
					<a class="btn btn-primary btn-lg" data-toggle="modal" data-target="#myModal">
						Crear Nuevo Campo
					</a>
				</div>
			</div>
		</div>
		<br>
		<div class="row">
			<table class="table" style="margin: 0 auto;">
				<thead>
					<tr>
						<th>ID</th>
						<th>Campo</th>
						<th>Porcentaje Limite</th>
						<th>Campo Superior</th>
						<th>Area Predeterminada</th>
						<th>Tipo de Evaluacion</th>
						<th>Accion</th>
					</tr>
				</thead>
				<tbody>
					@foreach ($fields as $field)
						<tr >
							<td style="max-width: 50px" class="bg-success">
								{{$field->id}}
								<input type="hidden" name="fields[{{$field->id}}][id]" id="fields_{{$field->id}}_id" value="{{$field->id}}">
							</td>
							<td style="max-width: 600px" class="bg-success">
								<textarea name="fields[{{$field->id}}][concept]" id="fields_{{$field->id}}_concept" cols="50" rows="4">{{$field->concept}}</textarea>
							</td>
							<td style="max-width: 50px" class="bg-success">
								<input type="number" name="fields[{{$field->id}}][percentage_limit]" id="fields_{{$field->id}}_percentage_limit" value="{{$field->percentage_limit}}"> %
							</td>
							<td style="max-width: 300px" class="bg-success">
								<div class="" style="margin-bottom: 5px">
									@if (isset($field->subFields) && count($field->subFields) > 0)
										No Aplica ya que posee {{count($field->subFields)}} sub conceptos. <i class="fa fa-arrow-down" aria-hidden="true"></i>
									@else
										<select name="fields[{{$field->id}}][sub_concept_id]" style="color: black;" id="fields_{{$field->id}}_sub_concept_id" class="select_sub_consept">
											<option ></option>
											@foreach ($fields as $superField)
												@php
													if ($superField->id == $field->id) {
														continue;
													}
												@endphp
												<option value="{{$superField->id}}" @if($field->sub_concept_id == $superField->id) selected @endif >{{$superField->concept}}</option>
											@endforeach
										</select>
									@endif
								</div>
							</td>
							<td style="max-width: 500px" class="bg-success">
								<div class="" style="margin-bottom: 5px">
									<select name="fields[{{$field->id}}][evaluator_area_id]" style="color: black;" id="fields_{{$field->id}}_evaluator_area_id" >
										<option ></option>
										@foreach ($areas as $area)
										<option value="{{$area->id}}" @if($field->evaluator_area_id == $area->id) selected @endif >{{$area->name}}</option>
										@endforeach
									</select>
								</div>
							</td>
							<td style="max-width: 500px" class="bg-success">
								<div class="" style="margin-bottom: 5px">
									<select name="fields[{{$field->id}}][evaluation_process_type]" style="color: black;" id="fields_{{$field->id}}_evaluation_process_type" >
										<option ></option>
										@foreach ($processTypes as $typeId => $type)
										<option value="{{$typeId}}" @if($field->evaluation_process_type == $typeId) selected @endif >{{trans("texts.$type")}}</option>
										@endforeach
									</select>
								</div>
							</td>
							<td style="max-width: 500px" class="bg-success">
								<a class="btn btn-success btn-sm btn-action" onclick="updateField('{{$field->id}}','update')">Actualizar</a>
								<a class="btn btn-danger btn-sm btn-action" onclick="updateField('{{$field->id}}','deleted')">Eliminar</a>
							</td>
						</tr>
						@if (isset($field->subFields))
							@foreach ($field->subFields as $subField)
								<tr>
									<td style="max-width: 100px">
										<i class="fa fa-arrow-right" aria-hidden="true"></i>
										{{$subField->id}}
										<input type="hidden" name="fields[{{$subField->id}}][id]" id="fields_{{$subField->id}}_id" value="{{$subField->id}}">
									</td>
									<td style="max-width: 600px">
										<textarea name="fields[{{$subField->id}}][concept]" id="fields_{{$subField->id}}_concept" cols="50" rows="4">{{$subField->concept}}</textarea>
									</td>
									<td style="max-width: 50px">
										<input type="number" name="fields[{{$subField->id}}][percentage_limit]" id="fields_{{$subField->id}}_percentage_limit"  value="{{$subField->percentage_limit}}"> %
									</td>
									<td style="max-width: 500px">
										<div class="" style="margin-bottom: 5px">
											<select name="fields[{{$subField->id}}][sub_concept_id]" style="color: black;" id="fields_{{$subField->id}}_sub_concept_id" class="select_sub_consept">
												<option ></option>
												@foreach ($fields as $superField)
												<option value="{{$superField->id}}" @if($subField->sub_concept_id == $superField->id) selected @endif >{{$superField->concept}}</option>
												@endforeach
											</select>
										</div>
									</td>
									<td style="max-width: 500px">
										<div class="" style="margin-bottom: 5px">
											<select name="fields[{{$subField->id}}][evaluator_area_id]" style="color: black;" id="fields_{{$subField->id}}_evaluator_area_id" >
												<option ></option>
												@foreach ($areas as $area)
												<option value="{{$area->id}}" @if($subField->evaluator_area_id == $area->id) selected @endif >{{$area->name}}</option>
												@endforeach
											</select>
										</div>
									</td>
									<td style="max-width: 500px">
										<div class="" style="margin-bottom: 5px">
											<select name="fields[{{$subField->id}}][evaluation_process_type]" style="color: black;" id="fields_{{$subField->id}}_evaluation_process_type" >
												<option ></option>
												@foreach ($processTypes as $typeId => $type)
												<option value="{{$typeId}}" @if($subField->evaluation_process_type == $typeId) selected @endif >{{trans("texts.$type")}}</option>
												@endforeach
											</select>
										</div>
									</td>
									<td style="max-width: 500px">
										<a class="btn btn-success btn-sm btn-action" onclick="updateField('{{$subField->id}}','update')">Actualizar</a>
										<a class="btn btn-danger btn-sm btn-action" onclick="updateField('{{$subField->id}}','deleted')">Eliminar</a>
									</td>
								</tr>
							@endforeach
						@endif
					@endforeach
				</tbody>
			</table>
		</div>
	</div>
	<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
		<div class="modal-dialog modal-lg" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title" id="myModalLabel">Crear Campo</h4>
				</div>
				<div class="modal-body">
					<form method="POST" action="{{route('evaluationprocess.edit.field')}}" enctype="multipart/form-data">
						{{ csrf_field() }}
						<div class="row text-center">
							<div class="col-md-6">
								<label for="concept">Campo</label><br>
								<textarea name="concept" id="concept" cols="50" rows="4" required></textarea>
								<input type="hidden" name="type" value="created">
							</div>
							<div class="col-md-6">
								<label for="evaluator_area_id">Area Predeterminada</label><br>
								<select name="evaluator_area_id" style="color: black;" id="evaluator_area_id" required>
									<option ></option>
									@foreach ($areas as $area)
									<option value="{{$area->id}}" >{{$area->name}}</option>
									@endforeach
								</select>
							</div>
							<div class="col-md-6">
								<label for="evaluation_process_type">Tipo de Evaluación</label><br>
								<select name="evaluation_process_type" style="color: black;" id="evaluation_process_type" required>
									<option ></option>
									@foreach ($processTypes as $typeId => $type)
										<option value="{{$typeId}}" >{{trans("texts.$type")}}</option>
									@endforeach
								</select>
							</div>
							<div class="col-md-6">
								<label for="sub_concept_id">Campo Superior</label><br>
								<select name="sub_concept_id" style="color: black;" id="sub_concept_id" class="select_sub_consept">
									<option ></option>
									@foreach ($fields as $superField)
										<option value="{{$superField->id}}" >{{$superField->concept}}</option>
									@endforeach
								</select>
							</div>
							<div class="col-md-6">
								<label for="percentage_limit">Tipo de Evaluación</label><br>
								<input type="number" name="percentage_limit" id="percentage_limit" > %
							</div>
						</div>
						<button type="submit" class="btn btn-primary" style="margin: 50px auto;display: block">Guardar</button>
					</form>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				</div>
			</div>
		</div>
	</div>
	
	<script src="//cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>

   	<script>
	$(document).ready(function(){
		$('.btn-action').on('click', function(event){
			$(this).attr('disabled', 'disabled');
		});
		$('table.table').DataTable({
			"order": [],
		});
	});

	async function updateField(current_id,type) {
		const wait = ms => new Promise((r, j) => setTimeout(r, ms));
		const id = $('#fields_'+current_id+'_id').val();
		const concept = $('#fields_'+current_id+'_concept').val();
		const evaluator_area_id = $('#fields_'+current_id+'_evaluator_area_id').val();
		const percentage_limit = $('#fields_'+current_id+'_percentage_limit').val();
		const sub_concept_id = $('#fields_'+current_id+'_sub_concept_id').val() ? $('#fields_'+current_id+'_sub_concept_id').val() : null;
		const evaluation_process_type = $('#fields_'+current_id+'_evaluation_process_type').val();

		const params = {
			'concept': concept,
			'id': id,
			'evaluator_area_id': evaluator_area_id,
			'percentage_limit': percentage_limit,
			'sub_concept_id': sub_concept_id,
			'type': type,
			'conection': 'ajax',
		};

		await wait(1000);
		let responceAjax = await myAjaxFunction(params);

		if(responceAjax && responceAjax.response == 'ok'){
			location.reload();
		}else{
			console.dir('aca la respuesta no fue ok');
			alert('A Ocurrido un Error, recargue la pagina y vuelva a intentar');
			return false;
		}
		
		console.dir('aca finalizo el ciclo for');
	}

	async function myAjaxFunction(params) {
		let respuesta = await $.ajax({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			type: 'POST',
			url: '{!! route('evaluationprocess.edit.field') !!}',
			dataType: 'json',
			data: params,
		});
		console.log(respuesta);
		return respuesta;
	}
	
	
   </script>

@stop
