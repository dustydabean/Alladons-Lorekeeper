{!! Form::open(['url' => 'admin/'.($isMyo ? 'myo/'.$character->id : 'character/'.$character->slug).'/genome/'.$genome->id.'/delete']) !!}
    <p>This will delete the entire genome seen below. <strong>This data will not be retrievable.</strong></p>
    <p>Are you sure you want to do this?</p>

    <h5>Genome</h5>
    @include('character._genes', ['genome' => $genome, 'buttons' => false])

    <div class="text-right">
        {!! Form::submit('Delete', ['class' => 'btn btn-danger']) !!}
    </div>
{!! Form::close() !!}
