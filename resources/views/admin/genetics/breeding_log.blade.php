@extends('admin.layout')

@section('admin-title') Breeding Log @endsection

@section('admin-content')
{!! breadcrumbs(['Admin Panel' => 'admin', 'Genetics' => 'admin/genetics/genes', 'Breeding Logs' => 'admin/genetics/logs', 'Log #'.$log->id => 'admin/genetics/logs/breeding/'.$log->id]) !!}

<h1 class="mb-0">Breeding Log #{{ $log->id }}</h1>
<p>Rolled by {!! $log->user->displayName !!} {!! pretty_date($log->rolled_at) !!}</p>

<p class="alert alert-info">The breeding roller will not automatically assign traits! Make sure you have assigned traits to the generated myo slots as needed.</p>
<hr>

<div class="row justify-content-center no-gutters">
    <div class="col-12">
        <h3 class="text-center">Parents</h3>
        @if($log->parents->count() > 0)
            <div class="row justify-content-center">
                @foreach ($log->parents as $parent)
                    <div class="col-md-3 col-6 text-center mb-3">
                        <div>
                            <a href="{{ $parent->character->url }}"><img src="{{ $parent->character->image->thumbnailUrl }}" class="img-thumbnail" /></a>
                        </div>
                        <div class="mt-1">
                            <a href="{{ $parent->character->url }}" class="h5 mb-0"> @if(!$parent->character->is_visible) <i class="fas fa-eye-slash"></i> @endif {{ $parent->character->fullName }}</a>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-center mb-3">This litter has unknown parentage.</p>
        @endif
        <hr class="mt-0">
    </div>
    <div class="col-12 col-md-8 pr-md-4">
        <h3 class="text-center">Children</h3>
        @if($log->children->count() > 0)
            <div class="row justify-content-center">
                @foreach ($log->children as $child)
                    <div class="col-6 col-xl-4 text-center mb-3">
                        <div>
                            <a href="{{ $child->character->url }}"><img src="{{ $child->character->image->thumbnailUrl }}" class="img-thumbnail" /></a>
                        </div>
                        <div class="mt-1">
                            <a href="{{ $child->character->url }}" class="h5 mb-0"> @if(!$child->character->is_visible) <i class="fas fa-eye-slash"></i> @endif {{ $child->character->fullName }}</a>
                            @if($child->twin)<br>Twin of {!! $child->twin->displayName !!}@endif
                            @if($child->chimerism)<br><strong>Chimera</strong>@endif
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-center">There were no children produced.</p>
        @endif
        <div class="d-md-none">
            <hr>
        </div>
    </div>
    <div class="col-12 col-sm-8 col-md-4">
        <h3 class="text-center">Settings</h3>
        @if($log->roller_settings)
            <div class="card mb-3">
                <ul class="list-group list-group-flush">
                    @foreach ($log->roller_settings as $key => $setting)
                        <li class="list-group-item d-flex w-100">
                            <div class="mr-auto h5 mb-0">{{ Str::title(str_replace('_', ' ', $key)) }}</div>
                            <div class="ml-auto h5 mb-0">{{ $setting .( ($key == "chimera_chance" || $key == "twin_chance") ? "%" : "") }}</div>
                        </li>
                    @endforeach
                </ul>
            </div>
        @else
            <p>Couldn't find a log of the settings used for this breeding roll...</p>
        @endif
    </div>
</div>
@endsection

@section('scripts')
@parent
<script>
    $(document).ready(function() {

    });
</script>
@endsection
