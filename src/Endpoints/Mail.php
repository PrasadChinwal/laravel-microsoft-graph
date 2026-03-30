<?php

namespace PrasadChinwal\MicrosoftGraph\Endpoints;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;
use PrasadChinwal\MicrosoftGraph\MicrosoftGraph;

class Mail extends MicrosoftGraph
{
    protected string $email;

    protected ?string $filter = null;

    protected ?int $top = 10;

    /**
     * @param string $email
     * @return Mail
     */
    public function for(string $email): static
    {
        $this->email = $email;

        return $this;
    }

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
                ->append(' ')
                ->append($value);
        } else {
            $filterString = $filterString
                ->append(' and ')
                ->append($field)
                ->append(' ')
                ->append($operator)
                ->append(' ')
                ->append($value);
        }

        $this->filter = $filterString->value();

        return $this;
    }

    /**
     * Set the maximum number of results to return.
     *
     * @param  int  $top  The maximum number of results
     * @return $this
     */
    public function top(int $top): static
    {
        $this->top = $top;

        return $this;
    }

    /**
     * Get messages for the user with applied filters.
     *
     * @return \Illuminate\Support\Collection
     *
     * @throws \Illuminate\Http\Client\RequestException
     */
    public function get(): \Illuminate\Support\Collection
    {
        return Http::graph()
            ->withToken($this->getAccessToken())
            ->withUrlParameters([
                'user_id' => $this->email,
            ])
            ->when(! empty($this->filter), function ($http) {
                return $http->withQueryParameters([
                    '$filter' => $this->filter,
                ]);
            })
            ->withQueryParameters([
                '$top' => $this->top,
            ])
            ->get('https://graph.microsoft.com/v1.0/users/{user_id}/messages')
            ->throwUnlessStatus(200)
            ->collect();
    }
}
