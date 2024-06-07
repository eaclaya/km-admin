<nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb" class="">
    @php
        $path = request()->path();
        $crumbs = explode('/', $path);
        foreach ($crumbs as $key => $val) {
            if (is_numeric($val)) {
                unset($crumbs[$key]);
            }
        }
        $crumbs = array_values($crumbs);
    @endphp
    <ol class="breadcrumb">
        /&nbsp;&nbsp;
        @for($i=0; $i<count($crumbs); $i++)
            @php
                $crumb = trim($crumbs[$i]);
                if (!$crumb) {
                    continue;
                }
                if ($crumb == 'company') {
                    continue;
                }
                $name = trans("texts.$crumb");
            @endphp
            @if ($i==count($crumbs)-1)
                <li class='breadcrumb-item active'>{{$name}}</li>
            @else
                <li class="breadcrumb-item"><a href="{{url($crumb)}}">{{$name}}</a></li>
            @endif
        @endfor
    </ol>
</nav>
