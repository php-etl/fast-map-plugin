<?php declare(strict_types=1);

namespace Kiboko\Component\ETL\Flow\FastMap\Configuration;

use Kiboko\Component\ETL\Flow\FastMap\Configuration;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class ListMapper implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        return (new Configuration())->getListTreeBuilder();
    }
}
