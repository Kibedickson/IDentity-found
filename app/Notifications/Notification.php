<?php

namespace App\Notifications;

use Filament\Notifications\Notification as BaseNotification;

class Notification extends BaseNotification
{
    protected string $documentId = '';

    public function toArray(): array
    {
        return [
            ...parent::toArray(),
            'document_id' => $this->getDocumentId(),
        ];
    }

    public static function fromArray(array $data): static
    {
        return parent::fromArray($data)->documentId($data['document_id']);
    }

    public function documentId(string $documentId): static
    {
        $this->documentId = $documentId;

        return $this;
    }

    public function getDocumentId(): string
    {
        return $this->documentId;
    }
}
