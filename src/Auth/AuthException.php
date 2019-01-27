<?php

/*
 * This file is part of the Eventum (Issue Tracking System) package.
 *
 * @copyright (c) Eventum Team
 * @license GNU General Public License, version 2 or later (GPL-2+)
 *
 * For the full copyright and license information,
 * please see the COPYING and AUTHORS files
 * that were distributed with this source code.
 */

namespace Eventum\Auth;

use RuntimeException;

class AuthException extends RuntimeException
{
    public const EMPTY_LOGIN = 1;
    public const EMPTY_PASSWORD = 2;
    public const UNKNOWN_USER = 3;
    public const WRONG_PASSWORD = 3;
    public const ACCOUNT_BACKOFF_LOCKED = 13;
}
