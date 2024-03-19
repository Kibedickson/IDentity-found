<?php

namespace App\Filament\Resources\DocumentResource\Pages;

use App\Enums\DocumentStatusEnum;
use App\Filament\Resources\DocumentResource;
use App\Models\Document;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Notifications\DatabaseNotification;

class ViewDocument extends ViewRecord
{
    protected static string $resource = DocumentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('claim')
                ->label('Mark as Claimed')
                ->visible(fn(Document $record) => $record->status === DocumentStatusEnum::NOT_CLAIMED && $record->user_id === auth()->id())
                ->button()
                ->color('success')
                ->action(function (Document $record) {
                    $notification = DatabaseNotification::where('data->document_id', $record->id)->first();
                    $record->update([
                        'status' => DocumentStatusEnum::CLAIMED,
                        'claim_user_id' => $notification?->data['user_id'] ?? auth()->id(),
                    ]);
                    Notification::make()
                        ->title('Document claimed successfully')
                        ->success()
                        ->send();
                })
                ->requiresConfirmation(),
        ];
    }
}
