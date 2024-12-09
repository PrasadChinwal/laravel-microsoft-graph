<?php

namespace PrasadChinwal\MicrosoftGraph\Calendar\Event\Eventable;

use PrasadChinwal\MicrosoftGraph\Calendar\Event\Eventable\Enums\DayOfWeek;
use PrasadChinwal\MicrosoftGraph\Calendar\Event\Eventable\Enums\RecurrenceType;
use PrasadChinwal\MicrosoftGraph\Calendar\Event\Eventable\Enums\WeekIndex;

class RecurrencePattern
{
    public ?int $dayOfMonth = 0;

    public ?array $daysOfWeek;

    public ?string $firstDayOfWeek = DayOfWeek::MONDAY;

    public ?string $index = WeekIndex::FIRST;

    public ?int $interval = 0;

    public ?int $month = 1;

    public string $type;

    public function __construct(
        string $type, int $dayOfMonth = 0,
        ?array $dayOfWeek = null, ?string $firstDayOfWeek = DayOfWeek::MONDAY,
        ?string $index = null, ?int $interval = 0, ?int $month = 1
    ) {
        $this->setType($type);
        $this->setDayOfMonth($dayOfMonth);
        $this->setDaysOfWeek($dayOfWeek);
        $this->setFirstDayOfWeek($firstDayOfWeek);
        $this->setIndex($index);
        $this->setInterval($interval);
        $this->setMonth($month);
    }

    public function getDayOfMonth(): ?int
    {
        return $this->dayOfMonth;
    }

    public function setDayOfMonth(?int $dayOfMonth): void
    {
        if (
            in_array($this->type, [RecurrenceType::ABSOLUTE_MONTHLY, RecurrenceType::ABSOLUTE_YEARLY]) &&
            $dayOfMonth === 0
        ) {
            throw new \InvalidArgumentException('Invalid day of month');
        }
        $this->dayOfMonth = $dayOfMonth;
    }

    public function getDaysOfWeek(): array
    {
        return $this->daysOfWeek;
    }

    public function setDaysOfWeek(string|array|null $daysOfWeek): void
    {
        if (
            in_array($this->type, [RecurrenceType::WEEKLY, RecurrenceType::RELATIVE_MONTHLY, RecurrenceType::RELATIVE_YEARLY]) &&
            $daysOfWeek === null
        ) {
            throw new \InvalidArgumentException('Invalid day of Week');
        }
        $this->daysOfWeek = $daysOfWeek;
    }

    public function getFirstDayOfWeek(): ?string
    {
        return $this->firstDayOfWeek;
    }

    public function setFirstDayOfWeek(?string $firstDayOfWeek): void
    {
        $this->firstDayOfWeek = $firstDayOfWeek;
    }

    public function getIndex(): ?string
    {
        return $this->index;
    }

    public function setIndex(?string $index): void
    {
        if (in_array($this->type, [RecurrenceType::RELATIVE_MONTHLY, RecurrenceType::RELATIVE_YEARLY])) {
            $this->index = $index;
        }
    }

    public function getInterval(): ?int
    {
        return $this->interval;
    }

    public function setInterval(?int $interval): void
    {
        $this->interval = $interval;
    }

    public function getMonth(): ?int
    {
        return $this->month;
    }

    public function setMonth(?int $month): void
    {
        if ($month < 1 || $month > 12) {
            throw new \InvalidArgumentException('Invalid month! Month must be between 1 and 12');
        }
        $this->month = $month;
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
