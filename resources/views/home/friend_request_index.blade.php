@extends('home.layout')

@section('home-title') Friend Requests @endsection

@section('home-content')
{!! breadcrumbs(['Friends' => 'friends', 'Friend Requests' => 'friends/requests']) !!}

<h1>
    Friend Requests
</h1>

<div class="row">
    <div class="col-md-6 card">
        <h3 class="card-header">Pending Requests</h3>
        @if(count($received_requests))
            <div class="list-group">
                @foreach($received_requests as $friend)
                    <div class="logs-table-row card p-2">
                        <div class="row flex-wrap">
                            <div class="col-md-4"><div class="logs-table-cell">{!! $friend->initiator->displayName !!}</div></div>
                            <div class="col-md-4"><div class="logs-table-cell text-right">{!! pretty_date($friend->created_at, false) !!}</div></div>
                            <div class="col-md-4">
                                <div class="logs-table-cell row col-12">
                                    <div class="col-6">
                                        {!! Form::open(['url' => 'friends/requests/'.$friend->id.'/1']) !!}
                                            {!! Form::submit('Accept', ['class' => 'btn btn-success btn-sm']) !!}
                                        {!! Form::close() !!}
                                    </div>
                                    <div class="col-6">
                                        {!! Form::open(['url' => 'friends/requests/'.$friend->id.'/0']) !!}
                                            {!! Form::submit('Decline', ['class' => 'btn btn-danger btn-sm']) !!}
                                        {!! Form::close() !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="my-2">You have no pending friend requests.</p>
        @endif
    </div>
    <div class="col-md-6 card">
        <h3 class="card-header">Sent Requests</h3>
        @if(count($sent_requests))
            <div class="logs-table-body">
                @foreach($sent_requests as $friend)
                    <div class="logs-table-row card p-2">
                        <div class="row flex-wrap">
                            <div class="col-md-6"><div class="logs-table-cell">{!! $friend->recipient->displayName !!}</div></div>
                            <div class="col-md-6 text-right">
                                <div class="logs-table-cell">
                                    {!! pretty_date($friend->created_at, false) !!}
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="my-2">You have no sent friend requests that are pending.</p>
        @endif
    </div>
</div>
@endsection

@section('scripts')
@parent 
    <script>

    </script>
@endsection