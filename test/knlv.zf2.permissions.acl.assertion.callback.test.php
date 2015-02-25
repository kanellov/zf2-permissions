<?php
require __DIR__ . '/../vendor/autoload.php';

use FUnit as fu;
use Zend\Permissions\Acl\Acl;
use Knlv\Zf2\Permissions\Acl\Assertion\Callback as AclCallback;

fu::setup(function () {
    $acl = new Acl();
    // add roles
    $acl->addRole('guest');
    $acl->addRole('member', 'guest');
    // add resources
    $acl->addResource('article');
    // add rules
    $acl->allow('guest', 'article', array('read'));
    $acl->allow('member', 'article', array('write', 'delete'));

    fu::fixture('acl', $acl);
});

fu::test('Test acl callback assertion', function () {
    $acl = fu::fixture('acl');
    $test = $acl->isAllowed('guest', 'article', 'read') &&
            $acl->isAllowed('member', 'article', 'read') &&
            $acl->isAllowed('member', 'article', 'write') &&
            !$acl->isAllowed('guest', 'article', 'write');
    fu::ok($test, 'Test acl without assertions');

    $assertTrue = new AclCallback(function () {
        return true;
    });
    $assertFalse = new AclCallback(function () {
        return false;
    });
    $acl->removeAllow('member', 'article', 'write');
    $acl->allow('member', 'article', 'write', $assertFalse);
    fu::not_ok(
        $acl->isAllowed('member', 'article', 'write'),
        'Assert not allowed when callback returns false'
    );
    $acl->removeAllow('member', 'article', 'write');
    $acl->allow('member', 'article', 'write', $assertTrue);
    fu::ok(
        $acl->isAllowed('member', 'article', 'write'),
        'Assert allowed when callback returns true'
    );
});