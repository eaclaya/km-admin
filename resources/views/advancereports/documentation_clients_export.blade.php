    @if (isset($result))
        <div class="row">
            <div class="col-md-12">
                <table class="table">
                    <thead>
                        <tr>
                            <td>ID</td>
                            <td>Ruta</td>
                            <td>Cliente</td>
                            <td>Empresa</td>
                            <td>Tipo</td>
                            <td>Telefono</td>
                            <td>Creado</td>
                            <td>Documentos</td>
                            <td>rtn</td>
                            <td>croquis</td>
                            <td>identidad</td>
                            <td>letra_cambio_firmada</td>
                            <td>foto_actividad_negocio</td>
                            <td>recibo_servicio_publico</td>
                            <td>revision_historial</td>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($result as $item)
                            <tr>
                                <td>{{ $item['id'] }}</td>
                                <td>{{ $item['route_name'] }}</td>
                                <td>{{ $item['name'] }}</td>
                                <td>{{ $item['company_name'] }}</td>
                                <td>{{ $item['type'] }}</td>
                                <td>{{ $item['phone'] }}</td>
                                <td>{{ $item['created_at'] }}</td>
                                <td>{{ count($item['extra_attributes']['adjunts'] ?? []) }} de 7</td>
                                <td>
                                    @if (isset($item['extra_attributes']['adjunts']['rtn']))
                                        <a href="{{ config('app.url') }}/{{ $item['extra_attributes']['adjunts']['rtn'] }}"
                                            target="_blank"> Ver </a>
                                    @endif
                                </td>
                                <td>
                                    @if (isset($item['extra_attributes']['adjunts']['croquis']))
                                        <a href="{{ config('app.url') }}/{{ $item['extra_attributes']['adjunts']['croquis'] }}"
                                            target="_blank"> Ver </a>
                                    @endif
                                </td>
                                <td>
                                    @if (isset($item['extra_attributes']['adjunts']['identidad']))
                                        <a href="{{ config('app.url') }}/{{ $item['extra_attributes']['adjunts']['identidad'] }}"
                                            target="_blank"> Ver </a>
                                    @endif
                                </td>
                                <td>
                                    @if (isset($item['extra_attributes']['adjunts']['letra_cambio_firmada']))
                                        <a href="{{ config('app.url') }}/{{ $item['extra_attributes']['adjunts']['letra_cambio_firmada'] }}"
                                            target="_blank"> Ver </a>
                                    @endif
                                </td>
                                <td>
                                    @if (isset($item['extra_attributes']['adjunts']['foto_actividad_negocio']))
                                        <a href="{{ config('app.url') }}/{{ $item['extra_attributes']['adjunts']['foto_actividad_negocio'] }}"
                                            target="_blank"> Ver </a>
                                    @endif
                                </td>
                                <td>
                                    @if (isset($item['extra_attributes']['adjunts']['recibo_servicio_publico']))
                                        <a href="{{ config('app.url') }}/{{ $item['extra_attributes']['adjunts']['recibo_servicio_publico'] }}"
                                            target="_blank"> Ver </a>
                                    @endif
                                </td>
                                <td>
                                    @if (isset($item['extra_attributes']['adjunts']['revision_historial_credito']))
                                        <a href="{{ config('app.url') }}/{{ $item['extra_attributes']['adjunts']['revision_historial_credito'] }}"
                                            target="_blank"> Ver </a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif