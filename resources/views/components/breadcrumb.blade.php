<nav aria-label="breadcrumb" class="no-print">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="bi bi-house-door"></i> Dashboard</a></li>
        @isset($items)
            @foreach($items as $item)
                @if($loop->last)
                    <li class="breadcrumb-item active" aria-current="page">{{ $item['title'] }}</li>
                @else
                    <li class="breadcrumb-item"><a href="{{ $item['url'] }}">{{ $item['title'] }}</a></li>
                @endif
            @endforeach
        @endisset
    </ol>
</nav>
