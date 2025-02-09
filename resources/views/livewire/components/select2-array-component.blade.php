<div wire:ignore>
    <select
        style="width: 100%" class="form-control {{$name}}"
        id="{{$name}}" name="{{$name . ((isset($is_multiple) && $is_multiple !== false) ? '[]' : '')}}"
        @if(isset($x_model) && trim($x_model) !== '')
            x-model="{{$x_model}}"
        @endif
        @if(isset($is_multiple) && $is_multiple !== false)
            multiple="multiple"
        @endif
    >
        <!-- Add more options as needed -->
    </select>
    <script>
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
                    tags: true,
                    data: @json($array)
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
            });
        });
    </script>
</div>
