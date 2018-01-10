<?php

namespace Carpediem\Mattermost;

use JsonSerializable;
use Traversable;

final class Message implements JsonSerializable
{
    /**
     * The text of the message.
     *
     * @var string
     */
    private $text = '';

    /**
     * The printed username of the message.
     *
     * @var string
     */
    private $username = '';

    /**
     * The channel of the message.
     *
     * @var string
     */
    private $channel = '';

    /**
     * The icon of the message.
     *
     * @var string
     */
    private $icon_url = '';

    /**
     * The attachments of the message.
     *
     * @var Attachment[]
     */
    public $attachments = [];

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return array_filter($this->toArray(), __NAMESPACE__.'\\filter_array_value');
    }

    /**
     * Returns the array representation of the object
     *
     * @return array
     */
    public function toArray()
    {
        return get_object_vars($this);
    }

    /**
     * @param string $text
     *
     * @return self
     */
    public function text($text)
    {
        $this->text = filter_string($text, 'text');

        return $this;
    }

    /**
     * @param string $username
     *
     * @return self
     */
    public function username($username)
    {
        $this->username = filter_string($username, 'username');

        return $this;
    }

    /**
     * @param string $channel
     *
     * @return self
     */
    public function channel($channel)
    {
        $this->channel = filter_string($channel, 'channel');

        return $this;
    }

    /**
     * @param string $icon_url
     *
     * @return self
     */
    public function iconUrl($icon_url)
    {
        $this->icon_url = filter_uri($icon_url, 'icon_url');

        return $this;
    }

    /**
     * Add an attachment for the message.
     *
     * @param Attachment|callable $attachment
     *
     * @return self
     */
    public function attachment($attachment)
    {
        if (is_callable($attachment)) {
            $item = new Attachment();
            $attachment($item);

            $this->attachments[] = $item;
            return $this;
        }

        if ($attachment instanceof Attachment) {
            $this->attachments[] = $attachment;

            return $this;
        }

        throw new Exception(sprintf('The submitted argument must be a callable or a %s class', Attachment::class));
    }

    /**
     * Override all attachements with a iterable structure
     *
     * @param array|Traversable $attachments
     *
     * @return self
     */
    public function attachments($attachments)
    {
        $this->attachments = [];
        foreach ($attachments as $attachment) {
            $this->attachment($attachment);
        }

        return $this;
    }
}
