<div class="card p-3 mb-2">
    <h3>Staff Profile</h3>
    {!! Form::open(['url' => $adminView ? 'admin/users/'.$user->name.'/staff-profile' : 'account/staff-profile']) !!}
        <div class="form-group">
            {!! Form::label('text', 'Staff Profile Text') !!} {!! add_help('This is the short profile that will display on the team page. This is a text-only field, meaning no HTML.') !!}
            <p class="small float-right">Maximumn of 250 characters.</p>
            {!! Form::textarea('text', $user->staffProfile ? $user->staffProfile->text : null, ['class' => 'form-control', 'maxlength' => 250]) !!}
        </div>
        <div class="text-right">
            {!! Form::submit('Edit', ['class' => 'btn btn-primary']) !!}
        </div>
    {!! Form::close() !!}
</div>
<div class="card p-3 mb-2">
    <h3>Staff-Profile Contacts</h3>
    <p>These are the websites {{ $adminView ? $user->name .' wishes for users to contact them at' : 'you wish for users to contact you at' }}, should they need moderation assistance.</p>
    {!! Form::open(['url' => $adminView ? 'admin/users/'.$user->name.'/staff-links' : 'account/staff-links']) !!}
        @include('widgets._website_links')
    {!! Form::close() !!}
        @include('widgets._website_link_row')
</div>