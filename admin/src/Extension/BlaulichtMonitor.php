<?php

namespace Piedpiper\Component\Spm\Administrator\Extension;

use Joomla\CMS\Extension\BootableExtensionInterface;
use Joomla\CMS\Extension\MVCComponent;
use Psr\Container\ContainerInterface;

class SpmComponent extends MVCComponent implements BootableExtensionInterface
{
    public function boot(ContainerInterface $container) {}
}
