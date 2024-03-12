<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum DocumentStatusEnum: string implements HasColor, HasLabel
{
    case CLAIMED = 'claimed';
    case NOT_CLAIMED = 'not_claimed';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::CLAIMED => 'Claimed',
            self::NOT_CLAIMED => 'Not Claimed',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::CLAIMED => 'success',
            self::NOT_CLAIMED => 'warning',
        };
    }
}
