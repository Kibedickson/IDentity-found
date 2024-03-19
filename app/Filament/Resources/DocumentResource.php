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
use Filament\Infolists\Components\SpatieMediaLibraryImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
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
use Illuminate\Validation\Rules\Unique;

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
                    ->unique(ignoreRecord: true, modifyRuleUsing: function (Unique $rule, callable $get) {
                        return $rule
                            ->where('type', $get('type'))
                            ->where('category_id', $get('category_id'));
                    })
                    ->validationMessages([
                        'unique' => 'The document has already been added.',
                    ])
                    ->required(),

                Select::make('category_id')
                    ->relationship('category', 'name')
                    ->searchable(['name'])
                    ->preload()
                    ->required()
                    ->native(false),
                TextInput::make('location')
                    ->required(),
                Select::make('type')
                    ->options(DocumentTypeEnum::class)
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
                TextColumn::make('category.name'),
                TextColumn::make('location'),
                TextColumn::make('user.name')->label('Added By'),
                TextColumn::make('claimUser.name')->label('Claimed By'),
                TextColumn::make('type'),
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
                            'claim_user_id' => $notification?->data['user_id'] ?? auth()->id(),
                        ]);
                        Notification::make()
                            ->title('Document claimed successfully')
                            ->success()
                            ->send();
                    })
                    ->requiresConfirmation(),
                ViewAction::make()->visible(fn(Document $record) => $record->user_id === auth()->id()),
                EditAction::make()->visible(fn(Document $record) => $record->status === DocumentStatusEnum::NOT_CLAIMED && $record->user_id === auth()->id()),
                DeleteAction::make()->visible(fn(Document $record) => $record->status === DocumentStatusEnum::NOT_CLAIMED && $record->user_id === auth()->id()),
            ])
            ->modifyQueryUsing(function (Builder $query) {
                $user = auth()->user();
                return $query
                    ->when(!$user->isSuperAdmin(), function (Builder $query) use ($user) {
                        $query->where('user_id', $user->id)
                            ->orWhere('claim_user_id', $user->id);
                    })
                    ->latest();
            });
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                TextEntry::make('document_number'),
                TextEntry::make('category.name'),
                TextEntry::make('location'),
                TextEntry::make('type'),
                TextEntry::make('status'),
                TextEntry::make('user.name')->label('Added By'),
                TextEntry::make('claimUser.name')->label('Claimed By')->visible(fn(Document $record) => $record->status === DocumentStatusEnum::CLAIMED),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDocuments::route('/'),
            'create' => Pages\CreateDocument::route('/create'),
            'edit' => Pages\EditDocument::route('/{record}/edit'),
            'view' => Pages\ViewDocument::route('/{record}'),
        ];
    }
}
