<div class="alert alert-info">
    <i class="fas fa-info-circle"></i> Drops every {{ $pet->dropData->interval }}.
</div>
@if (!$pet->dropData->override)
    <h2 class="h4">Base Pet Drops</h2>
    <div class="card p-3">
        @foreach ($pet->dropData->parameters as $label => $group)
            @if (isset($pet->dropData->rewards(true)[strtolower(str_replace(' ', '_', $label))]))
                <h4 class="h5">{{ ucwords(str_replace('_', ' ', $label)) }}</h4>
                <table class="table table-sm category-table">
                    <thead>
                        <tr>
                            <th width="70%">Reward</th>
                            <th>Quantity</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pet->dropData->rewards(true)[strtolower(str_replace(' ', '_', $label))] as $reward)
                            <tr>
                                <td>
                                    @php
                                        $reward_object = $reward->rewardable_type::find($reward->rewardable_id);
                                    @endphp
                                    @if ($reward_object->has_image)
                                        <div>
                                            <img class="img-fluid" style="max-height: 10em;" src="{{ $reward_object->imageUrl }}">
                                        </div>
                                    @endif
                                    {!! $reward_object->displayName !!}
                                </td>
                                <td>
                                    Between {{ $reward->min_quantity . ' and ' . $reward->max_quantity }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        @endforeach
    </div>
@endif
