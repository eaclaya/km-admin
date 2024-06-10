@extends('adminlte::page')

@section('css')
    <style>
        .fixed-input-group {
            width: 100px !important;
        }
    </style>
@stop

@section('content_header')
    <h1>
        Configuración del Menu
    </h1>
@stop

@section('content')
    <div class="card">
        <div class="card- overflow-auto">
            @if(isset($items))
                <form action="{{Route('setup_menu.store')}}" method="post">
                    @csrf
                    <div class="row">
                        <table class="table table-bordered" style="margin: 0 auto;">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Menu Superior</th>
                                    <th>Url</th>
                                    <th>Text</th>
                                    <th>Icon</th>
                                    <th>Permiso</th>
                                    <th>Label</th>
                                    <th>Color del Label</th>
                                    <th>Accion -
                                        <a class="btn btn-success btn-sm" onclick="showCreate('{{null}}')">
                                            <i class="fa fa-plus-circle" aria-hidden="true"></i>
                                        </a>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach ($items as $item)
                                @include('setup_menu.partials.rows',['item' => $item, 'ml' => 0])
                            @endforeach
                            </tbody>
                        </table>
                        <button type="submit" class="btn btn-primary mx-auto mt-2">Guardar</button>
                    </div>
                </form>
                <br>
                @if($items->hasPages())
                    <h1>Ver mas</h1>
                    {{ $items->links('pagination::bootstrap-5') }}
                @endif
            @else
                <div class="alert alert-warning" role="alert">
                    No hay datos para mostrar
                </div>
            @endif
        </div>
    </div>

	<div id="myModalCreate" class="modal fade" role="dialog">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title">Crear</h4>
					<button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                    </button>
				</div>
				<div class="modal-body">
					<form action="{{Route('setup_menu.create')}}" method="post" id="form_create">
                        <input type="hidden" name="supra_menu_id" id="supra_menu_id">
                        @csrf
						<div class="form-group">
							<label for="url">Url</label>
							<input type="text" class="form-control" id="url_create" name="url" >
                            <small class="form-text text-muted">Este campo no es requerido, puede dejarlo vacio o colocar una almoadilla.</small>
						</div>
                        <div class="form-group">
							<label for="url">Texto</label>
							<input type="text" class="form-control" id="text_create" name="text" required>
						</div>
                        <div class="form-group">
							<label for="url">Icono</label>
							<input type="text" class="form-control" id="icon_create" name="icon" >
                            <small class="form-text text-muted">Este campo no es requerido, puede dejarlo vacio.</small>
						</div>
                        <div class="form-group">
							<label for="url">Permiso</label>
							<input type="text" class="form-control" id="can_create" name="can" >
                            <small class="form-text text-muted">Este campo no es requerido, puede dejarlo vacio.</small>
						</div>
                        <div class="form-group">
							<label for="url">Label</label>
							<input type="text" class="form-control" id="label_create" name="label" >
                            <small class="form-text text-muted">Este campo no es requerido, puede dejarlo vacio.</small>
						</div>
                        <div class="form-group">
							<label for="url">Color del Label</label>
							<input type="text" class="form-control" id="label_color_create" name="label_color" >
                            <small class="form-text text-muted">Este campo no es requerido, puede dejarlo vacio.</small>
						</div>
                        <button type="submit" class="btn btn-primary">Guardar</button>
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

        async function showCreate(supra_menu_id){
            $('#supra_menu_id').val(supra_menu_id);

            $("#myModalCreate").modal();
            return true;
        }

    function confirmDelete(itemId) {
        return confirm("¿Estás seguro de que quieres eliminar la ruta: " + itemId + "?");
    }
    </script>
@stop
