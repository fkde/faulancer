<?php

namespace Faulancer\Tests\Unit;

use Faulancer\Config;
use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{

    /**
     * @test
     * @return void
     */
    public function itShouldReturnConfigValue(): void
    {

        $configData = [
            'key1' => 'value1'
        ];

        $config = new Config();

    }

}