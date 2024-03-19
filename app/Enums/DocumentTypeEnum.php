<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum DocumentTypeEnum: string implements HasColor, HasLabel
{
    case LOST = 'lost';
    case FOUND = 'found';

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::LOST => 'danger',
            self::FOUND => 'success',
        };
    }

    public function getLabel(): ?string
    {
        return match ($this) {
            self::LOST => 'Lost',
            self::FOUND => 'Found',
        };
    }
}
