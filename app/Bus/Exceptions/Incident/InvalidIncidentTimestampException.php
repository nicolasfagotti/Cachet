<?php

/*
 * This file is part of Cachet.
 *
 * (c) Alt Three Services Limited
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CachetHQ\Cachet\Bus\Exceptions\Incident;

use CachetHQ\Cachet\Bus\Exceptions\ExceptionInterface;
use Exception;

/**
 * This is the invalid incident timestamp exception.
 *
 * @author James Brooks <james@alt-three.com>
 */
class InvalidIncidentTimestampException extends Exception implements ExceptionInterface
{
    //
}
