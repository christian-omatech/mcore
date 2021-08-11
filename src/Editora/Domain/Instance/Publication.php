<?php declare(strict_types=1);

namespace Omatech\Mcore\Editora\Domain\Instance;

use DateTime;
use Omatech\Mcore\Editora\Domain\Value\Exceptions\Rules\InvalidEndDatePublishingException;

final class Publication
{
    private const DATE_FORMAT = 'd/m/Y H:i:s';
    private string $status = PublicationStatus::PENDING;
    private ?DateTime $startPublishingDate = null;
    private ?DateTime $endPublishingDate = null;

    public function fill(array $publication): void
    {
        assert(isset($publication['startPublishingDate']));
        $this->status = $publication['status'] ?? PublicationStatus::PENDING;
        $this->startPublishingDate = $publication['startPublishingDate'];
        $this->endPublishingDate = $publication['endPublishingDate'] ?? null;

        $this->validateEndPublishingDate();
    }

    private function validateEndPublishingDate(): void
    {
        if ($this->endPublishingDate !== null &&
        $this->endPublishingDate <= $this->startPublishingDate) {
            InvalidEndDatePublishingException::withDate(
                $this->endPublishingDate->format('Y-m-d H:i:s'),
                $this->startPublishingDate->format('Y-m-d H:i:s')
            );
        }
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
