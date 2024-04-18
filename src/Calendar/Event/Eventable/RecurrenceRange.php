<?php

namespace PrasadChinwal\MicrosoftGraph\Calendar\Event\Eventable;

use Carbon\Carbon;
use Date;
use PrasadChinwal\MicrosoftGraph\Calendar\Event\Eventable\Enums\RecurrenceTypeRange;

class RecurrenceRange
{

    public string $type;

    public string $startDate;

    public ? string $endDate;

    public string $recurrenceTimeZone;

    public int $numberOfOccurrences;

    public function __construct(
        string $type, Carbon $startDate, ? Carbon $endDate,
        ? string $recurrenceTimeZone = null, int $numberOfOccurrences = 0
    )
    {
        $this->setType($type);
        $this->setStartDate($startDate);
        $this->setEndDate($endDate);
        $this->setRecurrenceTimeZone($recurrenceTimeZone);
        $this->setNumberOfOccurrences($numberOfOccurrences);
    }

    public function getNumberOfOccurrences(): int
    {
        return $this->numberOfOccurrences;
    }

    public function setNumberOfOccurrences(int $numberOfOccurrences): void
    {
        if($this->type == RecurrenceTypeRange::NUMBERED && $numberOfOccurrences < 0) {
            throw new \InvalidArgumentException('Number of occurrences must be greater than 0');
        }
        $this->numberOfOccurrences = $numberOfOccurrences;
    }

    public function getRecurrenceTimeZone(): string
    {
        return $this->recurrenceTimeZone;
    }

    public function setRecurrenceTimeZone(null|string $recurrenceTimeZone): void
    {
        if($recurrenceTimeZone === null) {
            $this->recurrenceTimeZone = config('app.timezone');
        } else {
            $this->recurrenceTimeZone = $recurrenceTimeZone;
        }
    }

    public function getEndDate(): string
    {
        return Carbon::parse($this->endDate)->format('Y-m-d');
    }

    public function setEndDate(?Carbon $endDate): void
    {
        if($this->type == RecurrenceTypeRange::END_DATE && $endDate === null) {
            throw new \InvalidArgumentException('End date is required!');
        }
        $this->endDate = Carbon::parse($endDate)->format('Y-m-d');
    }

    public function getStartDate(): string
    {
        return Carbon::parse($this->startDate)->format('Y-m-d');
    }

    public function setStartDate(Carbon $startDate): void
    {
        $this->startDate = Carbon::parse($startDate)->format('Y-m-d');
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }
}
