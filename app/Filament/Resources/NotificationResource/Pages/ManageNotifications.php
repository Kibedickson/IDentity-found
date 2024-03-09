<?php

namespace App\Filament\Resources\NotificationResource\Pages;

use App\Filament\Resources\NotificationResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageNotifications extends ManageRecords
{
    protected static string $resource = NotificationResource::class;

    protected ?string $heading = 'Notifications';

    protected function getHeaderActions(): array
    {
        return [
        ];
    }
}
