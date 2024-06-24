@extends('character.layout', ['isMyo' => $character->is_myo_slot])

@section('profile-title')
    Editing {{ $character->fullName }}'s Links
@endsection

@section('meta-img')
    {{ $character->image->thumbnailUrl }}
@endsection

@section('profile-content')
    {!! breadcrumbs([
        $character->category->masterlist_sub_id ? $character->category->sublist->name . ' Masterlist' : 'Character masterlist' => $character->category->masterlist_sub_id ? 'sublist/' . $character->category->sublist->key : 'masterlist',
        $character->fullName => $character->url,
        'Editing Links' => $character->url . '/links/edit',
    ]) !!}

    @include('character._header', ['character' => $character])

    @if ($character->user_id != Auth::user()->id)
        <div class="alert alert-warning">
            You are editing this character as a staff member.
        </div>
    @endif
    <div class="alert alert-info">This creates a one-to-one relation with all requested characters!</div>

    <strong>
        <p>Characters you own will auto-link and not require approval.</p>
    </strong>

    {!! Form::open(['url' => $character->url . '/links/edit']) !!}
    <div class="text-right mb-3">
        <a href="#" class="btn btn-outline-info" id="addCharacter">Add Character</a>
    </div>
    <div id="characters" class="mb-3">
    </div>

    <div class="text-right mb-3">
        {!! Form::submit('Request Links', ['class' => 'btn btn-primary']) !!}
    </div>

    {!! Form::close() !!}

    @include('widgets._link_select', ['character' => $character])
@endsection
@section('scripts')
    @parent
    @include('js._character_select_js')
@endsection
