@extends('character.layout', ['isMyo' => $character->is_myo_slot])

@section('profile-title') Editing {{ $character->fullName }}'s Links @endsection

@section('meta-img') {{ $character->image->thumbnailUrl }} @endsection

@section('profile-content')
{!! breadcrumbs([($character->category->masterlist_sub_id ? $character->category->sublist->name.' Masterlist' : 'Character masterlist') => ($character->category->masterlist_sub_id ? 'sublist/'.$character->category->sublist->key : 'masterlist' ), $character->fullName => $character->url, 'Editing Links' => $character->url . '/links/edit']) !!}

@include('character._header', ['character' => $character])

@if($character->user_id != Auth::user()->id)
    <div class="alert alert-warning">
        You are editing this character as a staff member.
    </div>
@endif

{!! Form::open(['url' => 'links/edit/post']) !!}
<div id="characters" class="mb-3">
</div>
<div class="text-right mb-3">
    <a href="#" class="btn btn-outline-info" id="addCharacter">Add Character</a>
</div>
{!! Form::close() !!}

{!! Form::submit('Request Links', ['class' => 'btn btn-primary', 'id' => 'idbuttonUpdateStatus', 'onclick' => "this.disabled=true;this.value='Roll Daily';this.form.submit();"]) !!}
@include('widgets._link_select')

@endsection
@section('scripts')
@parent 
    @include('js._character_select_js')
@endsection