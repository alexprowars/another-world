<?php

namespace Sky\Core\Prophiler\Plugin\Phalcon\Mvc;

use Sky\Core\Prophiler\Benchmark\BenchmarkInterface;
use Sky\Core\Prophiler\Plugin\PluginAbstract;
use Phalcon\Events\Event;

class RouterPlugin extends PluginAbstract
{
    /**
     * @var BenchmarkInterface
     */
    protected $benchmarkRoute;

    public function beforeCheckRoutes(Event $event)
    {
        $name = get_class($event->getSource()) . '::сheckRoutes';
        $this->benchmarkRoute = $this->getProfiler()->start($name, [], 'Router ');
    }

    public function afterCheckRoutes()
    {
        $this->getProfiler()->stop($this->benchmarkRoute);
    }
}
