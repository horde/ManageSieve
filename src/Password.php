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

namespace Horde\ManageSieve;

/**
 * Interface representing a ManageSieve password object.
 *
 * @author    Jean Charles Delepine <delepine@u-picardie.fr>
 * @category  Horde
 * @copyright 2026 Horde LLC
 * @license   http://www.horde.org/licenses/bsd BSD
 * @package   ManageSieve
 * @since     2.0.0
 */
interface Password
{
    /**
     * Return the password to use for the server connection.
     *
     * @return string  The password.
     */
    public function getPassword();
}
