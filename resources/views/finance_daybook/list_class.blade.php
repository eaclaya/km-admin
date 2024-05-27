@extends('header')

@section('head')
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
				<div>
					<h2 style="text-align: center;">Clasificación de Catalogo Financiero</h2>
				</div>
			</div>
		</div>
		<br>
		{{-- <form method="POST" action="{{route('evaluationprocess.update', $evaluationProcess->id)}}" > --}}
			{{-- {{ method_field('PUT') }}
			{{ csrf_field() }} --}}
			<div class="row">
				<table style="margin: 0 auto;">
					<thead>
						<tr>
							<th>Id</th>
							<th>Nombre</th>
							<th>Sort</th>
							<th>Items_qty</th>
							<th>Color</th>
							<th>Accion - 
								<a class="btn btn-success btn-sm" data-toggle="modal" data-target="#myModalCreate">
									<i class="fa fa-plus-square-o" aria-hidden="true"></i> Agregar
								</a>
							</th>
						</tr>
					</thead>
					<tbody>
						@foreach ($items as $item)
							@include('finance_catalogue.partials.rows_class',['item' => $item, 'models' => $models])
						@endforeach
					</tbody>
				</table>
			</div>
		{{-- </form> --}}
	</div>
	<div id="myModalCreate" class="modal fade" role="dialog">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title">Crear</h4>
				</div>
				<div class="modal-body">
					<form action="" method="post" id="form_create">
						<div class="form-group">
							<label for="name_create">Nombre de la Clase</label>
							<input id='name_create' name='name' class="form-control" type="text" required>
						</div>
						<div class="form-group">
							<label for="sort_create">Posicion</label>
							<input  class="form-control" type="number" name="sort" id="sort_create" required>
						</div>
						<div class="form-group">
							<label for="items_qty_create">Cantidad de Items</label>
							<input  class="form-control" type="number" name="items_qty" id="items_qty_create" required>
						</div>
						<div class="form-group">
							<label for="color_create">Color</label>
							<input id='color_create' name='color' class="form-control" type="text" required>
						</div>
					</form>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				</div>
			</div>
		</div>
	</div>

	<script>
		$(document).ready(function(){
			$('[data-toggle="popover"]').popover();
		});

		async function showEdit(data_string){
			data_string = data_string.split('*-*');
			const item_id = data_string[0];
			
			const finance_account_name = data_string[1];
			const finance_catalogue_classification_sort = data_string[2];
			const sort = data_string[3];
			const model = data_string[4];
			const model_id = data_string[5];
			const limitSort = data_string[6];

			url = '{{url()->current()}}' + '/' + item_id + '/update';
			$('#form_update').attr('action', url);

			$('#finance_account_name').val(finance_account_name);
			$('#finance_catalogue_classification_sort').val(finance_catalogue_classification_sort);
			$('#sort').val(sort);
			$('#sort').attr('max', limitSort);

			if(model){
				const params = {
					'model' : model,
					'model_id' : (model_id && model_id > 0) ? model_id : null,
				};
				let responceAjax = await getModels(params);
				console.dir(responceAjax);
				$('#model_id').empty();
				$("#model_id").select2({
					data: responceAjax,
					width: 'resolve'
				}).trigger('change');

				$('#model').val(model);

				if(model_id && model_id > 0){
					$('#model_id').val(model_id);
				}
			}else{
				$('#model').val('');
				$('#model_id').empty();
				$("#model_id").select2({
					data: [],
					width: 'resolve'
				}).trigger('change');
			}

			$("#myModal").modal();
			return;
		}
		
		async function showCreate(data_string){
			data_string = data_string.split('*-*');
			const item_id = data_string[0];

			const finance_catalogue_classification_sort = data_string[1];
			const sort = data_string[2];

			if(finance_catalogue_classification_sort > totalClasifications){
				alert('No se puede agregar cuentas debajo de esta Clasificación ya que no existen Clasificaciones sucesivas');
				return;
			}

			url = '{{url()->current()}}' + '/' + item_id + '/create';
			$('#form_create').attr('action', url);

			$('#finance_catalogue_classification_sort_create').val(finance_catalogue_classification_sort);

			$("#finance_catalogue_classification_sort_create option:not(:selected)").attr("disabled", "disabled");

			$('#sort_create').val(sort);

			$("#myModalCreate").modal();
			return;
		}

		async function getModels(params) {
			let respuesta = await $.ajax({
				type: 'GET',
				url: '{!! route('finance_catalogue.get_models') !!}',
				data: params,
			});
			if (respuesta.msg == 'erro' || respuesta.response == 'reset_url') {
				alert('A ocurrido un error');
			}
			return respuesta;
		}

		async function changeModel(element,type){
			const model = element.value;
			const params = {
				'model' : model,
				'model_id' : null
			};
			let responceAjax = await getModels(params);
			console.dir(responceAjax);
			if(type == 'edit'){
				$('#model_id').empty();
				$('#model_id').select2({
					data: responceAjax,
					width: 'resolve'
				}).trigger('change');
			}else{
				$('#model_id_create').empty();
				$('#model_id_create').select2({
					data: responceAjax,
					width: 'resolve'
				}).trigger('change');
			}
		}
	</script>

@stop
