<ul>
    <li class="sidebar-header"><a href="{{ url('news') }}" class="card-link">News</a></li>
    @if (isset($newses))
        <li class="sidebar-section">
            <div class="sidebar-section-header">On This Page</div>
            @foreach ($newses as $news)
                @php $newslink = 'news/'.$news->slug; @endphp
                <div class="sidebar-item"><a href="{{ $news->url }}" class="{{ set_active($newslink) }}">{{ $news->title }}</a></div>
            @endforeach
        </li>
    @else
        <li class="sidebar-section">
            <div class="sidebar-section-header">Recent News</div>
            @foreach ($recentnews as $news)
                @php $newslink = 'news/'.$news->slug; @endphp
                <div class="sidebar-item"><a href="{{ $news->url }}" class="{{ set_active($newslink) }}">{{ $news->title }}</a></div>
            @endforeach
        </li>
    @endif

    <li class="sidebar-header"><a href="{{ url('devlogs') }}" class="card-link">Developpement Logs</a></li>
        <li class="sidebar-section">
            <div class="sidebar-section-header">Recent Dev Logs</div>
            @foreach ($recentdevLogs->take(2) as $devLogs)
                @php $logslink = 'devlogs/'.$devLogs->slug; @endphp
                <div class="sidebar-item"><a href="{{ $devLogs->url }}" class="{{ set_active($logslink) }}">{{ $devLogs->title }}</a></div>
            @endforeach
            <div class="sidebar-item"><a href="{{ url('devlogs') }}">All Dev Logs >></a></div>
        </li>
</ul>
