<div wire:ignore>
    <select style="width: 100%" class="{{$name}}" id="{{$name}}" name="{{$name}}">
        <!-- Add more options as needed -->
    </select>
</div>
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

        document.addEventListener("DOMContentLoaded", () => {
            Livewire.hook('component.init', (component) => {
                $('#{{$name}}').select2({
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
                            console.dir(success);
                            let request = await livewireTransport{{$uniqId}}(params);
                            success(request.results);
                            return request.results;
                        },
                    }
                });
                {{--
                $('#{{$name}}').on('change', function (e) {
                    let elementName = $(this).attr('id');
                    let data = $(this).select2("val");
                    {{--@this.set(elementName, data);
                });
                --}}
            });
        });
</script>
