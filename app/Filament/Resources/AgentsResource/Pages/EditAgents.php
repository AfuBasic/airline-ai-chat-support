<?php

namespace App\Filament\Resources\AgentsResource\Pages;

use App\Filament\Resources\AgentsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAgents extends EditRecord
{
    protected static string $resource = AgentsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
