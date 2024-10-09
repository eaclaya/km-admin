@extends("adminlte::page")

@section("title", "index")

@section("content_header")
<div class="container ">
    <div class="row">
        <h1>
            Editar de Rol
        </h1>
    </div>
</div>
   
@stop

@section("content")
<div class="container card p-5">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <form action="{{ route('roles.update', $role->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="form-group">
                    <label for="name">Nombre del Rol</label>
                    <input type="text" name="name" class="form-control" value="{{ $role->name }}" required>
                </div>
                
                <div class="form-group">
                    <label for="permissions">Permisos</label>
                    <div class="row">
                        @foreach($permissionList as $permission)
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input type="checkbox" name="permissions[]" value="{{ $permission['id'] }}-{{ $permission['code'] }} " 
                                           class="form-check-input" 
                                           {{ $permission["checked"] ? 'checked' : '' }}>
                                    <label class="form-check-label">{{ $permission['name'] }}</label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary">Guardar Cambios</button>
            </form>
        </div>
    </div>
</div>
@stop

@section("css")
    {{-- Add here extra stylesheets --}}
@stop

@section("js")
    {{-- Add here extra javascript --}}
@stop
