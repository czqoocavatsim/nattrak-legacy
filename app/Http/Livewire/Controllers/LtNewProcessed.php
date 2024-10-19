<?php

namespace App\Http\Livewire\Controllers;

use App\Http\Controllers\ClxMessagesController;
use App\Models\ClxMessage;
use App\Models\Track;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\RclMessage;
use Rappasoft\LaravelLivewireTables\Views\Columns\ButtonGroupColumn;
use Rappasoft\LaravelLivewireTables\Views\Columns\LinkColumn;
use Rappasoft\LaravelLivewireTables\Views\Columns\WireLinkColumn;
use Rappasoft\LaravelLivewireTables\Views\Filters\MultiSelectDropdownFilter;
use Rappasoft\LaravelLivewireTables\Views\Filters\MultiSelectFilter;

class LtNewProcessed     extends DataTableComponent
{
    public function builder(): Builder
    {
        return ClxMessage::whereOverwritten(false)->with('rclMessage');
    }

    public function configure(): void
    {
        $this->setPrimaryKey('id');
        // ->setTableRowUrl(function($row) {
        //     return route('controllers.clx.show-rcl-message', $row->id);
        // })
        // ->setTableRowUrlTarget(function($row) {

        //     return '_self';
        // })
        $this->setDefaultSort('request_time', 'asc');
        $this->setTrimSearchStringEnabled();
        $this->setRefreshTime(10000);
    }

    public function columns(): array
    {
        return [
            Column::make("Id", "id"),
            Column::make("Callsign", "rclMessage.callsign")
                ->searchable(),
            Column::make("Destination", "rclMessage.destination")
                ->searchable(),
            Column::make("Track")
                ->label(function ($row, Column $column) {
                    $msg = ClxMessage::whereId($row->id)->first();
                    if ($msg == null) return "N/A";
                    return $msg->track ? $msg->track->identifier : 'RR';
                }),
            Column::make("Flight level", "flight_level"),
            Column::make("Entry fix", "entry_fix")
                ->searchable(),
            Column::make("Entry time restriction", "entry_time_restriction")
                ->sortable(),
            Column::make("Entry time request", "rclMessage.entry_time")
                ->sortable(),
            Column::make("Cleared time", "created_at")
                ->sortable(),
            Column::make("Request time", "rclMessage.request_time")
                ->sortable(),
            LinkColumn::make('View') // make() has no effect in this case but needs to be set anyway
            ->title(fn($row) => 'View ' . $row->callsign)
                ->location(function($row) {
                    return route('controllers.clx.show-clx-message', [$row->id]);
                })
                ->attributes(function($row) {
                    return [
                        'class' => 'btn btn-primary btn-sm',
                    ];
                }),
            // WireLinkColumn::make("Delete Item")
            //     ->title(fn($row) => 'Delete')
            //     ->confirmMessage('Are you sure you want to delete this item?')
            //     ->action(fn($row) => 'deleteRow("'.$row->id.'")')
            //     ->attributes(fn($row) => [
            //         'class' => 'btn btn-outline-danger btn-sm',
            //     ]),
        ];
    }

    private function scopeWhereRandomRouteing($query)
    {
        return $query->whereNotNull('random_routeing');
    }

    /* private function whereTrack($query, $selected)
    {
        return $query->where
    } */

    public function filters(): array
    {
        $options = Track::query()
            ->orderBy('identifier')
            ->get()
            ->keyBy('id')
            ->map(fn($track) => $track->identifier)
            ->toArray();
        $options[100] = 'RR';
        return [
            MultiSelectDropdownFilter::make('Track')
                ->options(
                    $options
                )
                ->setFirstOption('All')
                ->filter(function(Builder $builder, array $value) use ($options) {
                    $selections = [];
                    foreach ($value as $selection) {
                        $selections[] = $options[$selection];
                    }
                    if (in_array('RR', $selections)) {
                        unset($value[array_search('100', $value)]);
                        if (!empty($value)) {
                            $builder->where('clx_messages.random_routeing', '!=', null)->orWhereIn('clx_messages.track_id', array_values($value));
                        } else {
                            $builder->where('clx_messages.random_routeing', '!=', null);
                        }
                    }
                    else {
                        $builder->whereIn('clx_messages.track_id', array_values($value));
                    }
                })
        ];
    }

    // public function deleteRow($id)
    // {
    //     $rclMessage = RclMessage::whereId($id)->firstOrFail();
    //     $redirectToProcessed = $rclMessage->clxMessages->count() > 0;
    //     $rclMessage->delete();
    //     flashAlert(type: 'success', title: null, message: 'Request deleted.', toast: true, timer: true);
    //     if ($redirectToProcessed) {
    //         return redirect()->route('controllers.clx.processed');
    //     } else {
    //         return redirect()->route('controllers.clx.pending');
    //     }
    // }
}
