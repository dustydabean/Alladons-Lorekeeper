@extends('admin.layout')

@section('admin-title') Dev Logs @endsection

@section('admin-content')
{!! breadcrumbs(['Admin Panel' => 'admin', 'Dev Logs' => 'admin/logs']) !!}

<h1>Dev Logs</h1>

<p>You can create new dev log posts here. Creating a dev log post alerts every user that there is a new post, unless the post is marked as not viewable (see the post creation page for details).</p>

<div class="text-right mb-3"><a class="btn btn-primary" href="{{ url('admin/logs/create') }}"><i class="fas fa-plus"></i> Create New Post</a></div>
@if(!count($devLogses))
    <p>No dev logs found.</p>
@else
    {!! $devLogses->render() !!}
      <div class="row ml-md-2">
        <div class="d-flex row flex-wrap col-12 pb-1 px-0 ubt-bottom">
          <div class="col-12 col-md-5 font-weight-bold">Title</div>
          <div class="col-6 col-md-3 font-weight-bold">Posted At</div>
          <div class="col-6 col-md-3 font-weight-bold">Last Edited</div>
        </div>
        @foreach($devLogses as $devLogs)
        <div class="d-flex row flex-wrap col-12 mt-1 pt-2 px-0 ubt-top">
          <div class="col-12 col-md-5">
              @if(!$devLogs->is_visible)
                  @if($devLogs->post_at)
                      <i class="fas fa-clock mr-1" data-toggle="tooltip" title="This post is scheduled to be posted in the future."></i>
                  @else
                      <i class="fas fa-eye-slash mr-1" data-toggle="tooltip" title="This post is hidden."></i>
                  @endif
              @endif
              <a href="{{ $devLogs->url }}">{{ $devLogs->title }}</a>
          </div>
          <div class="col-6 col-md-3">{!! pretty_date($devLogs->post_at ? : $devLogs->created_at) !!}</div>
          <div class="col-6 col-md-3">{!! pretty_date($devLogs->updated_at) !!}</div>
          <div class="col-12 col-md-1 text-right"><a href="{{ url('admin/logs/edit/'.$devLogs->id) }}" class="btn btn-primary py-0 px-2 w-100">Edit</a></div>
        </div>
        @endforeach
      </div>
    {!! $devLogses->render() !!}

    <div class="text-center mt-4 small text-muted">{{ $devLogses->total() }} result{{ $devLogses->total() == 1 ? '' : 's' }} found.</div>

@endif

@endsection