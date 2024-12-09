<?php

namespace PrasadChinwal\MicrosoftGraph\Calendar\Event\Eventable;

use Illuminate\Support\Traits\Conditionable;

class Content
{
    use Conditionable;

    /**
     * The Blade view that should be rendered for the mailable.
     *
     * @var string|null
     */
    public $view;

    /**
     * The Blade view that should be rendered for the mailable.
     *
     * Alternative syntax for "view".
     *
     * @var string|null
     */
    public $html;

    /**
     * The Blade view that represents the text version of the message.
     *
     * @var string|null
     */
    public $text;

    /**
     * The Blade view that represents the Markdown version of the message.
     *
     * @var string|null
     */
    public $markdown;

    /**
     * The pre-rendered HTML of the message.
     *
     * @var string|null
     */
    public $htmlString;

    /**
     * The message's view data.
     *
     * @var array
     */
    public $with;

    /**
     * The message that needs to be sent.
     */
    public string $message;

    /**
     * Create a new content definition.
     *
     * @param  string|null  $markdown
     *
     * @named-arguments-supported
     */
    public function __construct(?string $view = null, ?string $html = null, $markdown = null, array $with = [], ?string $htmlString = null)
    {
        $this->view = $view;
        $this->html = $html;
        $this->markdown = $markdown;
        $this->with = $with;
        $this->htmlString = $htmlString;
    }

    /**
     * Set the view for the message.
     *
     * @return $this
     */
    public function view(string $view)
    {
        $this->view = $view;

        return $this;
    }

    /**
     * Set the view for the message.
     *
     * @return $this
     */
    public function html(string $view)
    {
        return $this->view($view);
    }

    /**
     * Set the plain text view for the message.
     *
     * @return $this
     */
    public function text(string $view)
    {
        $this->text = $view;

        return $this;
    }

    /**
     * Set the Markdown view for the message.
     *
     * @return $this
     */
    public function markdown(string $view)
    {
        $this->markdown = $view;

        return $this;
    }

    /**
     * Set the pre-rendered HTML for the message.
     *
     * @return $this
     */
    public function htmlString(string $html)
    {
        $this->htmlString = $html;

        return $this;
    }

    /**
     * Add a piece of view data to the message.
     *
     * @param  array|string  $key
     * @param  mixed|null  $value
     * @return $this
     */
    public function with($key, $value = null)
    {
        if (is_array($key)) {
            $this->with = array_merge($this->with, $key);
        } else {
            $this->with[$key] = $value;
        }

        return $this;
    }
}
