<?php declare(strict_types=1);

namespace Kiboko\Plugin\FastMap\Configuration;

use Kiboko\Plugin\FastMap\Configuration;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class ConditionalMapper implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        return (new Configuration())->getConditionalTreeBuilder();
    }
}
