<?php

namespace Ipaas\Gapp\Tests;

use Orchestra\Testbench\TestCase as BaseTestCase;
//use PHPUnit\Framework\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        // Setup default database to use sqlite :memory:
        $app['config']->set('database.default', 'phpunit');
        $app['config']->set('database.connections.phpunit', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);
    }

    protected function getResponseResult($response)
    {
        return json_decode($response->getContent());
    }

    protected function getResponseData($response)
    {
        return json_decode($response->getContent())->data;
    }
}
