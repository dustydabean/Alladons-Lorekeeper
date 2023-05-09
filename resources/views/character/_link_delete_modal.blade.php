{{-- Delete Model --}}
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Delete this link?</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                You will have to make a new request to get back the link. All data is non-retrievable.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
                {!! Form::open(['url' => $character->url .'/links/delete/'.$link->id]) !!}
                {!! Form::hidden('chara_1', $character->id) !!}
                {!! Form::hidden('chara_2', $link->chara_2) !!}
                {!! Form::button('<i class="fas fa-trash"></i> Delete', ['class' => 'btn btn-danger btn-sm m-1', 'type' => 'submit']) !!}
                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>