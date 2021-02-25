@extends('character.layout', ['isMyo' => $character->is_myo_slot])

@section('profile-title') {{ $character->fullName }}'s Links @endsection

@section('meta-img') {{ $character->image->thumbnailUrl }} @endsection

@section('profile-content')
{!! breadcrumbs([($character->category->masterlist_sub_id ? $character->category->sublist->name.' Masterlist' : 'Character masterlist') => ($character->category->masterlist_sub_id ? 'sublist/'.$character->category->sublist->key : 'masterlist' ), $character->fullName => $character->url, 'Profile' => $character->url . '/profile']) !!}

<h2 class="bold">{{$character->fullName}}'s Links</h2>

<div class="container mt-4">
@foreach($character->links as $link)
<div class="row mb-2">
    <div class="col-md-7 mb-md-0 mb-2 text-center">
        <div>
            <a href="{{ $character->url }}"><img src="{{ $character->image->thumbnailUrl }}" class="img-thumbnail" /></a>
        </div>
        <div class="mt-1">
            <a href="{{ $character->url }}" class="h5 mb-0">@if(!$character->is_visible) <i class="fas fa-eye-slash"></i> @endif {{ $character->fullName }}</a>
        </div>
        <div class="small">
            {!! $character->image->species_id ? $character->image->species->displayName : 'No Species' !!} ・ {!! $character->image->rarity_id ? $character->image->rarity->displayName : 'No Rarity' !!} ・ {!! $character->displayOwner !!}
        </div>
    </div>
        <!-- second half -->
    <div class="mb-md-0 mb-2 text-center">
        <div>
            <a href="{{ $link->character->url }}"><img src="{{ $link->character->image->thumbnailUrl }}" class="img-thumbnail" /></a>
        </div>
        <div class="mt-1">
            <a href="{{ $link->character->url }}" class="h5 mb-0">@if(!$link->character->is_visible) <i class="fas fa-eye-slash"></i> @endif {{ $link->character->fullName }}</a>
        </div>
        <div class="small">
            {!! $link->character->image->species_id ? $link->character->image->species->displayName : 'No Species' !!} ・ {!! $link->character->image->rarity_id ? $link->character->image->rarity->displayName : 'No Rarity' !!} ・ {!! $link->character->displayOwner !!}
        </div>
    </div>
</div>

<div class="container mb-2">
    <div class="card character-bio w-100">
        <div class="card-header">
            <div class="row">
                <ul class="col-5 nav nav-tabs card-header-tabs">
                    <li class="nav-item ml-4">
                        <div class="nav-link active"  data-toggle="tab" role="tab">Info</a>      
                    </li>
                </ul>
            <h6 class="text-center text-uppercase"><b>Relationship Status: {{ $link->type }}</b></h6>
        </div>
    </div>
    
        <div class="card-body tab-content">   
        {{-- Basic info  --}}
            <div class="tab-pane fade show active">
                <div class="row">
                    <div class="col-md-6 mb-md-0 mb-2">
                        <div class="card m-2">

                                @if(Auth::check() && ($character->user_id == Auth::user()->id || Auth::user()->hasPower('manage_characters')))

                                {!! Form::open(['url' => $character->url .'/links/info/'.$link->id]) !!}
                                {!! Form::hidden('chara_1', $character->id) !!}
                                {!! Form::hidden('chara_2', $link->chara_2) !!}
                                {!! Form::textarea('info', $link->info ? $link->info : null, ['placeholder' => 'What are your characters feelings?', 'class' => 'form-control mb-2' , 'cols' => 20, 'rows' => 5]) !!}
                                
                                {!! Form::select('type', $types, null, ['class' => 'form-control mt-2', 'placeholder' => 'Relationship Type']) !!}
                                <div class="text-right m-2">
                                    {!! Form::button('<i class="fas fa-cog"></i> Edit Info', ['class' => 'btn btn-outline-info btn-sm', 'type' => 'submit']) !!}
                                </div>
                                {!! Form::close() !!}
                                @else
                                <div class="m-4">{{ $link->info }}</div>
                                @endif
                            </div>
                            @if(Auth::check() && ($character->user_id == Auth::user()->id || Auth::user()->hasPower('manage_characters')))
                                <button type="button" class="btn btn-danger btn-sm m-1" data-toggle="modal" data-target="#deleteModal">
                                    <i class="fas fa-trash"></i>
                                </button>
                            @endif
                        </div>
                    <div class="col-md-6 mb-md-0 mb-2">
                        <div class="card m-2">
                            <div class="m-4">{{ $link->inverse->info }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<br>
<hr>
<br>

{{-- Model --}}
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="deleteModalLabel">Delete this link?</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          You will have to make a new request to get back the link. All data is non-retrievable.
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
          {!! Form::open(['url' => $character->url .'/links/delete/'.$link->id]) !!}
          {!! Form::hidden('chara_1', $character->id) !!}
          {!! Form::hidden('chara_2', $link->chara_2) !!}
          {!! Form::button('<i class="fas fa-trash"></i> Delete', ['class' => 'btn btn-danger btn-sm m-1', 'type' => 'submit']) !!}
          {!! Form::close() !!}
        </div>
      </div>
    </div>
  </div>
@endforeach
</div>

{{-- Bio --}}
<a class="float-left m-2" href="{{ url('reports/new?url=') . $character->url . '/links' }}"><i class="fas fa-exclamation-triangle" data-toggle="tooltip" title="Click here to report this character's links." style="opacity: 50%;"></i></a>
@if(Auth::check() && ($character->user_id == Auth::user()->id || Auth::user()->hasPower('manage_characters')))
    <div class="text-right m-2">
        <a href="{{ $character->url . '/links/edit' }}" class="btn btn-outline-info btn-sm"><i class="fas fa-envelope"></i> Request Links</a>
    </div>
@endif
@endsection