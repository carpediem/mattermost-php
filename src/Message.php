<?php
/**
 * This file is part of the carpediem mattermost webhook library
 *
 * @license http://opensource.org/licenses/MIT
 * @link https://github.com/carpediem/mattermost-webhook/
 * @version 1.2.0
 * @package carpediem.mattermost-webhook
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Carpediem\Mattermost\Webhook;

use Traversable;

final class Message implements MessageInterface
{
    /**
     * @var string
     */
    private $text;

    /**
     * @var string
     */
    private $username = '';

    /**
     * @var string
     */
    private $channel = '';

    /**
     * @var string
     */
    private $icon_url = '';

    /**
     * @var AttachmentInterface[]
     */
    private $attachments = [];

    /**
     * Returns a new instance from an array
     *
     * @param array $arr
     *
     * @return self
     */
    public static function fromArray(array $arr)
    {
        if (!isset($arr['text'])) {
            $arr['text'] = '';
        }

        $prop = $arr + (new self($arr['text']))->toArray();
        foreach ($prop['attachments'] as $offset => $attachment) {
            if (!$attachment instanceof Attachment) {
                $attachment = Attachment::fromArray($attachment);
            }
            $prop['attachments'][$offset] = $attachment;
        }

        return self::__set_state($prop);
    }

    /**
     * {@inheritdoc}
     */
    public static function __set_state(array $prop)
    {
        return (new self($prop['text']))
            ->setUsername($prop['username'])
            ->setChannel($prop['channel'])
            ->setIconUrl($prop['icon_url'])
            ->setAttachments($prop['attachments'])
        ;
    }

    /**
     * New instance
     *
     * @param string $text
     */
    public function __construct($text)
    {
        $this->setText($text);
    }

    /**
     * {@inheritdoc}
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * {@inheritdoc}
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * {@inheritdoc}
     */
    public function getChannel()
    {
        return $this->channel;
    }

    /**
     * {@inheritdoc}
     */
    public function getIconUrl()
    {
        return $this->icon_url;
    }

    /**
     * {@inheritdoc}
     */
    public function getAttachments()
    {
        foreach ($this->attachments as $attachment) {
            yield $attachment;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        $arr = get_object_vars($this);

        return array_filter($arr, __NAMESPACE__.'\\filter_array_value');
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        $arr = get_object_vars($this);

        foreach ($arr['attachments'] as $offset => $attachment) {
            $arr['attachments'][$offset] = $attachment->toArray();
        }

        return $arr;
    }

    /**
     * Returns an instance with the specified message text.
     *
     * @param string $text
     *
     * @return self
     */
    public function setText($text)
    {
        $text = filter_string($text, 'text');
        if ('' === $text) {
            throw new Exception('The text can not be empty');
        }

        $this->text = $text;

        return $this;
    }

    /**
     * Returns an instance with the specified message username.
     *
     * @param string $username
     *
     * @return self
     */
    public function setUsername($username)
    {
        $this->username = filter_string($username, 'username');

        return $this;
    }

    /**
     * Returns an instance with the specified message channel.
     *
     * @param string $channel
     *
     * @return self
     */
    public function setChannel($channel)
    {
        $this->channel = filter_string($channel, 'channel');

        return $this;
    }

    /**
     * Returns an instance with the specified message icon URL.
     *
     * @param string|UriInterface $icon_url
     *
     * @return self
     */
    public function setIconUrl($icon_url)
    {
        $this->icon_url = filter_uri($icon_url, 'icon_url');

        return $this;
    }

    /**
     * Override all attachements object with a iterable structure
     *
     * @param array|Traversable $attachments
     *
     * @return self
     */
    public function setAttachments($attachments)
    {
        if (!is_iterable($attachments)) {
            throw new Exception(sprintf('%s() expects argument passed to be iterable, %s given', __METHOD__, gettype($attachments)));
        }

        $this->attachments = [];
        foreach ($attachments as $attachment) {
            $this->addAttachment($attachment);
        }

        return $this;
    }

    /**
     * Returns an instance with the added attachment object.
     *
     * @param AttachmentInterface $attachment
     *
     * @return self
     */
    public function addAttachment(Attachment $attachment)
    {
        $this->attachments[] = $attachment;

        return $this;
    }
}
