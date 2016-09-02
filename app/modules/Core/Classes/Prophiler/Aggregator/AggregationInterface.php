<?php
/**
 * @author @fabfuel <fabian@fabfuel.de>
 * @created 21.06.15 07:47
 */

namespace Sky\Core\Prophiler\Aggregator;

use Sky\Core\Prophiler\Benchmark\BenchmarkInterface;

/**
 * Interface AggregationInterface
 * @package Sky\Core\Prophiler\Aggregator
 */
interface AggregationInterface
{
    /**
     * @param string $command
     */
    public function __construct($command);

    /**
     * @param BenchmarkInterface $benchmark
     */
    public function aggregate(BenchmarkInterface $benchmark);

    /**
     * @return string
     */
    public function getCommand();

    /**
     * @return BenchmarkInterface[]
     */
    public function getBenchmarks();

    /**
     * @return int
     */
    public function getTotalExecutions();

    /**
     * @return float
     */
    public function getTotalDuration();

    /**
     * @return float
     */
    public function getAvgDuration();

    /**
     * @return float
     */
    public function getMinDuration();

    /**
     * @return float
     */
    public function getMaxDuration();
}
