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
        Catalogo Financiero -
        <a class="btn btn-success btn-sm" href="{{route('finance_catalogue.show_classifications')}}">Clasificaciones</a>
        <a class="btn btn-success btn-sm" href="{{route('finance_catalogue.export')}}">Exportar</a>
    </h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-center">
            <div class="btn-group text-center my-auto" role="group" aria-label="Basic example">
                @foreach ($itemsFilter as $item)
                    <a
                        class="btn btn-primary @if(isset($filter) && $item->id == $filter) disabled @endif"
                        href="{{url(request()->path()) . '?filter=' . $item->id}}"
                    >{{$item->finance_account_name}}</a>
                @endforeach
                <a
                    class="btn btn-success @if(isset($filter) && 'all' === $filter) disabled @endif"
                    href="{{url(request()->path()) . '?filter=all'}}"
                >Todos</a>
            </div>
        </div>
        <div class="card-body">
            {{-- <form method="POST" action="{{route('evaluationprocess.update', $evaluationProcess->id)}}" > --}}
            {{-- {{ method_field('PUT') }}
            {{ csrf_field() }} --}}
            <div class="row">
                <table style="margin: 0 auto;">
                    <thead>
                    <tr>
                        @php
                            $oldersClasificationIds = [];
                        @endphp
                        @foreach ($clasifications as $item)
                            <th>{{$item->name}}</th>
                            @php
                                $oldersClasificationIds[$item->id] = 0;
                            @endphp
                        @endforeach
                        <th>Clasificación</th>
                        <th>Nombre de la Cuenta</th>
                        <th>Accion -
                            <a class="btn btn-success btn-sm" onclick="showCreate('{{null}}*-*1*-*{{(isset($items) && count($items) > 0) ? count($items) + 1 : 1}}')">
                                <i class="fa fa-plus-square-o" aria-hidden="true"></i> Agregar
                            </a>
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($items as $item)
                        @include('finance_catalogue.partials.rows',['clasifications' => $clasifications, 'item' => $item, 'oldersClasificationIds' => $oldersClasificationIds, 'models' => $models, 'limitClassifications' => count($items)+1])
                    @endforeach
                    </tbody>
                </table>
            </div>
            {{-- </form> --}}
        </div>
    </div>

	<div id="myModal" class="modal fade" role="dialog">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title">Editar</h4>
				</div>
				<div class="modal-body">
					<form action="" method="post" id="form_update">
						<div class="form-group">
							<label for="finance_account_name">Nombre de la Cuenta</label>
							<input type="text" class="form-control" id="finance_account_name" name="finance_account_name" required>
						</div>
						{{-- <div class="form-group">
							<label for="finance_catalogue_classification_sort">Clasificación</label>
							<select class="form-control" id="finance_catalogue_classification_sort" name="finance_catalogue_classification_sort" required>
								@foreach ($clasifications as $clasification)
									<option value="{{$clasification->id}}">{{$clasification->name}}</option>
								@endforeach
							</select>
						</div> --}}
						<div class="form-group">
							<label for="sort">Posicion</label>
							<input type="number" name="sort" id="sort" min="0" max="">
						</div>
						<div class="form-group">
							<label for="model">Modelo (Tabla de la base de datos)</label>
							<select class="form-control" name="model" id="model" onchange="changeModel(this,'edit')" style="width: 50%">
								<option ></option>
								@foreach ($models as $model => $name)
									<option value="{{$model}}">{{$name}}</option>
								@endforeach
							</select>
						</div>
						<br>
						<div class="form-group">
							<label for="model">Identificador del Modelo</label>
							<select class="form-control" name="model_id" id="model_id" style="width: 50%">
								<option ></option>
							</select>
						</div>
					</form>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				</div>
			</div>
		</div>
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
							<label for="finance_account_name">Nombre de la Cuenta</label>
							<input type="text" class="form-control" id="finance_account_name_create" name="finance_account_name" required>
						</div>
						<div class="form-group">
							<label for="finance_catalogue_classification_sort_create">Clasificacion</label>
							<select class="form-control" id="finance_catalogue_classification_sort_create" name="finance_catalogue_classification_sort" required readonly>
								@foreach ($clasifications as $clasification)
									<option value="{{$clasification->id}}">{{$clasification->name}}</option>
								@endforeach
							</select>
						</div>
						<div class="form-group">
							<label for="sort">Siguiente Posicion</label>
							<input type="number" name="sort" id="sort_create">
						</div>
						<div>
							<label for="model">Modelo (Tabla de la base de datos)</label>
							<select class="form-control" name="model" id="model" onchange="changeModel(this,'create')" style="width: 50%">
								<option ></option>
								@foreach ($models as $model => $name)
									<option value="{{$model}}">{{$name}}</option>
								@endforeach
							</select>
						</div>
						<br>
						<div>
							<label for="model">Identificador del Modelo</label>
							<select class="form-control" name="model_id" id="model_id_create" style="width: 50%">
								<option ></option>
							</select>
						</div>
					</form>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				</div>
			</div>
		</div>
	</div>

@stop

@section('js')
    <script>
        $(document).ready(function(){
            $('#model_id').select2({width: 'resolve'});
            $('#model_id_create').select2({width: 'resolve'});
            $('[data-toggle="popover"]').popover();
        });

        function dNoneColumns(dNoneClass,action) {
            let selector = "[class^='"+dNoneClass+"-']";
            let elements = $(selector);
            let elementsToShow = elements.find("[class$='-show']");
            let elementsToHide = elements.find("[class$='-hide']");
            if(action === 'show'){
                elements.show();
                $('.'+dNoneClass+'-show').hide();
                $('.'+dNoneClass+'-hide').show();
                elementsToShow.hide();
                elementsToHide.show();
            }else{
                elements.hide();
                $('.'+dNoneClass+'-hide').hide();
                $('.'+dNoneClass+'-show').show();
                elementsToShow.show();
                elementsToHide.hide();
            }
        }

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
            return true;
        }

        async function showCreate(data_string){
            const totalClasifications = {{$clasifications->count()}};
            data_string = data_string.split('*-*');
            const item_id = data_string[0];

            const finance_catalogue_classification_sort = data_string[1];
            const sort = data_string[2];

            if(finance_catalogue_classification_sort > totalClasifications){
                alert('No se puede agregar cuentas debajo de esta Clasificación ya que no existen Clasificaciones sucesivas');
                return true;
            }

            let url = '{{url()->current()}}' + '/' + item_id + '/create';
            $('#form_create').attr('action', url);

            $('#finance_catalogue_classification_sort_create').val(finance_catalogue_classification_sort);

            $("#finance_catalogue_classification_sort_create option:not(:selected)").attr("disabled", "disabled");

            $('#sort_create').val(sort);

            $("#myModalCreate").modal();
            return true;
        }

        async function getModels(params) {
            let respuesta = await $.ajax({
                type: 'GET',
                url: '{!! route('finance_catalogue.get_models') !!}',
                data: params,
            });
            if (respuesta.msg === 'erro' || respuesta.response === 'reset_url') {
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
