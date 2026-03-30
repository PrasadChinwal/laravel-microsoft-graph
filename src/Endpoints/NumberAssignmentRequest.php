<?php

namespace PrasadChinwal\MicrosoftGraph\Endpoints;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use PrasadChinwal\MicrosoftGraph\MicrosoftGraph;
use PrasadChinwal\MicrosoftGraph\Response\NumberAssignment\NumberAssignments;
use PrasadChinwal\MicrosoftGraph\Response\TeamConfiguration\TeamConfiguration;

class NumberAssignmentRequest extends MicrosoftGraph
{
    protected string $baseEndpoint = 'https://graph.microsoft.com/beta/admin/teams/telephoneNumberManagement/numberAssignments';

    protected ?string $filter = null;

    /**
     * Add a WHERE condition to the filter.
     *
     * @param string $field The field name to filter on
     * @param string|null $operator The operator (=, !=, >, <, >=, <=)
     * @param string|null $value The value to compare against
     * @return $this
     * @throws \Throwable
     */
    public function where(string $field, ?string $operator = null, ?string $value = null): static
    {
        throw_if(empty($value), new \InvalidArgumentException('Value cannot be empty'));

        $operator = match ($operator) {
            '!=' => 'ne',
            '>' => 'gt',
            '<' => 'lt',
            '>=' => 'ge',
            '<=' => 'le',
            default => 'eq',
        };

        $filterString = Str::of($this->filter);

        if ($filterString->isEmpty()) {
            $filterString = $filterString
                ->append($field)
                ->append(' ')
                ->append($operator)
                ->append(" '")
                ->append($value)
                ->append("'");
        } else {
            $filterString = $filterString
                ->append(' and ')
                ->append($field)
                ->append(' ')
                ->append($operator)
                ->append(" '")
                ->append($value)
                ->append("'");
        }

        $this->filter = $filterString->value();

        return $this;
    }

    /**
     * Get messages for the user with applied filters.
     *
     * @return array|\Illuminate\Contracts\Pagination\CursorPaginator|\Illuminate\Contracts\Pagination\Paginator|\Illuminate\Pagination\AbstractCursorPaginator|\Illuminate\Pagination\AbstractPaginator|\Illuminate\Support\Collection|\Illuminate\Support\Enumerable|\Illuminate\Support\LazyCollection|\Spatie\LaravelData\CursorPaginatedDataCollection|\Spatie\LaravelData\DataCollection|\Spatie\LaravelData\PaginatedDataCollection
     *
     * @throws \Illuminate\Http\Client\RequestException
     */
    public function get(): array|\Illuminate\Contracts\Pagination\CursorPaginator|\Illuminate\Contracts\Pagination\Paginator|\Illuminate\Pagination\AbstractCursorPaginator|\Illuminate\Pagination\AbstractPaginator|\Illuminate\Support\Collection|\Illuminate\Support\Enumerable|\Illuminate\Support\LazyCollection|\Spatie\LaravelData\CursorPaginatedDataCollection|\Spatie\LaravelData\DataCollection|\Spatie\LaravelData\PaginatedDataCollection
    {
        $response = Http::graph()
            ->withToken($this->getAccessToken())
            ->when(! empty($this->filter), function ($http) {
                return $http->withQueryParameters([
                    '$filter' => $this->filter,
                ]);
            })
            ->get($this->baseEndpoint)
            ->throwUnlessStatus(200)
            ->collect('value');

        return NumberAssignments::collect($response);
    }
}