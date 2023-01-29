@extends('user.layout')

@section('profile-title') {{ $user->name }}'s Collection Logs @endsection

@section('profile-content')
{!! breadcrumbs(['Users' => 'users', $user->name => $user->url, 'Logs' => $user->url.'/collection-logs']) !!}

<h1>
    {!! $user->displayName !!}'s Collection Logs
</h1>

<h3 class="text-center">
    {!! $user->displayName !!}'s Completed Collections
</h3>
@if($user->collections->count())
@foreach($collections as $categoryId=>$categoryCollections)
    <div class="card mb-3 inventory-category">
        <h5 class="card-header inventory-header">
            {!! isset($categories[$categoryId]) ? '<a href="'.$categories[$categoryId]->searchUrl.'">'.$categories[$categoryId]->name.'</a>' : 'Miscellaneous' !!}
            <a class="small inventory-collapse-toggle collapse-toggle collapsed" href="#{!! isset($categories[$categoryId]) ? str_replace(' ', '', $categories[$categoryId]->name) : 'miscellaneous' !!}" data-toggle="collapse">Show</a></h3>
        </h5>
        <div class="card-body inventory-body collapse show" id="{!! isset($categories[$categoryId]) ? str_replace(' ', '', $categories[$categoryId]->name) : 'miscellaneous' !!}">
            @foreach($categoryCollections->chunk(4) as $chunk)
                <div class="row mb-3">
                    @foreach($chunk as $collectionId=>$stack)
                        <div class="col-sm-3 col-6 text-center inventory-Collection" data-id="{{ $stack->first()->pivot->id }}" data-name="{{ $user->name }}'s {{ $stack->first()->name }}">
                            <div class="mb-1">
                                <a href="{{ $stack->first()->idUrl }}"><img src="{{ $stack->first()->imageUrl }}" alt="{{ $stack->first()->name }}" /></a>
                            </div>
                            <div>
                                {!! $stack->first()->displayName !!}</a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>
    </div>
@endforeach
@else
<div class="text-center">{!! $user->displayName !!} hasn't completed any collections.</div>
@endif

@if($user->collections->count())
{!! $logs->render() !!}
<div class="row ml-md-2 mb-4">
  <div class="d-flex row flex-wrap col-12 mt-1 pt-1 px-0 ubt-bottom">
    <div class="col-6 col-md-2 font-weight-bold">Sender</div>
    <div class="col-6 col-md-2 font-weight-bold">Recipient</div>
    <div class="col-6 col-md-2 font-weight-bold">Collection</div>
    <div class="col-6 col-md-4 font-weight-bold">Log</div>
    <div class="col-6 col-md-2 font-weight-bold">Date</div>
  </div>
  @foreach($logs as $log)
      @include('user._collection_log_row', ['log' => $log, 'owner' => $user])
  @endforeach
</div>
{!! $logs->render() !!}
@endif

@endsection