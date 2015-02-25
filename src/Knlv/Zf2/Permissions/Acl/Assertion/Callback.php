<?php

/**
 * Knlv\Zf2\Permissions\Acl\Assertion\Callback
 *
 * @link https://github.com/kanellov/zf2-permissions
 * @copyright Copyright (c) 2015 Vassilis Kanellopoulos - contact@kanellov.com
 * @license https://raw.githubusercontent.com/kanellov/zf2-permissions/master/LICENSE
 */

namespace Knlv\Zf2\Permissions\Acl\Assertion;

use Zend\Permissions\Acl\Acl;
use Zend\Permissions\Acl\Assertion\AssertionInterface;
use Zend\Permissions\Acl\Exception\InvalidArgumentException;
use Zend\Permissions\Acl\Resource\ResourceInterface;
use Zend\Permissions\Acl\Role\RoleInterface;

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
     * Returns true if and only if the assertion conditions are met
     *
     * This method is passed the ACL, Role, Resource, and privilege to which the authorization query applies. If the
     * $role, $resource, or $privilege parameters are null, it means that the query applies to all Roles, Resources, or
     * privileges, respectively.
     *
     * @param  Acl  $acl
     * @param  RoleInterface         $role
     * @param  ResourceInterface $resource
     * @param  string                         $privilege
     * @return bool
     */
    public function assert(
        Acl $acl,
        RoleInterface $role = null,
        ResourceInterface $resource = null,
        $privilege = null
    ) {
        return (bool) call_user_func($this->callback, $acl, $role, $resource, $privilege);
    }
}
