<?php

namespace PrasadChinwal\MicrosoftGraph\Calendar\Event;

use Closure;
use Illuminate\Config\Repository as ConfigRepository;
use Illuminate\Container\Container;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Markdown;
use Illuminate\Mail\TextMessage;
use Illuminate\Support\HtmlString;
use ReflectionException;

class CalendarEvent extends Mailable
{
    public function buildEnvelope(): array
    {
        $this->prepareMailableForDelivery();

        return [
            'subject' => $this->envelope()->subject,
            'body' => [
                'contentType' => 'HTML',
                'content' => $this->render(),
            ],
            'start' => $this->envelope()->start,
            'end' => $this->envelope()->end,
            'location' => [
                'displayName' => $this->envelope()->location,
            ],
            'attendees' => $this->envelope()->attendees,
            'isReminderOn' => $this->envelope()->reminder,
            'isOnlineMeeting' => $this->envelope()->isOnlineMeeting,
            'importance' => $this->envelope()->importance,
            'onlineMeetingProvider' => $this->envelope()->meetingProvider,
            'organizer' => [
                'emailAddress' => [
                    '@odata.type' => 'microsoft.graph.emailAddress',
                ],
            ],
            'recurrence' => $this->envelope()->recurrence,
        ];
    }

    public function prepareMailableForDelivery(): void
    {
        $this->hydrateContent();
    }

    public function hydrateContent(): void
    {
        if (! method_exists($this, 'content')) {
            return;
        }

        $content = $this->content();

        if ($content->view) {
            $this->view($content->view);
        }

        if ($content->html) {
            $this->view($content->html);
        }

        if ($content->text) {
            $this->text($content->text);
        }

        if ($content->markdown) {
            $this->markdown($content->markdown);
        }

        if ($content->htmlString) {
            $this->html($content->htmlString);
        }

        foreach ($content->with as $key => $value) {
            $this->with($key, $value);
        }
    }

    /**
     * @throws ReflectionException
     */
    protected function buildView(): array|string
    {
        if (isset($this->html)) {
            return array_filter([
                'html' => new HtmlString($this->html),
                'text' => $this->textView ?? null,
            ]);
        }

        if (isset($this->markdown)) {
            return $this->buildMarkdownView();
        }

        if (isset($this->view, $this->textView)) {
            return [$this->view, $this->textView];
        } elseif (isset($this->textView)) {
            return ['text' => $this->textView];
        }

        return $this->view;
    }

    /**
     * Build the Markdown view for the message.
     *
     *
     * @throws \ReflectionException
     */
    protected function buildMarkdownView(): array
    {
        $data = $this->buildViewData();

        return [
            'html' => $this->buildMarkdownHtml($data),
            'text' => $this->buildMarkdownText($data),
        ];
    }

    protected function buildMarkdownHtml($viewData): Closure
    {
        return fn ($data) => $this->markdownRenderer()->render(
            $this->markdown,
            array_merge($data, $viewData),
        );
    }

    protected function buildMarkdownText($viewData): Closure
    {
        return function ($data) use ($viewData) {
            if (isset($data['message'])) {
                $data = array_merge($data, [
                    'message' => new TextMessage($data['message']),
                ]);
            }

            return $this->textView ?? $this->markdownRenderer()->renderText(
                $this->markdown,
                array_merge($data, $viewData)
            );
        };
    }

    protected function markdownRenderer()
    {
        return tap(Container::getInstance()->make(Markdown::class), function ($markdown) {
            $markdown->theme($this->theme ?: Container::getInstance()->get(ConfigRepository::class)->get(
                'mail.markdown.theme', 'default')
            );
        });
    }

    /**
     * @throws ReflectionException
     * @throws BindingResolutionException
     */
    public function getEmailContent(): string
    {
        $html = Container::getInstance()->make('mailer')->render(
            $view = $this->buildView(), $this->buildViewData()
        );

        if (is_array($view) && isset($view[1])) {
            $text = $view[1];
        }

        $text ??= $view['text'] ?? '';

        if (! empty($text) && ! $text instanceof Htmlable) {
            $text = Container::getInstance()->make('mailer')->render(
                $text, $this->buildViewData()
            );
        }

        return (string) $text;
    }
}
