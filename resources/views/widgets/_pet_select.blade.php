<h3>Your Pets <a class="small pet-collapse-toggle collapse-toggle" href="#userPet" data-toggle="collapse">Show</a></h3>
<div class="card mb-3 collapse show" id="userPet">
    <div class="card-body">
        <div class="text-right mb-3">
            <div class="d-inline-block">
                {!! Form::label('item_category_id', 'Filter:', ['class' => 'mr-2']) !!}
                <select class="form-control d-inline-block w-auto" id="userItemCategory">
                    <option value="all">All Categories</option>
                    <option value="selected">Selected Items</option>
                    <option disabled>&#9472;&#9472;&#9472;&#9472;&#9472;&#9472;&#9472;&#9472;&#9472;&#9472;</option>
                    <option value="0">Miscellaneous</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="d-inline-block">
                {!! Form::label('item_category_id', 'Action:', ['class' => 'ml-2 mr-2']) !!}
                <a href="#" class="btn btn-primary pet-select-all">Select All Visible</a>
                <a href="#" class="btn btn-primary pet-clear-selection">Clear Visible Selection</a>
            </div>
        </div>
        <div id="userItems" class="user-items">
            <div class="row">
                @foreach ($pet as $item)
                    <div class="col-lg-2 col-sm-3 col-6 mb-3 user-item category-all category-{{ $item->item->item_category_id ?: 0 }} {{ isset($selected) && in_array($item->id, $selected) ? 'category-selected' : '' }}" data-id="{{ $item->id }}"
                        data-name="{{ $user->name }}'s {{ $item->item->name }}">
                        <div class="text-center pet-item">
                            <div class="mb-1">
                                <a class="pet-stack"><img src="{{ $item->item->imageUrl }}" /></a>
                            </div>
                            <div>
                                <a class="pet-stack pet-stack-name">{{ $item->item->name }}</a>
                                {!! Form::checkbox(isset($fieldName) && $fieldName ? $fieldName : 'stack_id[]', $item->id, isset($selected) && in_array($item->id, $selected) ? true : false, ['class' => 'pet-checkbox hide']) !!}
                            </div>
                            <div>
                                <a href="#" class="btn btn-xs btn-outline-info pet-info">Info</a>

                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
