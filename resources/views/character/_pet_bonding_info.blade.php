<div class="col-md-12 mb-2">
    <div class="card d-flex flex-row align-items-center p-2 px-3">
        <div class="col-md-4">
            <img src="{{ $pet->pet->image($pet->id) }}" class="rounded img-fluid" />
        </div>
        <div class="col-md-8">
            @if ($pet->pet_name)
                <span class="text-light badge badge-dark mb-2 p-2" style="font-size:95%;">
                    {!! $pet->pet_name !!}
                </span> the
            @endif
            {!! $pet->pet->displayName !!}

            <div>
                Level {{ $pet->level?->levelName }}
            </div>
        </div>
    </div>
</div>
