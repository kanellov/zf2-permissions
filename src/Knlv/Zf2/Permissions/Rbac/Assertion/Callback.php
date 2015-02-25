<?php

/**
 * Knlv\Zf2\Permissions\Rbac\Assertion\Callback
 *
 * @link https://github.com/kanellov/zf2-permissions
 * @copyright Copyright (c) 2015 Vassilis Kanellopoulos - contact@kanellov.com
 * @license https://raw.githubusercontent.com/kanellov/zf2-permissions/master/LICENSE
 */

namespace Knlv\Zf2\Permissions\Rbac\Assertion;

use Zend\Permissions\Rbac\Rbac;
use Zend\Permissions\Rbac\AssertionInterface;
use Zend\Permissions\Rbac\Exception\InvalidArgumentException;

class Callback implements AssertionInterface
{
    protected $callback;

    /**
     * Class constructor
     * @param callable $callback the autentication callback
     */
    public function __construct($callback)
    {
        if (!is_callable($callback)) {
            throw new InvalidArgumentException(sprintf(
                'Expected callable. %s given',
                (is_object($callback) ? get_class($callback) : gettype($callback))
            ));
        }

        $this->callback = $callback;
    }

    /**
     * Assertion method - must return a boolean.
     *
     * @param  Rbac    $rbac
     * @return bool
     */
    public function assert(Rbac $rbac)
    {
        return (bool) call_user_func($this->callback, $rbac);
    }
}
