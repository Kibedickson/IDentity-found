<?php

namespace App\Livewire;

use Filament\Forms\Components\Group;
use Filament\Forms\Components\TextInput;
use Jeffgreco13\FilamentBreezy\Livewire\PersonalInfo;

class CustomPersonalInfo extends PersonalInfo
{
    public array $only = ['name', 'email', 'phone'];

    protected function getProfileFormSchema(): array
    {
        $groupFields = Group::make([
            $this->getNameComponent(),
            $this->getEmailComponent(),
            $this->getPhoneComponent(),
        ])->columnSpan(3);

        return ($this->hasAvatars)
            ? [filament('filament-breezy')->getAvatarUploadComponent(), $groupFields]
            : [$groupFields];
    }

    protected function getPhoneComponent(): TextInput
    {
        return TextInput::make('phone')
            ->tel()
            ->required();
    }
}
