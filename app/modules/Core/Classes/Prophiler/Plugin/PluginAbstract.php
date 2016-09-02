<?php
/**
 * @author @fabfuel <fabian@fabfuel.de>
 * @created 16.11.14, 20:05 
 */
namespace Sky\Core\Prophiler\Plugin;

use Sky\Core\Prophiler\ProfilerInterface;

abstract class PluginAbstract
{
    /**
     * @var ProfilerInterface
     */
    protected $profiler;

    /**
     * @param ProfilerInterface $profiler
     * @return static
     */
    public static function getInstance(ProfilerInterface $profiler)
    {
        $plugin = new static;
        $plugin->setProfiler($profiler);
        return $plugin;
    }

    /**
     * @return ProfilerInterface
     */
    public function getProfiler()
    {
        return $this->profiler;
    }

    /**
     * @param ProfilerInterface $profiler
     */
    public function setProfiler(ProfilerInterface $profiler)
    {
        $this->profiler = $profiler;
    }
}
