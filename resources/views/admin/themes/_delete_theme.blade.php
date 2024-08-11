@if ($theme)
    {!! Form::open(['url' => 'admin/themes/delete/' . $theme->id]) !!}

    <p>
        You are about to delete the theme titled <strong>{{ $theme->name }}</strong>. This is not reversible.
    </p>
    @if ($theme->userCount)
        <p>
            The {{ $theme->userCount }} user{{ $theme->userCount == 1 ? ' who is' : 's who are' }} using this theme will be automatically changed to the default theme {!! isset($theme) ? '<strong>' . $theme->name . '</strong>.' : 'which is not currently set.' !!}
        </p>
    @endif
    @if ($theme->is_default)
        <p>
            This is currently the default theme. Any visitor or user using the default theme will instead look at your website as if it didn't have the Theme Manager installed.
        </p>
    @endif
    <p>Are you sure you want to delete <strong>{{ $theme->name }}</strong>?</p>

    <div class="text-right">
        {!! Form::submit('Delete Theme', ['class' => 'btn btn-danger']) !!}
    </div>

    {!! Form::close() !!}
@else
    Invalid post selected.
@endif
