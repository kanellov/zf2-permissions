<?php
require __DIR__ . '/../vendor/autoload.php';

use FUnit as fu;
use Zend\Permissions\Rbac\Rbac;
use Knlv\Zf2\Permissions\Rbac\Assertion\Callback as RbacCallback;

fu::setup(function () {
    $rbac = new Rbac();
    $rbac->addRole('member');
    $rbac->addRole('guest', 'member');

    $rbac->getRole('guest')->addPermission('read');
    $rbac->getRole('member')->addPermission('write');

    fu::fixture('rbac', $rbac);
});

fu::test('Test rbac callback assertion', function () {
    $rbac = fu::fixture('rbac');
    $test = $rbac->isGranted('guest', 'read') &&
            $rbac->isGranted('member', 'read') &&
            !$rbac->isGranted('guest', 'write') &&
            $rbac->isGranted('member', 'write');

    fu::ok($test, 'Test rbac without assertions');

    $assertTrue = new RbacCallback(function () {
        return true;
    });
    $assertFalse = new RbacCallback(function () {
        return false;
    });

    fu::not_ok(
        $rbac->isGranted('member', 'read', $assertFalse),
        'Assert permission not granted when callback returns false'
    );

    fu::ok(
        $rbac->isGranted('member', 'write', $assertTrue),
        'Assert permission granted when callback returns true'
    );
});
