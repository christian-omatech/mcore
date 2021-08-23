<?php declare(strict_types=1);

namespace Omatech\Mcore\Editora\Domain\Instance;

use DateTime;
use Omatech\Mcore\Editora\Domain\Instance\Exceptions\InvalidEndDatePublishingException;

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
        $this->startPublishingDate = new DateTime($publication['startPublishingDate']);
        if (isset($publication['endPublishingDate'])) {
            $this->endPublishingDate = new DateTime($publication['endPublishingDate']);
        }
        $this->validateEndPublishingDate();
    }

    private function validateEndPublishingDate(): void
    {
        if ($this->endPublishingDate?->diff($this->startPublishingDate)->invert === 0) {
            InvalidEndDatePublishingException::withDate(
                $this->endPublishingDate->format($this::DATE_FORMAT),
                $this->startPublishingDate->format($this::DATE_FORMAT)
            );
        }
    }

    public function data(): array
    {
        return [
            'status' => $this->status,
            'startPublishingDate' => $this->startPublishingDate,
            'endPublishingDate' => $this->endPublishingDate,
        ];
    }

    public function toArray(): array
    {
        return [
            'status' => $this->status,
            'startPublishingDate' => $this->startPublishingDate?->format('Y-m-d H:i:s'),
            'endPublishingDate' => $this->endPublishingDate?->format('Y-m-d H:i:s'),
        ];
    }
}
