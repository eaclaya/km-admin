@extends("adminlte::page")

@section("title", "index")

@section("content_header")
<div class="container ">
    <div class="row">

    </div>
</div>

@stop

@section("content")
<div class="container ">
    <div class="header">
        <h1>
            Editar Rol
        </h1>
    </div>
    <div class="card">
        <div class="body">
            <div class="row">
                <div class="col-md-12 p-5">
                    <form action="{{ route('roles.update', $role->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                            <label for="name">Nombre del Rol</label>
                            <input type="text" name="name" class="form-control" value="{{ $role->name }}" required>
                        </div>

                        <div class="form-group">
                            <label for="permissions">Permisos</label>
                            <div class="row ml-2">
                                @foreach($permissionList as $key => $group)

                                @if($key != '' )
                                <div class="col-md-12">
                                    <h5>{{ Str::title($permissionCategories[$key]->name) }}</h5>
                                </div>
                                @endif
                                @foreach($group as $permission)
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input type="checkbox" name="permissions[]" value="{{ $permission['id'] }}-{{ $permission['code'] }} "
                                            class="checkbox-primary"
                                            {{ $permission["checked"] ? 'checked' : '' }}>
                                        <label class="form-check-label">{{ $permission['name'] }}</label>
                                    </div>
                                </div>

                                @endforeach
                                <hr>
                                @endforeach
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                    </form>
                </div>
            </div>
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