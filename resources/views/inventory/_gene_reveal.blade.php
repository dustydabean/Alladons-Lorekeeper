<li class="list-group-item">
    <a class="card-title h5 collapse-title"  data-toggle="collapse" href="#genomeReveal"> Reveal Genome</a>
    <div id="genomeReveal" class="collapse">
        {!! Form::hidden('tag', $tag->tag) !!}
        <p>This action is not reversible. Are you sure you want to reveal a genome?</p>
        <div class="form-group">
            {!! Form::select('genome_id', $tag->service->getPossibleGenomes($tag, Auth::user()), null, ['class' => "form-control"]) !!}
            <span class="text-muted form-text">
                {{ $tag->getData()['reveal_strength'] ? "This item will fully reveal a genome." : "This item will half-reveal a hidden genome, and fully-reveal a half-hidden genome." }}
                {{ $tag->getData()['fully_hidden_only'] ? "It can only be used on fully hidden genomes." : "" }}
            </span>
        </div>
        <div class="text-right">
            {!! Form::button('Reveal', ['class' => 'btn btn-primary', 'name' => 'action', 'value' => 'act', 'type' => 'submit']) !!}
        </div>
    </div>
</li>
