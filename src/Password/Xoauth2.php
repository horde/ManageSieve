<?php

/**
 * Copyright 2025-2026 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file LICENSE for license information (BSD). If you
 * did not receive this file, see http://www.horde.org/licenses/bsd.
 *
 * @category  Horde
 * @copyright 2025 Horde LLC
 * @license   http://www.horde.org/licenses/bsd BSD
 * @package   ManageSieve
 */

namespace Horde\ManageSieve\Password;

/**
 * Generates an OAuth 2.0 authentication token as used in the XOAUTH2
 * authentication mechanism.
 *
 * See: https://developers.google.com/gmail/xoauth2_protocol
 *
 * @author    Jean Charles Delepine <delepine@u-picardie.fr>
 * @category  Horde
 * @copyright 2026 Horde LLC
 * @license   http://www.horde.org/licenses/bsd BSD
 * @package   ManageSieve
 * @since     2.0.0
 */
class Xoauth2 implements \Horde\ManageSieve\Password
{
    /**
     * Access token.
     *
     * @var string
     */
    public $access_token;

    /**
     * Username.
     *
     * @var string
     */
    public $username;

    /**
     * Constructor.
     *
     * @param string $username      The username.
     * @param string $access_token  The access token.
     */
    public function __construct($username, $access_token)
    {
        $this->username = $username;
        $this->access_token = $access_token;
    }

    /**
     * Return the password to use for the server connection.
     *
     * @return string  The password.
     */
    public function getPassword()
    {
        // base64("user=" {User} "^Aauth=Bearer " {Access Token} "^A^A")
        // ^A represents a Control+A (\001)
        return base64_encode(
            'user=' . $this->username . "\1"
            . 'auth=Bearer ' . $this->access_token . "\1\1"
        );
    }
}
