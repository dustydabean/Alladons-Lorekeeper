<div class="hide">
    <table>
        <tbody id="linkRow">
            <tr class="link-row">
                <td>{!! Form::text('site[]', null, ['class' => 'form-control', 'maxlength' => 50]) !!}</td>
                <td>{!! Form::text('url[]', null, ['class' => 'form-control', 'maxlength' => 50]) !!}</td>
                <td class="text-right"><a href="#" class="btn btn-danger remove-link-button">Remove</a></td>
            </tr>
        </tbody>
    </table>
</div>