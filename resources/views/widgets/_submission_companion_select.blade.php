@php
    $isAdmin = isset($isAdmin) && $isAdmin;
    if ($isAdmin) {
        $submitter = $submission->user;
        $userPets = \App\Models\User\UserPet::where('user_id', $submitter->id)
            ->whereNull('deleted_at')
            ->with(['pet', 'pet.category', 'variant', 'evolution'])
            ->orderBy('sort', 'DESC')
            ->get();
        $selectedPetIds = $submission->submissionPetIds;
    } else {
        $userPets = \App\Models\User\UserPet::where('user_id', Auth::user()->id)
            ->whereNull('deleted_at')
            ->with(['pet', 'pet.category', 'variant', 'evolution'])
            ->orderBy('sort', 'DESC')
            ->get();
        $selectedPetIds = isset($selectedPets) ? $selectedPets : [];
    }
    $petCategories = \App\Models\Pet\PetCategory::orderBy('sort', 'DESC')->get();
@endphp

<div id="companionSelectSection">
    @if($userPets->count())
        <div class="d-flex justify-content-between align-items-center mb-2">
            <div class="d-flex align-items-center">
                <select class="form-control form-control-sm d-inline-block w-auto mr-2" id="companionCategoryFilter">
                    <option value="all">All Categories</option>
                    @foreach ($petCategories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
                {!! Form::text(null, null, ['class' => 'form-control form-control-sm', 'id' => 'companionSearch', 'placeholder' => 'Search by name, species, or ID...', 'style' => 'width:220px;']) !!}
            </div>
            <div>
                <strong id="companionCount">{{ count($selectedPetIds) }}/10 selected</strong>
            </div>
        </div>
        <div id="companionGrid" class="row" style="max-height:300px; overflow-y:auto;">
            @foreach ($userPets as $userPet)
                @php $isSelected = in_array($userPet->id, $selectedPetIds); @endphp
                <div class="col-md-2 col-sm-3 col-4 mb-2 companion-entry category-{{ $userPet->pet->pet_category_id ?: 0 }}"
                    data-id="{{ $userPet->id }}" data-category="{{ $userPet->pet->pet_category_id ?: 0 }}"
                    data-search="{{ strtolower($userPet->id . ' ' . ($userPet->pet_name ?? '') . ' ' . ($userPet->pet->name ?? '')) }}">
                    <div class="companion-box text-center p-1 rounded border {{ $isSelected ? 'border-primary companion-selected' : '' }}" style="cursor:pointer;">
                        <img src="{{ $userPet->pet->VariantImage($userPet->id) }}" class="img-fluid rounded" style="max-height: 60px; max-width: 60px;" alt="{{ $userPet->pet->name }}" />
                        <div class="small text-truncate" title="{{ $userPet->selectName }}">
                            {{ $userPet->selectName }}
                        </div>
                        {!! Form::checkbox('pet_id[]', $userPet->id, $isSelected, ['class' => 'companion-checkbox d-none']) !!}
                        @if ($isAdmin)
                            <div class="companion-exp-field mt-1 d-flex align-items-center justify-content-center {{ $isSelected ? '' : 'd-none' }}">
                                {!! Form::number('pet_exp[' . $userPet->id . ']', 0, ['class' => 'form-control form-control-sm companion-exp-input', 'style' => 'width:60px; display:inline-block;', 'placeholder' => '0', 'min' => 0]) !!}
                                <span class="small ml-1">EXP</span>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <p class="text-muted">{{ $isAdmin ? 'This user does not own any companions.' : "You don't own any companions." }}</p>
    @endif
</div>
