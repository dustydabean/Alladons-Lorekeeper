<div class="card mb-3">
    <div class="card-header">
        <h2 class="card-title mb-0">{!! $devLogs->displayName !!}</h2>
        <small>
            Posted {!! $devLogs->post_at ? pretty_date($devLogs->post_at) : pretty_date($devLogs->created_at) !!} :: Last edited {!! pretty_date($devLogs->updated_at) !!} by {!! $devLogs->user->displayName !!}
        </small>
    </div>
    <div class="card-body">
        <div class="parsed-text">
            {!! $devLogs->parsed_text !!}
        </div>
    </div>
    <?php $commentCount = App\Models\Comment::where('commentable_type', 'App\Models\DevLogs')->where('commentable_id', $devLogs->id)->count(); ?>
    @if(!$page)
        <div class="text-right mb-2 mr-2">
            <a class="btn" href="{{ $devLogs->url }}"><i class="fas fa-comment"></i> {{ $commentCount }} Comment{{ $commentCount != 1 ? 's' : ''}}</a>
        </div>
    @else
        <div class="text-right mb-2 mr-2">
            <span class="btn"><i class="fas fa-comment"></i> {{ $commentCount }} Comment{{ $commentCount != 1 ? 's' : ''}}</span>
        </div>
    @endif
</div>
