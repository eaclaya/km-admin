<table class="table">
      <thead>
              <tr>
                      <td>Proveedor</td>
                      <td>Unidades vendidas</td>
                      <td>Monto vendido</td>
                      <td>Costo Total</td>
              </tr>
      </thead>
      <tbody>
              @foreach($result as $item)
              <tr>
                      <td>{{$item['vendor']}}</td>
                      <td>{{$item['qty']}}</td>
                      <td>{{$item['total']}}</td>
                      <td>{{$item['cost']}}</td>
              </tr>
              @endforeach
      </tbody>
</table>
