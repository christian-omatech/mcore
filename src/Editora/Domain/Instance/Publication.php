<?php declare(strict_types=1);

namespace Omatech\Mcore\Editora\Domain\Instance;

use DateTime;

final class Publication
{
    private const DATE_FORMAT = 'd/m/Y H:i:s';
    private string $status = PublicationStatus::PENDING;
    private ?DateTime $startPublishingDate = null;
    private ?DateTime $endPublishingDate = null;

    public function fill(array $publication): void
    {
        $this->status = $publication['status'] ?? PublicationStatus::PENDING;
        $this->startPublishingDate = $publication['startPublishingDate'] ?? null;
        $this->endPublishingDate = $publication['endPublishingDate'] ?? null;
    }

    public function toArray(): array
    {
        return [
            'status' => $this->status,
            'startPublishingDate' => $this->startPublishingDate?->format($this::DATE_FORMAT),
            'endPublishingDate' => $this->endPublishingDate?->format($this::DATE_FORMAT),
        ];
    }
}
