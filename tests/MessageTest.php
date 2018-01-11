<?php

namespace Carpediem\Mattermost\Webhook\Test;

use Carpediem\Mattermost\Webhook\Attachment;
use Carpediem\Mattermost\Webhook\Exception;
use Carpediem\Mattermost\Webhook\Message;
use PHPUnit\Framework\TestCase;
use TypeError;

/**
 * @coversDefaultClass Carpediem\Mattermost\Webhook\Message
 */
final class MessageTest extends TestCase
{
    public function testAttachmentState()
    {
        $message = new Message();
        $this->assertEmpty($message->jsonSerialize());
        $this->assertNotEmpty($message->toArray());
    }

    public function testBuilder()
    {
        $message = (new Message())
            ->text('This is a *test*.')
            ->channel('tests')
            ->username('A Tester')
            ->iconUrl('https://upload.wikimedia.org/wikipedia/fr/f/f6/Phpunit-logo.gif')
            ->attachment(function (Attachment $attachment) {
                $attachment->success();
            })
        ;
        $this->assertNotEmpty($message->toArray());
        $this->assertNotEmpty($message->jsonSerialize());
    }

    public function testBuilderThrowsExceptionWithInvalidUri()
    {
        $this->expectException(Exception::class);
        (new Message())->iconUrl('//github.com');
    }

    public function testBuilderThrowsExceptionWithInvalidAttachment()
    {
        $this->expectException(TypeError::class);
        (new Message())->attachment('foobar');
    }

    public function testMutability()
    {
        $message = new Message();
        $message->text('Coucou it\'s me');
        $message->attachments([new Attachment(), new Attachment()]);
        $this->assertSame('Coucou it\'s me', $message->toArray()['text']);
        $message->text('Overwritten info');
        $this->assertSame('Overwritten info', $message->toArray()['text']);
    }
}
