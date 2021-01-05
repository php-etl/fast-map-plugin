<?php declare(strict_types=1);

namespace functional\Kiboko\Component\ETL\Flow\FastMap\Configuration;

use Kiboko\Component\ETL\Flow\FastMap;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\Definition\Processor;

final class HashMapperTest extends TestCase
{
    public function testEmpty()
    {
        $this->markTestIncomplete();
//        $processor = new Processor();
//        $configuration = new FastMap\Configuration\MapMapper();
//
//        $this->expectException(InvalidConfigurationException::class);
//        $this->expectExceptionMessage('Your configuration should contain the "map" key.');
//
//        $processor->processConfiguration($configuration, [
//            []
//        ]);
    }

    public function testWithNoFields()
    {
        $this->markTestIncomplete();
//        $processor = new Processor();
//        $configuration = new FastMap\Configuration\MapMapper();
//
//        $this->assertEmpty(
//            $processor->processConfiguration($configuration, [
//                [
//                    'map' => []
//                ]
//            ])
//        );
    }

    public function testWithCompetingField()
    {
        $this->markTestIncomplete();
//        $processor = new Processor();
//        $configuration = new FastMap\Configuration\MapMapper();
//
//        $this->assertEmpty(
//            $processor->processConfiguration($configuration, [
//                [
//                    'object' => []
//                ]
//            ])
//        );
    }
}
