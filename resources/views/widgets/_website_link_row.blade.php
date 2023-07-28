<div class="hide">
    <table>
        <tbody id="linkRow">
            <tr class="link-row">
                <td>
                    <a class="fas fa-arrows-alt-v handle mr-3" href="#"></a>
                </td>
                <td>{!! Form::text('site[]', null, ['class' => 'test form-control', 'maxlength' => 50]) !!}</td>
                <td>{!! Form::text('url[]', null, ['class' => 'form-control', 'maxlength' => 50]) !!}</td>
                <td class="text-right"><a href="#" class="btn btn-danger remove-link-button">Remove</a></td>
            </tr>
        </tbody>
    </table>
</div>