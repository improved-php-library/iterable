<?php

namespace Jasny\IteratorPipeline\Tests;

use PHPUnit\Framework\TestCase;
use Jasny\IteratorPipeline\Aggregator\CountAggregator;

/**
 * @covers \Jasny\IteratorPipeline\Aggregator\AbstractAggregator
 * @covers \Jasny\IteratorPipeline\Aggregator\CountAggregator
 */
class CountAggregatorTest extends TestCase
{
    public function testAggregate()
    {
        $values = array_fill(2, 6, null);
        $iterator = new \ArrayIterator($values);

        $aggregator = new CountAggregator($iterator);

        $result = $aggregator();

        $this->assertEquals(6, $result);
        $this->assertSame($iterator, $aggregator->getIterator());
    }

    public function testAggregateEmpty()
    {
        $aggregator = new CountAggregator(new \EmptyIterator());

        $result = $aggregator();

        $this->assertEquals(0, $result);
    }
}