<div>
    @section('title', 'Daybook')

    @section('content_header')
        <h1>DayBook</h1>
    @stop
    @section('content')
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">DayBook</h3>
            </div>
            <div class="card-body">
                @livewire('datatables.finance-catalogue')
{{--                <livewire:datatables.finance-catalogue />--}}
            </div>
        </div>
    @stop

    @section('css')
    @stop

    @section('js')
    @stop
</div>
