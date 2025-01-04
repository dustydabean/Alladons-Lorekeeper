@extends('admin.layout')

@section('admin-title')
    Criteria Rewards
@endsection

@section('admin-content')
    {!! breadcrumbs(['Admin Panel' => 'admin', 'Criteria' => 'admin/data/criteria']) !!}
    <p>
        These are currency criteria rewards that can be used with prompts, claims, and gallery submissions in-place of static reward amounts.
    </p>

    <h2 class="mt-5">Criteria Rewards <a href="{{ url('admin/data/criteria/create') }}" class="btn btn-primary float-right"><i class="fas fa-plus"></i> Create Criterion</a></h2>
    <p>Drag and Drop the cards to re-order your steps. Steps that are inactive will not be shown or included in the final calculation.</p>
    <div>
        @foreach ($criteria as $criterion)
            <div class="card p-3 mb-2 pl-0">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="flex-grow-1">
                        <h4 class="pb-0 mb-0">
                            @if ($criterion->is_active === 0)
                                <i class="fas fa-eye-slash"></i>
                            @else
                                <i class="fas fa-eye"></i>
                            @endif
                            {{ $criterion->name }}
                        </h4>
                        <span class="text-secondary">{{ $criterion->summary }}</span>
                    </div>
                    <div>
                        <a href="{{ url('admin/data/criteria/edit/' . $criterion->id) }}" class="btn btn-info text-white mr-2"><i class="fas fa-pencil-alt"></i></a>
                        <button class="btn btn-danger delete-button" data-id="{{ $criterion->id }}"><i class="fas fa-trash"></i></button>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection



@section('scripts')
    @parent
    <script>
        $(document).ready(function() {
            $('.delete-button').on('click', function(e) {
                e.preventDefault();
                var id = $(this).attr('data-id');
                loadModal("{{ url('admin/data/criteria/delete') }}/" + id, 'Delete Criterion');
            });
        });
    </script>
@endsection
