<div class="card mb-4">
    <div class="card-body">
        <div class="row">
            @if(!isset($isCharacter) || !$isCharacter)
                <div class="col-2 col-md-3">
                    <img src="{{ $character->image->thumbnailUrl }}" alt="{{ $character->fullName }} Thumbnail" class="img-thumb mw-100" />
                </div>
            @endif
            <div class="{{ isset($isCharacter) && $isCharacter ? 'col-12 col-md-12' : 'col-10 col-md-9' }}">
                <h5>
                    @if(Auth::check() && (Auth::user()->id == $permission->recipient_id || Auth::user()->hasPower('manage_characters')) && !$permission->is_used)
                        <div class="float-right">
                            @if(Auth::user()->id == $permission->recipient_id || Auth::user()->hasPower('manage_characters'))
                                <a href="#" class="btn btn-sm btn-primary transfer-breeding-permission" data-id="{{ $permission->id }}" data-slug="{{ $character->slug }}">{{ Auth::user()->id != $permission->recipient_id && Auth::user()->hasPower('manage_characters') ? '(Admin)' : '' }} Transfer</a>
                            @endif
                            @if(Auth::user()->hasPower('manage_characters'))
                                <a href="#" class="btn btn-sm btn-warning use-breeding-permission" data-id="{{ $permission->id }}" data-slug="{{ $character->slug }}">(Admin) Mark Used</a>
                            @endif
                        </div>
                    @endif
                    Breeding Permission #{{ $permission->id }}
                    @if($permission->is_used)
                        (Used)
                    @endif
                    @if(!isset($isCharacter) || !$isCharacter)
                        ・ {!! $character->displayName !!}
                    @endif
                    <small>
                        <br/>
                        @if(!isset($isCharacter) || $isCharacter)
                            Granted to: {!! $permission->recipient->displayName !!} ・
                        @endif
                        Type: {{ $permission->type }}
                    </small>
                </h5>

                @if($permission->description)
                    <strong>Notes:</strong>
                    <p>
                        {!! nl2br(htmlentities($permission->description)) !!}
                    </p>
                @else
                    <p><i>No notes provided.</i></p>
                @endif

                <h5>
                    History
                    <a class="small permission-collapse-toggle collapse-toggle collapsed" href="#logs-{{ $permission->id }}" data-toggle="collapse">Show</a>
                </h5>

                <div class="card-body permission-body collapse" id="logs-{{ $permission->id }}">
                    @php
                        $logs = $permission->getOwnershipLogs()
                    @endphp

                    <div class="row ml-md-2 mb-4">
                        <div class="d-flex row flex-wrap col-12 mt-1 pt-1 px-0 ubt-bottom">
                            <div class="col-6 col-md font-weight-bold">Sender</div>
                            <div class="col-6 col-md font-weight-bold">Recipient</div>
                            <div class="col-6 col-md-4 font-weight-bold">Log</div>
                            <div class="col-6 col-md font-weight-bold">Date</div>
                        </div>
                        @foreach($logs as $log)
                            <div class="d-flex row flex-wrap col-12 mt-1 pt-1 px-0 ubt-top">
                                <div class="col-6 col-md">{!! $log->sender ? $log->sender->displayName : '' !!}</div>
                                <div class="col-6 col-md">{!! $log->recipient ? $log->recipient->displayName : '' !!}</div>
                                <div class="col-6 col-md-4">{!! $log->log !!}</div>
                                <div class="col-6 col-md">{!! pretty_date($log->created_at) !!}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
