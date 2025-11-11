<?php

namespace PrasadChinwal\MicrosoftGraph\Traits;

use Illuminate\Support\Str;
use Illuminate\Support\Stringable;

trait HasQueryFilters
{
    /**
     * The OData filter string.
     */
    protected ?string $filter = null;

    /**
     * Add a WHERE condition to the OData filter.
     *
     * @param  string  $field  The field name to filter on
     * @param  string  $condition  The condition operator (eq, ne, gt, lt, ge, le, etc.)
     * @param  string  $value  The value to compare against
     * @return $this
     */
    public function where(string $field, string $condition, string $value): static
    {
        $this->filter = Str::of($this->filter)
            ->whenNotEmpty(function (Stringable $string) {
                return $string->append(' and ');
            })
            ->append($field)
            ->append(' ')
            ->append($condition)
            ->append(' ')
            ->append(Str::wrap($value, "'"))
            ->value();

        return $this;
    }

    /**
     * Add an OR WHERE condition to the OData filter.
     *
     * @param  string  $field  The field name to filter on
     * @param  string  $condition  The condition operator (eq, ne, gt, lt, ge, le, etc.)
     * @param  string  $value  The value to compare against
     * @return $this
     */
    public function orWhere(string $field, string $condition, string $value): static
    {
        $this->filter = Str::of($this->filter)
            ->whenNotEmpty(function (Stringable $string) {
                return $string->append(' or ');
            })
            ->append($field)
            ->append(' ')
            ->append($condition)
            ->append(' ')
            ->append(Str::wrap($value, "'"))
            ->value();

        return $this;
    }
}
