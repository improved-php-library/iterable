<?php

namespace Jasny\Tests;

use PHPUnit\Framework\TestCase;
use function Jasny\iterable_first;

/**
 * @covers \Jasny\iterable_first
 */
class IterableFirstTest extends TestCase
{
    use ProvideIterablesTrait;

    public function provider()
    {
        return $this->provideIterables(['one', 'two', 'three'], true);
    }

    /**
     * @dataProvider provider
     */
    public function test($values)
    {
        $result = iterable_first($values);

        $this->assertEquals('one', $result);
    }

    public function testNoWalk()
    {
        $iterator = $this->createMock(\Iterator::class);
        $iterator->expects($this->any())->method('valid')->willReturn(true);
        $iterator->expects($this->once())->method('current')->willReturn('one');

        $result = iterable_first($iterator);

        $this->assertEquals('one', $result);
    }


    public function testEmpty()
    {
        $result = iterable_first(new \EmptyIterator());

        $this->assertNull($result);
    }
}