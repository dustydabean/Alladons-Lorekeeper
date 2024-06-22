@extends('user.layout')

@section('profile-title')
    {{ $user->name }}'s Pets
@endsection

@section('profile-content')
    {!! breadcrumbs(['Users' => 'users', $user->name => $user->url, 'Pets' => $user->url . '/pets']) !!}

    <h1>
        Pets
    </h1>

    @foreach ($pets as $categoryId => $categoryPets)
        <div class="card mb-3 inventory-category">
            <h5 class="card-header inventory-header">
                {!! isset($categories[$categoryId]) ? '<a href="' . $categories[$categoryId]->searchUrl . '">' . $categories[$categoryId]->name . '</a>' : 'Miscellaneous' !!}
            </h5>
            <div class="card-body inventory-body">
                @foreach ($categoryPets->chunk(4) as $chunk)
                    <div class="row mb-3">
                        @foreach ($chunk as $pet)
                            <?php
                            $pet->pivot->pluck('pet_name', 'id');
                            $stackName = $pet->pivot->pluck('pet_name', 'id')->toArray()[$pet->pivot->id];
                            ?>
                            <div class="col-sm-3 col-6 text-center inventory-item" data-id="{{ $pet->pivot->id }}" data-name="{{ $user->name }}'s {{ $pet->pivot->pet_name ?? $pet->name }}">
                                <div class="mb-1">
                                    <a href="#" class="inventory-stack">
                                        <img class="img-fluid rounded" src="{{ $pet->VariantImage($pet->pivot->id) }}" />
                                    </a>
                                </div>
                                <div>
                                    <a href="#" class="inventory-stack inventory-stack-name">
                                        {{ $pet->pivot->evolution_id ? $pet->evolutions->where('id', $pet->pivot->evolution_id)->first()->evolution_name : $pet->name }}
                                        @if ($pet->pivot->has_image)
                                            <i class="fas fa-brush ml-1" data-toggle="tooltip" title="This pet has custom art."></i>
                                        @endif
                                        @if ($pet->pivot->character_id)
                                            <span data-toggle="tooltip" title="Attached to a character."><i class="fas fa-link ml-1"></i></span>
                                        @endif
                                        @if ($pet->pivot->evolution_id)
                                            <span data-toggle="tooltip" title="This pet has evolved. Stage
                                            {{ $pet->evolutions->where('id', $pet->pivot->evolution_id)->first()->evolution_stage }}."><i
                                                    class="fas fa-angle-double-up ml-1"></i>
                                            </span>
                                        @endif
                                    </a>
                                </div>
                                @if ($stackName)
                                    <div>
                                        <span class="text-light badge badge-dark" style="font-size:95%;">
                                            {{ $stackName }}
                                        </span>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </div>
        </div>
    @endforeach


    <h3>Latest Activity</h3>
    <table class="table table-sm">
        <thead>
            <th>Sender</th>
            <th>Recipient</th>
            <th>Pet</th>
            <th>Log</th>
            <th>Date</th>
        </thead>
        <tbody>
            @foreach ($logs as $log)
                @include('user._pet_log_row', ['log' => $log, 'user' => $user])
            @endforeach
        </tbody>
    </table>
    <div class="text-right">
        <a href="{{ url($user->url . '/pet-logs') }}">View all...</a>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            $('.inventory-stack').on('click', function(e) {
                e.preventDefault();
                var $parent = $(this).parent().parent();
                loadModal("{{ url('pets') }}/" + $parent.data('id'), $parent.data('name'));
            });
        });
    </script>
@endsection
