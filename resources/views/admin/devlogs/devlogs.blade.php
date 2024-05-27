@extends('admin.layout')

@section('admin-title') Dev Logs @endsection

@section('admin-content')
{!! breadcrumbs(['Admin Panel' => 'admin', 'Dev Logs' => 'admin/devlogs']) !!}

<h1>Dev Logs</h1>

<p>You can create new dev log posts here. Creating a dev log post alerts every user that have the notification setting turned on (by default) that there is a new post, unless the post is marked as not viewable (see the post creation page for details).</p>

<div class="text-right mb-3"><a class="btn btn-primary" href="{{ url('admin/devlogs/create') }}"><i class="fas fa-plus"></i> Create New Post</a></div>
@if(!count($devLogses))
    <p>No dev logs found.</p>
@else
    {!! $devLogses->render() !!}
    <div class="mb-4 logs-table">
        <div class="logs-table-header">
            <div class="row">
                <div class="col-12 col-md-5">
                    <div class="logs-table-cell">Title</div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="logs-table-cell">Posted At</div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="logs-table-cell">Last Edited</div>
                </div>
            </div>
        </div>
        <div class="logs-table-body">
        @foreach($devLogses as $devLogs)
          <div class="logs-table-row">
              <div class="row flex-wrap">
                  <div class="col-12 col-md-5">
                      <div class="logs-table-cell">
                      @if(!$devLogs->is_visible)
                          @if($devLogs->post_at)
                              <i class="fas fa-clock mr-1" data-toggle="tooltip"       title="This post is scheduled to be posted in the  future.      "></i>
                          @else
                              <i class="fas fa-eye-slash mr-1" data-toggle="tooltip"       title="This post is hidden."></i>
                          @endif
                      @endif
                      <a href="{{ $devLogs->url }}">{{ $devLogs->title }}</a>
                      </div>  
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="logs-table-cell">{!! pretty_date($devLogs->post_at ? : $devLogs->created_at) !!}</div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="logs-table-cell">{!! pretty_date($devLogs->updated_at) !!}</div>
                    </div>
                    <div class="col-12 col-md-1 text-right">
                        <div class="logs-table-cell"><a href="{{ url('admin/devlogs/edit/'.$devLogs->id) }}" class="btn btn-primary py-0 px-2 w-100">Edit</a></div>
                    </div>
                </div>
            </div>
        @endforeach
      </div>
  </div>
    {!! $devLogses->render() !!}

    <div class="text-center mt-4 small text-muted">{{ $devLogses->total() }} result{{ $devLogses->total() == 1 ? '' : 's' }} found.</div>

@endif

@endsection