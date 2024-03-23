<div class="submission-character mb-3 card">
    <div class="card-body">
        <div class="text-right"><a href="#" class="remove-character text-muted"><i class="fas fa-times"></i></a></div>
        <div class="row">
            <div class="col-md-2 align-items-stretch d-flex">
                <div class="d-flex text-center align-items-center">
                    <div class="character-image-blank hide">Enter character code.</div>
                    <div class="character-image-loaded">
                        @include('home._character', ['character' => $character->character])
                    </div>
                </div>
            </div>
            <div class="col-md-10">
                <a href="#" class="float-right fas fa-close"></a>
                <div class="form-group">
                    {!! Form::label('slug[]', 'Character Code') !!}
                    {!! Form::text('slug[]', $character->character->slug, ['class' => 'form-control character-code']) !!}
                </div>
            </div>
        </div>
    </div>
</div>