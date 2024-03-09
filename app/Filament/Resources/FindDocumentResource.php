<?php

namespace App\Filament\Resources;

use App\Enums\DocumentStatusEnum;
use App\Enums\DocumentTypeEnum;
use App\Filament\Resources\FindDocumentResource\Pages;
use App\Models\Document;
use App\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Url;

class FindDocumentResource extends Resource
{
    protected static ?string $model = Document::class;

    protected static ?string $slug = 'find-documents';

    protected static ?string $navigationLabel = "Find Documents";

    protected static ?string $navigationIcon = 'heroicon-o-document-duplicate';

    protected static ?string $navigationGroup = 'Documents';

    protected static ?int $navigationSort = 2;

    #[Url]
    public array $tableColumnSearches = [];


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('document_number')->searchable(),
                TextColumn::make('document_name')
                    ->wrap()
                    ->searchable(),
                TextColumn::make('location'),
                TextColumn::make('category.name'),
            ])
            ->actions([
                Action::make('claim')
                    ->requiresConfirmation()
                    ->modalDescription('Your contact information will be sent to the person who found the document.')
                    ->button()
                    ->color('success')
                    ->action(function (Document $record) {
                        $documentNumber = $record->document_number;
                        $phoneNumber = auth()->user()->phone;
                        Notification::make()
                            ->title('Claim Document')
                            ->success()
                            ->body('A notification has been sent to the person who found the document.')
                            ->send();
                        Notification::make()
                            ->success()
                            ->title("New Document Claimed")
                            ->body("Document ${documentNumber} has been claimed. Please contact the user through ${phoneNumber}. Once confirmed, please mark the document as 'claimed'.")
                            ->documentId($record->id)
                            ->sendToDatabase($record->user);
                    })
            ])
            ->bulkActions([
            ])
            ->emptyStateHeading('No document found')
            ->emptyStateDescription('Enter a document number to find it.');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('type', DocumentTypeEnum::LOST)
            ->where('status', DocumentStatusEnum::PENDING);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageFindDocuments::route('/'),
        ];
    }
}
