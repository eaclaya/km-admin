@if(isset($result))
  <div class="row">
    <div class="col-md-12">
      <table class="table">
        <thead>
          <tr>
            <td>Codigo</td>
            <td>Descripcion</td>
            <td>Fecha</td>
            <td>Cantidad</td>
            <td>Numero de transferencia</td>
            <td>Origen</td>
            <td>Destino</td>
            <td>Proveedor</td>
          </tr>
        </thead>
        <tbody>
          @foreach($result as $item)
          <tr>
            <td>{{$item['product_key']}}</td>
            <td>{{$item['notes']}}</td>
            <td>{{$item['created_at']}}</td>
            <td>{{$item['qty']}}</td>
            <td>{{$item['transfer']}}</td>
            <td>{{$item['from_account']}}</td>
            <td>{{$item['to_account']}}</td>
            <td>{{$item['vendor']}}</td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
  @endif
