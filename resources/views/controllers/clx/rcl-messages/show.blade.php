@extends('_layouts.main')
@section('page')
    <div class="container">
        <form method="POST" action="{{ route('controllers.clx.transmit', $message) }}">
            @csrf
            <p class="mb-1">
                <a class="icon-link icon-link-hover" href="{{ route('controllers.clx.pending') }}">
                    <i class="fa-solid fa-chevron-left"></i>
                    Pending
                </a>
            </p>
            <p class="mb-4">
                <a class="icon-link icon-link-hover" href="{{ route('controllers.clx.pending') }}">
                    <i class="fa-solid fa-chevron-left"></i>
                    Processed
                </a>
            </p>
            <h5 class="text-secondary font-display">Request Message</h5>
            <h3 class="font-display">{{ $message->callsign }} to {{ $message->destination }} {{ $message->is_concorde ? '(Concorde)' : '' }}</h3>
            @if ($message->isEditLocked() && $message->editLockVatsimAccount != Auth::user())
                <div class="alert alert-warning">
                    <span>{{ $message->editLockVatsimAccount->full_name }} {{ $message->editLockVatsimAccount->id }} is editing this as of {{ $message->edit_lock_time->diffForHumans() }}.</span>
                </div>
            @endif
            @if ($message->re_request)
                <div class="alert alert-warning">
                    <span>This is a re-request.</span>
                </div>
            @endif
            @if ($message->is_acknowledged)
                RCL has been auto acknowledged.
            @endif
            @if ($errors->any())
                <div class="alert alert-danger" role="alert">
                    <p>Some input was incorrect.</p>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <div class="row mt-4">
                <div class="col-md">
                    <div>
                        <h5>Request Data</h5>
                        <table class="table table-bordered table-sm">
                            <thead>
                            </thead>
                            <tbody>
                            <tr>
                                <td>CS</td>
                                <td>{{ $message->callsign }}</td>
                            </tr>
                            <tr>
                                <td>DEST</td>
                                <td>{{ $message->destination }}
                            </tr>
                            <tr>
                                <td>TRACK/RR</td>
                                <td>{{ $message->track ? 'NAT '. $message->track->identifier . ' ' . $message->track->last_routeing : $message->random_routeing }}</td>
                            </tr>
                            <tr>
                                <td>ENTRY</td>
                                <td>{{ $message->entry_fix }}</td>
                            </tr>
                            <tr>
                                <td>ETA</td>
                                @if ($message->new_entry_time)
                                    <td>
                                        <span class="badge rounded-pill text-bg-primary" style="font-size: 13px">{{ $message->entry_time }}</span> - <span class="fst-italic">prev {{ $message->previous_entry_time }} - notified at {{ $message->new_entry_time_notified_at->format('Hi') }}</span>
                                    </td>
                                @else
                                    <td>{{ $message->entry_time }}</td>
                                @endif
                            </tr>
                            <tr>
                                <td>FL</td>
                                <td>{{ $message->flight_level }}</td>
                            </tr>
                            <tr>
                                @if ($message->is_concorde)
                                    <td>UFL</td>
                                    <td>{{ $message->upper_flight_level }}</td>
                                @else
                                    <td>MFL</td>
                                    <td>{{ $message->max_flight_level ?? 'N/A' }}</td>
                                @endif
                            </tr>
                            <tr>
                                <td>MACH</td>
                                <td>{{ $message->mach }}</td>

                            </tr>
                            <tr>
                                <td>TIME</td>
                                <td>{{ $message->request_time->format('Hi') }}</td>
                            </tr>
                            <tr>
                                <td>CID</td>
                                <td>{{ $message->vatsimAccount->full_name }} {{ $message->vatsimAccount->id }}</td>
                            </tr>
                            <tr>
                                <td>Target OCA</td>
                                <td>{{ $message->targetDatalinkAuthority->id }}</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <div>
                        <h5>
                            Clearances
                        </h5>
                        @if ($message->previous_clx_message)
                            <div class="card">
                                <div class="card-body">
                                    <p class="text-secondary">Prior to ETA Notify - Issued by {{ $message->previous_clx_message['vatsim_account_id'] }} - {{ $message->previous_clx_message['created_at'] }}</p>
                                    <p>
                                        @foreach($message->previous_clx_message['datalink_message'] as $line)
                                            {{ $line }}<br>
                                        @endforeach
                                    </p>
                                </div>
                            </div>
                        @endif
                        @foreach($message->clxMessages->sortbyDesc('created_at') as $clx)
                            <div class="card">
                                <div class="card-body">
                                    <p class="text-secondary">Issued by {{ $clx->vatsimAccount->full_name }} {{ $clx->vatsimAccount->id }} - {{ $clx->created_at }} ({{ $clx->created_at->diffForHumans() }})</p>
                                    <p>
                                        @foreach($clx->datalink_message as $line)
                                            {{ $line }}<br>
                                        @endforeach
                                    </p>
                                </div>
                            </div>
                        @endforeach
                        @if ($message->clxMessages->isEmpty())
                            <p>None issued.</p>
                        @endif
                    </div>
                </div>
                <div class="col-md">
                    <div>
                        <div>
                            <h5 class="mt-2">Send clearance</h5>
                            <div class="row">
                                <div class="col">
                                    <label class="form-label" for="">Datalink authority</label>
                                    <div class="">
                                        <select name="datalink_authority" id="" autocomplete="off" class="form-select form-select-sm">
                                            @foreach($dlAuthorities as $authority)
                                                <option value="{{ $authority->id }}" @if($authority->id == $activeDlAuthority->id) selected="selected" @endif>{{ $authority->id }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col">
                                    <label class="form-label" for="">Change {{ $message->is_concorde ? 'lower block' : '' }} flight level to</label>
                                    <div class="uk-form-controls">
                                        <select name="atc_fl" id="atc_fl" autocomplete="off" class="form-select form-select-sm">
                                            <option value="" selected>Don't change</option>
                                            @for ($i = 200; $i <= 600; $i += 10)
                                                @if (in_array($i, [420, 440])) @continue @endif
                                                <option value="{{ $i }}">FL {{ $i }} @if ($message->flight_level == $i) (pilot request) @elseif ($message->max_flight_level == $i) (max pilot flight level) @endif</option>
                                            @endfor
                                        </select>
                                    </div>
                                </div>
                                @if (!$message->is_concorde)
                                    <div class="col">
                                        <label class="form-label" for="">Change mach to</label>
                                        <div class="uk-form-controls">
                                            <select name="atc_mach" id="atc_mach" autocomplete="off" class="form-select form-select-sm">
                                                <option value="" selected>Don't change</option>
                                                @for ($i = 55; $i < 99; $i++)
                                                    <option value="0{{ $i }}">0{{ $i }} @if ($message->mach == '0' . $i) (pilot request) @endif</option>
                                                @endfor
                                            </select>
                                        </div>
                                    </div>
                                @else
                                    <div class="col">
                                        <label class="form-label" for="">Change upper block flight level to</label>
                                        <div class="uk-form-controls">
                                            <select name="atc_ufl" id="atc_ufl" autocomplete="off" class="form-select form-select-sm">
                                                <option value="" selected>Don't change</option>
                                                @for ($i = 200; $i <= 600; $i += 10)
                                                    @if (in_array($i, [420, 440])) @continue @endif
                                                    <option value="{{ $i }}">FL {{ $i }} @if ($message->upper_flight_level == $i) (pilot request)@endif</option>
                                                @endfor
                                            </select>
                                        </div>
                                    </div>
                                @endif
                                <hr class="my-3">
                                <div class="col">
                                    <label class="form-label" for="">Entry time requirement for {{ $message->entry_fix }}</label>
                                    <div class="input-group">
                                        <select class="form-select form-select-sm" autocomplete="off" name="entry_time_type" id="entry_time_type">
                                            <option value="=" selected>At</option>
                                            <option value="<">Before</option>
                                            <option value=">">After</option>
                                            <option value="callsign">Interval</option>
                                        </select>
                                        <input type="number" name="entry_time_requirement" id="entry_time_requirement" class="form-control form-conrtol-sm" value="{{ $message->entry_time }}" maxlength="4">
                                        <input type="text" name="interval_callsign" id="interval_callsign" class="form-control form-conrtol-sm" value="" style="display: none;" placeholder="Callsign of cleared aircraft">
                                        <input type="number" name="interval_minutes" id="interval_minutes" class="form-control form-conrtol-sm" value="" style="display: none;" maxlength="4" placeholder="Minutes (+/-)">
                                        <script type="module">
                                            $('#entry_time_type').on('change', function () {
                                                if (this.value == 'callsign') {
                                                    $('#entry_time_requirement').hide();
                                                    $('#interval_callsign').show();
                                                    $('#interval_minutes').show();
                                                } else {
                                                    $('#entry_time_requirement').show();
                                                    $('#interval_callsign').hide();
                                                    $('#interval_minutes').hide();
                                                }
                                            });
                                        </script>
                                    </div>
                                </div>
                                <hr class="my-3">
                                <div class="col">
                                    <label class="form-label" for="">Change route to another NAT</label>
                                    <div >
                                        <select class="form-select form-select-sm" autocomplete="off" name="new_track_id" id="new_track_id">
                                            <option value="" selected>None</option>
                                            @foreach($tracks as $track)
                                                <option value="{{ $track->id }}">{{ $track->identifier }} ({{ $track->last_routeing }})</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col">
                                    <label class="form-label" for="">Change route to another RR</label>
                                    <div class="uk-form-controls">
                                        <input type="text" name="new_random_routeing" id="new_random_routeing" class="form-control form-conrtol-sm" autocomplete="off" placeholder="">
                                    </div>
                                </div>
                                <hr class="my-3">
                                <div class="col">
                                    <label class="form-label" for="">Free text</label>
                                    <div class="uk-form-controls">
                                        <input type="text" name="free_text" class="form-control form-conrtol-sm">
                                    </div>
                                </div>
                                <hr class="my-3"/>
                                <div class="col">
                                    <div class="card card-body" style="padding: 15px !important;">
                                        <livewire:controllers.conflict-checker callsign="{{ $message->callsign }}" level="{{ $message->flight_level }}" time="{{ $message->entry_time }}" entry="{{ $message->entry_fix }}"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-3 d-grid gap-2">
                        <button class="btn btn-success" onclick="" type="submit">Transmit {{ $message->clxMessages->count() > 0 || $message->is_acknowledged ? 'Reclearance' : 'Clearance' }}</button>
                    </div>
                </div>
            </div>
        </form>
        <div class="d-flex gap-2" uk-grid>

            @if ($message->is_acknowledged)
                <div class="row">
                    <div class="col">
                        <form action="{{ route('controllers.clx.move-to-processed', $message) }}" method="post">
                            @csrf
                            <button class="btn btn-primary w-100 my-2" onclick="" type="submit">Move to Processed List</button>
                        </form>
                    </div>
                </div>
            @endif
            <form action="{{ route('controllers.clx.revert-to-voice', $message) }}" method="post">
                @csrf
                <button class="btn btn-sm btn-outline-secondary">Revert To Voice</button>
            </form>
            <form action="{{ route('controllers.clx.delete-rcl-message', $message) }}" method="post">
                @csrf
                <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure? Make sure to communicate with the pilot.')">Delete Request</button>
            </form>
        </div>
    </div>
    <script type="module">
        $("#atc_fl").change(function () {
            Livewire.dispatch('levelChanged', { newLevel: this.value });
        });

        $('#entry_time_requirement').blur(function () {
            Livewire.dispatch('timeChanged', { newTime: this.value });
        });

        $('#new_track_id').change(function () {
            Livewire.dispatch('trackChanged', { newTrackId: this.value });
        });

        $('#new_random_routeing').blur(function () {
            Livewire.dispatch('rrChanged', { newRouteing: this.value });
        });
    </script>
@endsection
