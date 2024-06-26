{!! Form::open(['url' => 'activities/' . $activity->id . '/act']) !!}

{{-- Default rewards are only default if we're not choosing --}}
@if (!$activity->data->choose_reward)
    <h3>Submission Approval will Reward:</h3>
    <table class="table table-sm mb-0">
        <thead>
            <tr>
                <th width="70%">Reward</th>
                <th width="30%">Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($activity->data->prompt->rewards as $reward)
                <tr>
                    <td>{!! $reward->reward->displayName !!}</td>
                    <td>{{ $reward->quantity }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endif

<h3 class="mt-4">Submission Entry</h3>
<div class="form-group">
    {!! Form::label('comments', 'Submission') !!}
    {!! Form::textarea('comments', $activity->data->template ?? null, ['class' => 'form-control wysiwyg']) !!}
</div>

@if ($activity->data->choose_reward)
    <div class="form-group">
        {!! Form::label('choose_reward', 'Choose your reward') !!}
        @php
            $rewards = $activity->data->prompt->rewards->mapWithKeys(function ($reward, $index) {
                return [$index => $reward->reward->name . ' (x' . $reward->quantity . ')'];
            });
        @endphp
        {!! Form::select('choose_reward', $rewards, null, ['class' => 'form-control', 'placeholder' => 'Select Reward']) !!}
    </div>
@endif

@if ($activity->data->show_rewards)
    <h2>Rewards</h2>
    <p>Select the rewards you would like to claim with this submission</p>
    @include('widgets._loot_select', ['loots' => null, 'showLootTables' => false, 'showRaffles' => false])
@endif


<div class="text-right">
    {!! Form::submit('Submit', ['class' => 'btn btn-primary']) !!}
</div>

{!! Form::close() !!}

@if ($activity->data->show_rewards)
    @include('widgets._loot_select_row', ['items' => $items, 'currencies' => $currencies, 'showLootTables' => false, 'showRaffles' => false])
    @include('js._loot_js', ['showLootTables' => false, 'showRaffles' => false])
@endif


<style>
    .tox-tinymce {
        max-height: 300px;
    }
</style>
