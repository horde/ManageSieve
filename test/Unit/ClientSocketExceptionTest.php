<?php

declare(strict_types=1);

/**
 * Copyright 2026 The Horde Project (http://www.horde.org/)
 *
 * See the enclosed file LICENSE for license information (BSD). If you
 * did not receive this file, see http://www.horde.org/licenses/bsd.
 */

namespace Horde\ManageSieve\Test\Unit;

use Horde\ManageSieve\Client;
use Horde\ManageSieve\Exception;
use Horde\Socket\Client as SocketClient;
use Horde\Socket\Client\Exception as SocketClientException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * Client subclass that injects a mock socket to test exception wrapping.
 */
class SocketExceptionClient extends Client
{
    public function __construct(object $mockSocket)
    {
        $this->_params = [
            'authmethod' => self::AUTH_AUTOMATIC,
            'bypassauth' => false,
            'context' => [],
            'euser' => null,
            'host' => 'localhost',
            'logger' => null,
            'password' => '',
            'port' => 4190,
            'secure' => true,
            'timeout' => 5,
            'user' => '',
        ];
        $this->_state = self::STATE_AUTHENTICATED;
        $this->_sock = $mockSocket;
    }

    public function exposeSendCmd(string $cmd): void
    {
        $this->_sendCmd($cmd);
    }

    public function exposeRecvLn(): string
    {
        return $this->_recvLn();
    }

    public function exposeRecvBytes(int $length): string
    {
        return $this->_recvBytes($length);
    }
}

/**
 * Mock socket that throws SocketClientException on all I/O operations.
 */
class ThrowingSocket
{
    public function getStatus(): array
    {
        throw new SocketClientException('Connection reset');
    }

    public function write(string $data): void
    {
        throw new SocketClientException('Broken pipe');
    }

    public function gets(int $size): string
    {
        throw new SocketClientException('Error reading data from socket');
    }

    public function read(int $size): string
    {
        throw new SocketClientException('Error reading data from socket');
    }
}

/**
 * Mock socket with a working getStatus() but throwing write().
 */
class ThrowingWriteSocket
{
    public function getStatus(): array
    {
        return ['eof' => false, 'timed_out' => false, 'blocked' => false];
    }

    public function write(string $data): void
    {
        throw new SocketClientException('Broken pipe');
    }
}

/**
 * Tests that SocketClientException from low-level I/O is wrapped in
 * ManageSieve\Exception, so callers catching ManageSieveException handle
 * socket errors properly.
 */
#[CoversClass(Client::class)]
class ClientSocketExceptionTest extends TestCase
{
    public function testRecvLnWrapsSocketException(): void
    {
        $client = new SocketExceptionClient(new ThrowingSocket());

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Error reading data from socket');
        $client->exposeRecvLn();
    }

    public function testRecvBytesWrapsSocketException(): void
    {
        $client = new SocketExceptionClient(new ThrowingSocket());

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Error reading data from socket');
        $client->exposeRecvBytes(100);
    }

    public function testSendCmdWrapsSocketExceptionFromGetStatus(): void
    {
        $client = new SocketExceptionClient(new ThrowingSocket());

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Connection reset');
        $client->exposeSendCmd('LISTSCRIPTS');
    }

    public function testSendCmdWrapsSocketExceptionFromWrite(): void
    {
        $client = new SocketExceptionClient(new ThrowingWriteSocket());

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Broken pipe');
        $client->exposeSendCmd('LISTSCRIPTS');
    }

    public function testWrappedExceptionPreservesOriginal(): void
    {
        $client = new SocketExceptionClient(new ThrowingSocket());

        try {
            $client->exposeRecvLn();
            $this->fail('Expected ManageSieve\Exception');
        } catch (Exception $e) {
            $this->assertInstanceOf(
                SocketClientException::class,
                $e->getPrevious(),
                'Original SocketClientException should be preserved as previous exception'
            );
        }
    }
}
