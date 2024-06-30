<div wire:ignore>
    <select
        style="width: 100%" class="form-control {{$name}}"
        id="{{$name}}" name="{{$name}}"
        @if(isset($x_model) && trim($x_model) !== '')
            x-model="{{$x_model}}"
        @endif
    >
        <!-- Add more options as needed -->
    </select>
    <script>
        async function livewireTransport{{$uniqId}}(params) {
            return new Promise((resolve, reject) => {
                let data = {
                    search: params.data.search ?? '',
                    page: params.data.page || 1
                };
                @this.dispatchSelf('getOptions', data);
                @this.on('options', (data) => {
                    resolve({
                        results: data[0].results,
                    });
                });
            });
        }

        function runAfterLivewireLoaded{{$uniqId}}(callback) {
            if (window.Livewire) {
                callback();
            } else {
                document.addEventListener('DOMContentLoaded', callback);
            }
        }
        runAfterLivewireLoaded{{$uniqId}}(() => {
            Livewire.hook('component.init', (component) => {
                let thisSelect = $('#{{$name}}');
                let dialog = thisSelect.closest('div[role="dialog"]');
                $('#{{$name}}').select2({
                    dropdownParent: dialog.length > 0 ? dialog : null,
                    width: 'resolve',
                    ajax: {
                        delay: 250,
                        data: function (params) {
                            return {
                                search: params.term,
                                page: params.page || 1
                            };
                        },
                        processResults: function (data, params) {
                            params.page = params.page || 1;
                            let data_results = [];
                            let arr = data.data;
                            if({{$all}} > 0){
                                if (params.page === 1) {
                                    data_results.unshift({'id': 'all', 'text': 'Todos'});
                                }
                            }

                            for (let index = 0; index < arr.length; index++) {
                                const element = arr[index];
                                data_results.push({'id':element['id'], 'text':element['text']});
                            }
                            return {
                                results: data_results,
                                pagination: {
                                    more: (params.page * data.per_page) < data.total,
                                }
                            };
                        },
                        transport: async function (params, success, failure) {
                            let request = await livewireTransport{{$uniqId}}(params);
                            success(request.results);
                            return request.results;
                        },
                    }
                });
                $('#{{$name}}').on('change', function (e) {
                    let data = $(this).select2("val");
                    if("{{$wire_model}}" !== ""){
                        let wire_model = "{{$wire_model}}";
                        @this.$parent.set(wire_model, data);
                    }
                    if({{$hasProperties}} !== 0){
                        @this.dispatchSelf('sendProperties', {'properties_id':data});
                    }
                });
                setTimeout(() => {
                    @this.on('clear-select', () => {
                        $('#{{$name}}').val(null).trigger('change');
                    });
                }, 1000);
            });
        });
    </script>
</div>
