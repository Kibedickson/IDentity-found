<?php

namespace App\Filament\Resources\DocumentResource\Pages;

use App\Enums\DocumentStatusEnum;
use App\Enums\DocumentTypeEnum;
use App\Filament\Resources\DocumentResource;
use App\Filament\Resources\NotificationResource;
use App\Models\Document;
use App\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Builder;

class CreateDocument extends CreateRecord
{
    protected static string $resource = DocumentResource::class;

    protected static bool $canCreateAnother = false;

    protected function getHeaderActions(): array
    {
        return [

        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();
        return $data;
    }

    protected function afterCreate(): void
    {
        $document = $this->getRecord();
        $existingDocument = Document::query()
            ->where('document_number', $document->document_number)
            ->where('category_id', $document->category_id)
            ->when($document->type === DocumentTypeEnum::FOUND, function (Builder $query) {
                $query->where('type', DocumentTypeEnum::LOST);
            })
            ->when($document->type === DocumentTypeEnum::LOST, function (Builder $query) {
                $query->where('type', DocumentTypeEnum::FOUND);
            })
            ->first();

        if ($existingDocument) {
            $user = auth()->user();
            if ($existingDocument->type === DocumentTypeEnum::LOST) {
                Notification::make()
                    ->success()
                    ->title("Your Document has been found")
                    ->body("Your document {$existingDocument->document_number} has been found. Please contact the person who found the document through {$user->phone}.")
                    ->documentId($existingDocument->id)
                    ->userId($user->id)
                    ->actions([
                        \Filament\Notifications\Actions\Action::make('view')
                            ->button()
                            ->url(NotificationResource::getUrl())
                    ])
                    ->sendToDatabase($existingDocument->user);
                Notification::make()
                    ->title('Document found successfully')
                    ->success()
                    ->body('An existing document with the details you provided has been found. The owner of the document will be notified to contact you.')
                    ->send()
                    ->sendToDatabase($user);
                $existingDocument->claim_user_id = $existingDocument->user_id;
                $existingDocument->status = DocumentStatusEnum::CLAIMED;
                $existingDocument->save();

                $document->delete();
            } else {
                Notification::make()
                    ->success()
                    ->title("Your Document has been found")
                    ->body("Your document {$existingDocument->document_number} has been found. Please contact the person who found the document through {$existingDocument->user->phone}.")
                    ->documentId($existingDocument->id)
                    ->userId($existingDocument->user_id)
                    ->actions([
                        \Filament\Notifications\Actions\Action::make('view')
                            ->button()
                            ->url(NotificationResource::getUrl())
                    ])
                    ->send()
                    ->sendToDatabase($user);
                Notification::make()
                    ->title('Document found successfully')
                    ->success()
                    ->body('A document you added as Found has been claimed. The owner of the document will be notified to contact you.')
                    ->sendToDatabase($existingDocument->user);
                $document->claim_user_id = $user->user_id;
                $document->status = DocumentStatusEnum::CLAIMED;
                $document->save();

                $existingDocument->delete();
            }
        }
    }
}
