@extends("adminlte::page")

@section("title", "index")
@section('content')
<h1>Crear Nuevo Rol</h1>
<div class="container card p-5">
 
    <form action="{{ route('roles.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="name">Nombre del Rol:</label>
            <input type="text" class="form-control" id="name" name="name" required>
        </div>
        <div class="form-group">
            <label for="permissions">Permisos:</label>
            <div id="permissions">
                @foreach($permissions->chunk(4) as $chunk)
                    <div class="row">
                        @foreach($chunk as $permission)
                            <div class="col-md-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="permission_{{ $permission->id }}" name="permissions[]" value="{{ $permission->id }}">
                                    <label class="form-check-label" for="permission_{{ $permission->id }}">
                                        {{ $permission->code }}
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </div>
        </div>
        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
    </form>
</div>
@endsection