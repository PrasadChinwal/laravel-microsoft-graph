<?php

namespace PrasadChinwal\MicrosoftGraph\Endpoints;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;
use PrasadChinwal\MicrosoftGraph\MicrosoftGraph;

class Mail extends MicrosoftGraph
{
    protected string $email;

    protected ?string $filters = null;

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

    public function where(string $field, string $operator = null, string $value)
    {
        $operator = match ($operator) {
            '!=' => 'ne',
            '>' => 'gt',
            '<' => 'lt',
            '>=' => 'ge',
            '<=' => 'le',
            default => 'eq',
        };
        $filter = Str::of($this->filters);
        if($filter->isEmpty()) {
            $filter = $filter // ->append('?$filter=')
                ->append($field)
                ->append(' ')
                ->append($operator)
                ->append(' ')
                ->append($value);
        } else {
            $filter = $filter->append(' and ')
                ->append($field)
                ->append(' ')
                ->append($operator)
                ->append(' ')
                ->append($value);
        }
        $this->filters = $filter->value();
        return $this;
    }

    public function top(int $top): static
    {
        $this->top = $top;
        return $this;
    }

    /**
     * @return \Illuminate\Support\Collection
     *
     * @throws \Illuminate\Http\Client\RequestException
     */
    public function get()
    {
        return Http::graph()
            ->withToken($this->getAccessToken())
            ->withUrlParameters([
                'user_id' => $this->email,
            ])
            ->when(!empty($this->filters), function ($http) {
                return $http->withQueryParameters([
                    '$filter' => $this->filters,
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
