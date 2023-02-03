@extends('account.layout')

@section('account-title') Links @endsection

@section('account-content')
{!! breadcrumbs(['My Account' => Auth::user()->url, 'Aliases' => 'account/aliases']) !!}

<h1>Aliases</h1>

<p>To verify your identity, you can authenticate any number of accounts from these social media sites that you own. Characters credited to these social media accounts will be added to your account.</p>
<p>You must have a <strong>primary</strong> account linked. This account is displayed on your profile, and cannot be hidden and/or removed - to change it, you will need to change <strong>primary</strong> status to a different account. Non-primary linked accounts can be hidden and/or removed at any time.</p>

<h3>Linked Accounts</h3>
<div class="row ml-md-2 mb-5">
    <div class="d-flex row flex-wrap col-12 mt-1 pt-1 px-0 ubt-bottom">
        <div class="col-3 font-weight-bold">Alias</div>
        <div class="col-3 font-weight-bold">Site</div>
        <div class="col-1 font-weight-bold"></div>
        <div class="col-5 font-weight-bold"></div>
    </div>
    @foreach(Auth::user()->aliases()->orderBy('is_primary_alias', 'DESC')->orderBy('site')->get() as $alias)
        <div class="d-flex row flex-wrap col-12 mt-1 pt-1 px-0 ubt-top">
            <div class="col-3"><a href="{{ $alias->url }}">{{ $alias->alias }}</a></div>
            <div class="col-3"><i class="{{ $alias->config['icon'] }} fa-fw mr-1"></i> {{ Config::get('lorekeeper.sites.'.$alias->site.'.full_name') }}</div>
            <div class="col-1">
                @if($alias->is_primary_alias) <span class="badge badge-success">Primary</span> @endif
                @if(!$alias->is_visible) <i class="fas fa-eye-slash" data-toggle="tooltip" title="This alias is hidden from public view."></i> @endif
            </div>
            <div class="col-5 text-right">
                @if(!$alias->is_primary_alias)
                    @if(Config::get('lorekeeper.sites.'.$alias->site.'.primary_alias')) 
                        <a href="#" class="btn btn-outline-primary btn-sm make-primary" data-id="{{ $alias->id }}">Make Primary</a>
                    @endif
                    <a href="#" class="btn btn-outline-secondary btn-sm hide-alias" data-id="{{ $alias->id }}">{{ $alias->is_visible ? 'Hide' : 'Unhide' }}</a>
                    <a href="#" class="btn btn-outline-danger btn-sm remove-alias" data-id="{{ $alias->id }}">Remove</a>
                @endif
            </div>
        </div>
    @endforeach
</div>

<h3>Link New Account</h3>

<p>Click on a button to link a social media account to your {{ config('lorekeeper.settings.site_name', 'Lorekeeper') }} account. You must be logged into the account you want to link to be able to continue.</p>
<p>Accounts on sites that have the <strong>Primary</strong> label can be used as a primary account, but will not automatically switch your primary account once authenticated. Added accounts are not visible on your profile by default.</p>

@foreach(Config::get('lorekeeper.sites') as $provider => $site)
    @if(isset($site['auth']) && $site['auth'])
        <div class="d-flex mb-3">
            <div class="d-flex justify-content-end align-items-center"><i class="{{ $site['icon'] }} fa-fw mr-3"></i></div>
            <div class="">
                <a href="{{ url('auth/redirect/'.$provider) }}" class="btn btn-outline-primary mr-3">Link <strong>{{ $site['full_name'] }}</strong> Account</a> 
                @if(isset($site['primary_alias']) && $site['primary_alias'])<span class="badge badge-success">Primary</span>@endif
            </div>
        </div>
    @endif
@endforeach

@endsection
@section('scripts')
<script>
    $('.make-primary').on('click', function(e) {
        e.preventDefault();
        loadModal("{{ url('account/make-primary') }}/" + $(this).data('id'), 'Make Primary Alias');
    });
    $('.hide-alias').on('click', function(e) {
        e.preventDefault();
        loadModal("{{ url('account/hide-alias') }}/" + $(this).data('id'), 'Alias Visibility');
    });
    $('.remove-alias').on('click', function(e) {
        e.preventDefault();
        loadModal("{{ url('account/remove-alias') }}/" + $(this).data('id'), 'Remove Alias');
    });
</script>
@endsection