<?php

namespace Jasny\IteratorPipeline\Tests;

use PHPUnit\Framework\TestCase;
use Jasny\IteratorPipeline\Aggregator\MaxAggregator;

/**
 * @covers \Jasny\IteratorPipeline\Aggregator\AbstractAggregator
 * @covers \Jasny\IteratorPipeline\Aggregator\MaxAggregator
 */
class MaxAggregatorTest extends TestCase
{
    public function testAggregateInt()
    {
        $values = [99, 24, 122];
        $iterator = new \ArrayIterator($values);

        $aggregator = new MaxAggregator($iterator);

        $result = $aggregator();

        $this->assertEquals(122, $result);
        $this->assertSame($iterator, $aggregator->getIterator());
    }

    public function testAggregateNegative()
    {
        $values = [99, 24, -7, -337, 122];
        $iterator = new \ArrayIterator($values);

        $aggregator = new MaxAggregator($iterator);

        $result = $aggregator();

        $this->assertEquals(122, $result);
        $this->assertSame($iterator, $aggregator->getIterator());
    }

    public function testAggregateFloat()
    {
        $values = [9.9, 99.1, 7.5, 8.0];
        $iterator = new \ArrayIterator($values);

        $aggregator = new MaxAggregator($iterator);

        $result = $aggregator();

        $this->assertEquals(99.1, $result);
    }

    public function testAggregateAlpha()
    {
        $values = ["Charlie", "Bravo", "Alpha", "Foxtrot", "Delta"];
        $iterator = new \ArrayIterator($values);

        $aggregator = new MaxAggregator($iterator);

        $result = $aggregator();

        $this->assertEquals("Foxtrot", $result);
    }

    public function testAggregateCallback()
    {
        $values = [
            (object)['num' => 1, 'name' => "Charlie"],
            (object)['num' => 2, 'name' => "Bravo"],
            (object)['num' => 3, 'name' => "Alpha"],
            (object)['num' => 4, 'name' => "Foxtrot"],
            (object)['num' => 5, 'name' => "Delta"],
            (object)['num' => 6, 'name' => "Alpha"]
        ];
        $iterator = new \ArrayIterator($values);

        $aggregator = new MaxAggregator($iterator, function(\stdClass $a, \stdClass $b) {
            return $a->name <=> $b->name;
        });

        $result = $aggregator();

        $this->assertSame($values[3], $result);
    }

    public function testAggregateEmpty()
    {
        $aggregator = new MaxAggregator(new \EmptyIterator());

        $result = $aggregator();

        $this->assertNull($result);
    }
}