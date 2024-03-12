<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NotificationResource\Pages;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Notifications\DatabaseNotification;

class NotificationResource extends Resource
{
    protected static ?string $model = DatabaseNotification::class;

    protected static ?string $navigationIcon = 'heroicon-o-bell';

    protected static ?string $navigationLabel = 'Notifications';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::query()
            ->where('notifiable_id', auth()->id())
            ->whereNull('read_at')
            ->count();
    }


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('data.body')->wrap(),
                Tables\Columns\TextColumn::make('created_at')->date(),
                Tables\Columns\TextColumn::make('read_at')->date(),

            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->tooltip('View Document')
                    ->url(function (DatabaseNotification $record): string {
                        $record->markAsRead();
                        return DocumentResource::getUrl('index', ['tableSearch' => $record->data['document_id']]);
                    }),
                Tables\Actions\Action::make('read')
                    ->icon('heroicon-o-check-circle')
                    ->iconButton()
                    ->tooltip('Mark as read')
                    ->visible(fn(DatabaseNotification $record) => $record->read_at === null)
                    ->action(function (DatabaseNotification $record) {
                        $record->markAsRead();
                    })
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageNotifications::route('/'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $user = auth()->user();
        return parent::getEloquentQuery()
            ->when(!$user->isSuperAdmin(), function (Builder $query) use ($user) {
                $query->where('notifiable_id', $user->id);
            });
    }
}
