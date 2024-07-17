
@if($collection->parent_id && Auth::check() && !Auth::user()->hasCollection($collection->id))     
@php 
                if(Auth::check()) $completed = DB::table('user_collections')->where('user_id', Auth::user()->id)->where('collection_id', $collection->parent_id)->count();    
            @endphp     
    @if(!$completed)
        <div class="col-md-3 px-1 mb-2 collectionnotunlocked">
            <div class="card alert-secondary rounded-0 py-0 col-form-label" data-id="{{ $collection->id }}" data-name="{{ $collection->name }}">
                <div class="p-2 row">
                    <div class="col">
                        @if(isset($collection->image_url))
                            <img src="{{ $collection->imageUrl }}" class="collection-image mr-2" style="max-height:15px; width:auto;">
                        @endif
                        <h4 class="mb-0 mt-0 d-inline col-form-label">{!! $collection->displayName !!}</h4>
                    </div>
                    <div class="col-auto mx-2 text-right"><a class="btn btn-sm ml-2 btn-craft w-100"><i class="fas fa-lock" style="opacity:0.5" data-toggle="tooltip" title="You haven't unlocked this collection."></i></a></div>
                    
                </div>
            </div>
        </div>
    @elseif($completed)
        <div class="col-md-3 px-1 mb-2">
            <div class="card alert-secondary rounded-0 py-0 col-form-label" data-id="{{ $collection->id }}" data-name="{{ $collection->name }}">
                <div class="p-2 row">
                    <div class="col">
                        @if(isset($collection->image_url))
                            <img src="{{ $collection->imageUrl }}" class="collection-image mr-2" style="max-height:15px; width:auto;">
                        @endif
                        <h4 class="mb-0 mt-0 d-inline col-form-label">{!! $collection->displayName !!}</h4>
                    </div>
                    @if(Auth::check() && Auth::user()->hasCollection($collection->id))
                    <div class="col-auto mx-2 text-right"><a class="btn btn-secondary btn-sm ml-2 btn-craft w-100" style="line-height:1;" href="">View</a></div>
                    @else
                    <div class="col-auto mx-2 text-right"><a class="btn btn-secondary btn-sm ml-2 btn-craft w-100" style="line-height:1;" href="">Complete</a></div>
                    @endif
                </div>
            </div>
        </div>
    @endif
@else
    <div class="col-md-3 px-1 mb-2">
            <div class="card alert-secondary rounded-0 py-0 col-form-label" data-id="{{ $collection->id }}" data-name="{{ $collection->name }}">
                <div class="p-2 row">
                    <div class="col">
                        @if(isset($collection->image_url))
                            <img src="{{ $collection->imageUrl }}" class="collection-image mr-2" style="max-height:15px; width:auto;">
                        @endif
                        <h4 class="mb-0 mt-0 d-inline col-form-label">{!! $collection->displayName !!}</h4>
                    </div>
                    @if(Auth::check() && Auth::user()->hasCollection($collection->id))
                    <div class="col-auto mx-2 text-right"><a class="btn btn-secondary btn-sm ml-2 btn-craft w-100" style="line-height:1;" href="">View</a></div>
                    @else
                    <div class="col-auto mx-2 text-right"><a class="btn btn-secondary btn-sm ml-2 btn-craft w-100" style="line-height:1;" href="">Complete</a></div>
                    @endif
                </div>
            </div>
        </div>
@endif

