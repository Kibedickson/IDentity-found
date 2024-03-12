<?php

namespace App\Filament\Resources;

use App\Enums\DocumentStatusEnum;
use App\Enums\DocumentTypeEnum;
use App\Filament\Resources\DocumentResource\Pages;
use App\Models\Document;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ForceDeleteAction;
use Filament\Tables\Actions\ForceDeleteBulkAction;
use Filament\Tables\Actions\RestoreAction;
use Filament\Tables\Actions\RestoreBulkAction;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Notifications\DatabaseNotification;

class DocumentResource extends Resource
{
    protected static ?string $model = Document::class;

    protected static ?string $slug = 'documents';

    protected static ?string $navigationIcon = 'heroicon-o-document-duplicate';

    protected static ?string $navigationGroup = 'Documents';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('document_number')
                    ->required(),

                TextInput::make('document_name')
                    ->required(),

                Select::make('category_id')
                    ->relationship('category', 'name')
                    ->searchable(['name'])
                    ->preload()
                    ->required()
                    ->native(false),

                TextInput::make('location')
                    ->required(),
                SpatieMediaLibraryFileUpload::make('files')
                    ->multiple()
                    ->reorderable()
                    ->responsiveImages(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                TextColumn::make('document_number')->searchable(),
                TextColumn::make('document_name')
                    ->wrap(),
                TextColumn::make('location'),
                TextColumn::make('category.name'),
                TextColumn::make('user.name')->label('Added By'),
                TextColumn::make('claimUser.name')->label('Claimed By'),
                TextColumn::make('status'),
                SpatieMediaLibraryImageColumn::make('files'),
            ])
            ->actions([
                \Filament\Tables\Actions\Action::make('claim')
                    ->label('Mark as Claimed')
                    ->visible(fn(Document $record) => $record->status === DocumentStatusEnum::NOT_CLAIMED && $record->user_id === auth()->id())
                    ->button()
                    ->color('success')
                    ->action(function (Document $record) {
                        $notification = DatabaseNotification::where('data->document_id', $record->id)->first();
                        $record->update([
                            'status' => DocumentStatusEnum::CLAIMED,
                            'claim_user_id' => $notification->data['user_id']
                        ]);
                        Notification::make()
                            ->title('Document claimed successfully')
                            ->success()
                            ->send();
                    })
                    ->requiresConfirmation(),
                EditAction::make()->visible(fn(Document $record) => $record->status === DocumentStatusEnum::NOT_CLAIMED && $record->user_id === auth()->id()),
                DeleteAction::make()->visible(fn(Document $record) => $record->status === DocumentStatusEnum::NOT_CLAIMED && $record->user_id === auth()->id()),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDocuments::route('/'),
            'create' => Pages\CreateDocument::route('/create'),
            'edit' => Pages\EditDocument::route('/{record}/edit'),
            'view' => Pages\ViewDocument::route('/{record}/view'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $user = auth()->user();
        return parent::getEloquentQuery()
            ->when(!$user->isSuperAdmin(), function (Builder $query) use ($user) {
                $query->where('user_id', $user->id)
                    ->orWhere('claim_user_id', $user->id);
            });
    }
}
