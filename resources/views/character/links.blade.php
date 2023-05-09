@extends('character.layout', ['isMyo' => $character->is_myo_slot])

@section('profile-title') {{ $character->fullName }}'s Links @endsection

@section('meta-img') {{ $character->image->thumbnailUrl }} @endsection

@section('profile-content')
{!! breadcrumbs([($character->category->masterlist_sub_id ? $character->category->sublist->name.' Masterlist' : 'Character masterlist') => ($character->category->masterlist_sub_id ? 'sublist/'.$character->category->sublist->key : 'masterlist' ), $character->fullName => $character->url, 'Profile' => $character->url . '/profile']) !!}

    <h2>{{ $character->fullName }}'s Links</h2>
    @if (count($character->links))
        <div class="container mt-4">
            @foreach($character->links as $link)
                <div class="row mb-2 justify-content-center">
                    @include('character._link_character', ['character' => $character])
                    @include('character._link_character', ['character' => $link->character])
                </div>

                <div class="card mb-2">
                    <div class="card-header">
                        <div class="row">
                            <ul class="col-5 nav nav-tabs card-header-tabs">
                                <li class="nav-item ml-4">
                                    <a class="nav-link active"  data-toggle="tab" role="tab">Info</a>      
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
                                    <div class="m-2">
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

            @if (!$loop->last())
                <hr class="my-4 w-75" />
            @endif

            @include('character._link_delete_modal', ['character' => $character, 'link' => $link])

            @endforeach
        </div>
    @else
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i> This character has no links.
        </div>
    @endif

    <hr class="my-4 w-75" />

    {{-- Bio --}}
    <a class="float-left m-2" href="{{ url('reports/new?url=') . $character->url . '/links' }}">
        <i class="fas fa-exclamation-triangle" data-toggle="tooltip" title="Click here to report this character's links." style="opacity: 50%;"></i>
    </a>

    @if(Auth::check() && ($character->user_id == Auth::user()->id || Auth::user()->hasPower('manage_characters')))
        <div class="text-right m-2">
            <a href="{{ $character->url . '/links/edit' }}" class="btn btn-outline-info btn-sm"><i class="fas fa-envelope"></i> Request Links</a>
        </div>
    @endif
@endsection