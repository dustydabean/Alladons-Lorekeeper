@extends('admin.layout')

@section('admin-title')
    Pet Levels
@endsection

@section('admin-content')
    {!! breadcrumbs(['Admin Panel' => 'admin', 'Pets' => 'admin/data/pets', 'Pet Levels' => 'admin/data/pets/levels']) !!}

    <h1>Pet Levels</h1>

    <p>
        These levels represent how much a character can "bond" with specified pets, and the benefits / rewards that come with that bond.
        <br />Pets only gain "level" information after being attached to a character, and the level is determined by the character's actions and interactions with the pet.
    </p>

    <div class="text-right mb-3">
        <a class="btn btn-primary" href="{{ url('admin/data/pets') }}"><i class="fas fa-arrow-left mr-1"></i> Back to Pets</a>
        <a class="btn btn-primary" href="{{ url('admin/data/pets/levels/create') }}"><i class="fas fa-plus mr-1"></i> Create New Level</a>
    </div>

    @if (!config('lorekeeper.pets.pet_bonding_enabled'))
        <p class="alert alert-info">
            <strong>NOTE:</strong> Pet bonding is currently disabled. You can enable it in site config.
        </p>
    @endif

    @if (!count($levels))
        <p>No pet levels found.</p>
    @else
        {!! $levels->render() !!}
        <div class="row ml-md-2">
            <div class="d-flex row flex-wrap col-12 mt-1 pt-1 px-0 ubt-bottom">
                <div class="col-5 col-md-6 font-weight-bold">Level</div>
                <div class="col col-md font-weight-bold">Name</div>
            </div>
            @foreach ($levels->sortBy('level') as $level)
                <div class="d-flex row flex-wrap col-12 mt-1 pt-1 px-0 ubt-top">
                    <div class="col-5 col-md-6"> {{ $level->level }} </div>
                    <div class="col-5 col-md-5"> {{ $level->name }} </div>
                    <div class="col-2 col-md-1 text-right"> <a href="{{ url('admin/data/pets/levels/edit/' . $level->id) }}" class="btn btn-primary py-0">Edit</a> </div>
                </div>
            @endforeach
        </div>
        {!! $levels->render() !!}
    @endif

    <div class="text-center mt-4 small text-muted">{{ $levels->total() }} result{{ $levels->total() == 1 ? '' : 's' }} found.</div>

@endsection

@section('scripts')
    @parent
@endsection
