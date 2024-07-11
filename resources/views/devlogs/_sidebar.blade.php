<ul>
    <li class="sidebar-header"><a href="{{ url('news') }}" class="card-link">News</a></li>
        <li class="sidebar-section">
            <div class="sidebar-section-header">Recent News</div>
            @foreach ($recentnews->take(2) as $news)
                @php $newslink = 'news/'.$news->slug; @endphp
                <div class="sidebar-item"><a href="{{ $news->url }}" class="{{ set_active($newslink) }}">{{ $news->title }}</a></div>
            @endforeach
            <div class="sidebar-item"><a href="{{ url('news') }}">All News >></a></div>
        </li>

    <li class="sidebar-header"><a href="{{ url('logs') }}" class="card-link">Developpement Logs</a></li>
    @if (isset($devLogses))
        <li class="sidebar-section">
            <div class="sidebar-section-header">On This Page</div>
            @foreach($devLogses as $devLogs)
            @php $logslink = 'devlogs/'.$devLogs->slug; @endphp
                <div class="sidebar-item"><a href="{{ $devLogs->url }}" class="{{ set_active($logslink) }}">{{ $devLogs->title }}</a></div>
            @endforeach
        </li>
    @else
        <li class="sidebar-section">
            <div class="sidebar-section-header">Recent Dev Logs</div>
            @foreach($recentdevLogs as $devLogs)
            @php $logslink = 'devlogs/'.$devLogs->slug; @endphp
                <div class="sidebar-item"><a href="{{ $devLogs->url }}" class="{{ set_active($logslink) }}">{{ $devLogs->title }}</a></div>
            @endforeach
        </li>
    @endif
</ul> 
