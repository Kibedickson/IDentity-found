<?php

namespace App\Filament\Resources\FindDocumentResource\Pages;

use App\Filament\Resources\FindDocumentResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Database\Eloquent\Builder;

class ManageFindDocuments extends ManageRecords
{
    protected static string $resource = FindDocumentResource::class;

    protected ?string $heading = 'Find Documents';

    protected function getHeaderActions(): array
    {
        return [
        ];
    }

    protected function applySearchToTableQuery(Builder $query): Builder
    {
        $this->applyColumnSearchesToTableQuery($query);
        $search = $this->getTableSearch();
        if (!filled($search)) {
            return $query->where('id', null);
        }
        return $query->whereAny(['document_number', 'document_name', 'location'], 'LIKE', "%${search}%");
    }
}
