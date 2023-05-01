<table class="table table-sm">
    <thead>
        <tr>
            <th width="35%" colspan="2">Website</th>
            <th width="65%">URL</th>
            <th width="10%"></th>
        </tr>
    </thead>
    <tbody id="linkTable" class="sortable">
        @if(isset($links->contacts))
            @foreach($links->contacts['site'] as $key=>$contact)
                <tr class="link-row">
                    <td>
                        <a class="fas fa-arrows-alt-v handle mr-3" href="#"></a>
                    </td>
                    <td>
                        {!! Form::text('site[]', $contact, ['class' => 'form-control', 'maxlength' => 50]) !!}
                    </td>
                    <td>
                        {!! Form::text('url[]', $links->contacts['url'][$key], ['class' => 'form-control', 'maxlength' => 50]) !!}
                    </td>
                    <td class="text-right">
                        <a href="#" class="btn btn-danger remove-link-button">Remove</a>
                    </td>
                </tr>
            @endforeach
        @endif
    </tbody>
</table>
<div class="mb-3">
    <a href="#" class="btn btn-outline-info" id="addLink">Add Website Link</a>
    {!! Form::submit('Save Links', ['class' => 'float-right btn btn-primary']) !!}
</div>