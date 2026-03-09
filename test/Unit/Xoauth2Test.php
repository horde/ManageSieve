<?php
declare(strict_types=1);
/**
 * Copyright 2026 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file LICENSE for license information (BSD). If you
 * did not receive this file, see http://www.horde.org/licenses/bsd.
 *
 * @category  Horde
 * @copyright 2026 Horde LLC
 * @license   http://www.horde.org/licenses/bsd BSD
 * @package   ManageSieve
 */

namespace Horde\ManageSieve\Test\Unit;

use Horde\ManageSieve\Password;
use Horde\ManageSieve\Password\Xoauth2;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Xoauth2::class)]
class Xoauth2Test extends TestCase
{
    public function testTokenGeneration(): void
    {
        // Example from https://developers.google.com/gmail/xoauth2_protocol
        $xoauth2 = new Xoauth2(
            'someuser@example.com',
            'vF9dft4qmTc2Nvb3RlckBhdHRhdmlzdGEuY29tCg==',
        );
        $this->assertEquals(
            'dXNlcj1zb21ldXNlckBleGFtcGxlLmNvbQFhdXRoPUJlYXJlciB2RjlkZnQ0cW1UYzJOdmIzUmxja0JoZEhSaGRtbHpkR0V1WTI5dENnPT0BAQ==',
            $xoauth2->getPassword(),
        );
    }

    public function testImplementsPasswordInterface(): void
    {
        $xoauth2 = new Xoauth2('user@example.com', 'token');
        $this->assertInstanceOf(Password::class, $xoauth2);
    }

    public function testUsernameIsAccessible(): void
    {
        $xoauth2 = new Xoauth2('user@example.com', 'token');
        $this->assertSame('user@example.com', $xoauth2->username);
    }

    public function testAccessTokenIsAccessible(): void
    {
        $xoauth2 = new Xoauth2('user@example.com', 'mytoken');
        $this->assertSame('mytoken', $xoauth2->access_token);
    }
}
