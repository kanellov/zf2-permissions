# zf2-permissions

[![Build Status](https://travis-ci.org/kanellov/zf2-permissions.svg?branch=develop)](https://travis-ci.org/kanellov/zf2-permissions.svg?branch=develop)

Adds extra functionality for [Component_ZendPermissionsRbac](https://github.com/zendframework/Component_ZendPermissionsRbac) and [Component_ZendPermissionsAcl](https://github.com/zendframework/Component_ZendPermissionsAcl)

## Installation

Install composer in your project:

    curl -s https://getcomposer.org/installer | php

Create a composer.json file in your project root:

    {
        "require": {
            "kanellov/zf2-permissions": "dev-master"
        }
    }

Install via composer:

    php composer.phar install

Add this line to your application’s index.php file:

    <?php
    require 'vendor/autoload.php';

## System Requirements

You need PHP >= 5.3.23.

## Acl Callback Assertions

    <?php
    use Zend\Permissions\Acl\Acl;
    use Knlv\Zf2\Permissions\Acl\Assertion\Callback;

    $validIps = array(
        10.10.10.10,
    );
    $acl = new Acl();
    $assertion = new Callback(function ($acl, $role, $resource, $privilege) use ($validIps) {
        return in_array($_SERVER['REMOTE_ADDR'], $validIps);
    });
    $acl->allow(null, null, null, $assertion);

See also [Writing Conditional ACL Rules with Assertions](http://framework.zend.com/manual/current/en/modules/zend.permissions.acl.advanced.html#writing-conditional-acl-rules-with-assertions)

## Rbac Callback Assertions

    <?php
    use Zend\Permissions\Rbac\Rbac;
    use Knlv\Zf2\Permissions\Rbac\Assertion\Callback;

    // User is assigned the foo role with id 5
    // News article belongs to userId 5
    // Jazz article belongs to userId 6

    $rbac = new Rbac();
    $user = $mySessionObject->getUser();
    $news = $articleService->getArticle(5);
    $jazz = $articleService->getArticle(6);

    $rbac->addRole($user->getRole());
    $rbac->getRole($user->getRole())->addPermission('edit.article');

    $assertionCb = function ($user, $article) {
        return function ($rbac) use ($user, $article) {
            return $user->getId() == $article->getUserId();
        };
    };

    // true always - bad!
    if ($rbac->isGranted($user->getRole(), 'edit.article')) {
        // hacks another user's article
    }

    $assertion = new Callback($assertionCb($user, $news));

    // true for user id 5, because he belongs to write group and user id matches
    if ($rbac->isGranted($user->getRole(), 'edit.article', $assertion)) {
        // edits his own article
    }

    $assertion = new Callback($assertionCb($user, $jazz));

    // false for user id 5
    if ($rbac->isGranted($user->getRole(), 'edit.article', $assertion)) {
        // can not edit another user's article
    }

See also: [Dynamic Assertions](http://framework.zend.com/manual/current/en/modules/zend.permissions.rbac.examples.html#dynamic-assertions)
