@extends("adminlte::page")

@section("title", "index")

@section("content_header")
    <h1>
        Descuento en Facturas -
        <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#exampleModal">
            Procesos
        </button>
    </h1>
@stop

@section("content")
    <div class="container">
        <form action="{{route('invoice_discount.set_discount')}}" method="post" enctype="multipart/form-data">
            <div class="input-group">
                @csrf
                <label for="formFileSm" class="input-group-text form-label">Subir Archivo de facturas Modificadas</label>
                <input class="form-control" id="formFileSm" type="file" name="csv_file">
                <button class="btn btn-outline-secondary" type="submit" id="button-addon2">Subir</button>
            </div>
        </form>
        <br>
        <div class="container">
            <livewire:Datatables.invoice-discount-table :account_id="$account_id"/>
        </div>
    </div>
    <div class="modal fade" id="exampleModal" role="dialog" >
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Procesos</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <livewire:Datatables.report-process-table :name="$name" />
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@stop

@section("css")
    {{-- Add here extra stylesheets --}}
@stop

@section("js")
    <script>
        $(document).ready(function() {
            Livewire.on('sentIds', (ids) => {
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    type: 'POST',
                    url: '{{ route('invoice_discount.export_invoice_pdf') }}',
                    data: { ids: ids[0].ids },
                    success: function(response) {
                        console.log(response);
                        alert('Se han convertido las facturas a pdf');
                    }
                });
            });
        })
    </script>
@stop
