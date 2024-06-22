<div class="alert alert-info mt-2">
    <i class="fas fa-info-circle"></i> Drops every {{ $pet->dropData->interval }}.
</div>
@if (!$pet->dropData->override)
    <h2 class="h4">Base Pet Drops</h2>
    <div class="card p-3 mb-3">
        @foreach ($pet->dropData->parameters as $label => $group)
            @if (isset($pet->dropData->rewards(true)[strtolower($label)]))
                <h4 class="h5">{{ $label }}</h4>
                <table class="table table-sm category-table">
                    <thead>
                        <tr>
                            <th width="70%">Reward</th>
                            <th>Quantity</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pet->dropData->rewards(true)[strtolower($label)] as $reward)
                            <tr>
                                <td>
                                    @php $reward_object = $reward->rewardable_type::find($reward->rewardable_id); @endphp
                                    @if ($reward_object->has_image)
                                        <img class="img-fluid" style="max-height: 10em;" src="{{ $reward_object->imageUrl }}"><br />
                                    @endif
                                    {!! $reward_object->displayName !!}
                                </td>
                                <td>Between {{ $reward->min_quantity . ' and ' . $reward->max_quantity }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        @endforeach
    </div>
@endif
@if ($pet->variants()->has('dropData')->get()->count())
    <h2 class="h4">Variant Drops</h2>
    <div class="card card-body my-2 mb-4">
        <table class="table table-sm">
            <thead>
                <tr>
                    <th width="50%">Variant</th>
                    <th width="50%">Rewards</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($pet->variants()->has('dropData')->get() as $variant)
                    <tr id="variant-{{ $variant->id }}">
                        <td>{{ $variant->variant_name }}</td>
                        <td>
                            @if ($variant->dropData->rewards())
                                @foreach ($variant->dropData->rewardString() as $label => $string)
                                    {!! '<b>' . ucfirst($label) . ':</b> ' . implode(', ', $string) . ($loop->last ? '' : '<br />') !!}
                                @endforeach
                            @else
                                <i>No rewards set.</i>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif
