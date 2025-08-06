<?php

namespace App\Filament\Resources\AgentsResource\Pages;

use App\Filament\Resources\AgentsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAgents extends ListRecords
{
    protected static string $resource = AgentsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Create Agent'),
        ];
    }
}
