@extends("adminlte::page")

@section("title", "create")

@section("content_header")
<div class="container ">
    <div class="d-flex justify-content-between">
        <div class="header">
            <h1>
                Crear Nuevo Permiso
            </h1>
        </div>
    </div>
</div>
@stop

@section('content')
<div class="container">
    <div class="card p-3">
        <div class="card-body">
            <form action="{{ route('permissions.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="name">Nombre del Permiso</label>
                    <input type="text" requied class="form-control" id="name" name="name" required>
                </div>
                <div class="form-group">
                    <label for="code">Codigo</label>
                    <input type="text" requied class="form-control" id="code" name="code" required>
                </div>

                <div class="form-group">
                    <label for="description">Descripcion</label>
                    <textarea class="form-control" name="description" id="description"></textarea>
                </div>

                <div class="form-group">
                    <label for="category">Categoria</label>
                    <select class="form-control" name="category" id="category">
                        <option value="0">Seleccione una Categoria</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->value }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Crear Permiso</button>
            </form>
        </div>
    </div>

</div>
@endsection