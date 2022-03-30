<div>
    <link rel="stylesheet" href="//cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <script src="//cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    @if (config('services.clx-filtering.update.poll_for_updates') && config('services.clx-filtering.update.auto_populate_table'))
    <div wire:poll.2s>
    @else
    <div>
    @endif
        @if (config('services.clx-filtering.update.poll_for_updates') && config('services.clx-filtering.update.auto_populate_table'))
            <p class="small">Automatic table updates enabled.</p>
        @endif
        <table id="dataTable" class="table table-striped table-bordered" style="width:100%">
            <thead>
            <tr>
                <th>CS</th>
                <th>DEST</th>
                <th>ROUTE</th>
                <th>ENTRY</th>
                <th>ETA</th>
                <th>FL</th>
                <th>MFL</th>
                <th>MACH</th>
                <th>REQ TIME</th>
                <th></th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            @foreach($pendingRclMsgs as $msg)
                <tr>
                    <th>{{ $msg->callsign }}</th>
                    <td>{{ $msg->destination }}
                    <td>{{ $msg->track ? 'NAT '. $msg->track->identifier : 'RR' }}</td>
                    <td>{{ $msg->entry_fix }}</td>
                    <td>{{ $msg->entry_time }}</td>
                    <td>{{ $msg->flight_level }}</td>
                    <td>{{ $msg->max_flight_level }}</td>
                    <td>{{ $msg->mach }}</td>
                    <td>{{ $msg->request_time->format('Hi') }}</td>
                    <td>
                        <a href="{{ route('controllers.clx.show-rcl-message', $msg) }}" class="btn btn-sm btn-primary"><b>ACTION</b></a>
                    </td>
                    <td>
                        <form action="">
                            <button class="btn btn-sm" onclick="return confirm('Are you sure? Make sure to communicate with the pilot.')">DEL</button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    {{--<script>
        $(document).ready( function () {
            $('#dataTable').DataTable();
        } );
    </script>--}}
</div>
