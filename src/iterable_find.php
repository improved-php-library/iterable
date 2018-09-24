<?php

declare(strict_types=1);

namespace Jasny;

/**
 * Get the first element that matches a condition.
 * Returns null if no element is found.
 *
 * @param iterable $iterable
 * @param callable $matcher
 * @return mixed
 */
function iterable_first(iterable $iterable, callable $matcher = null)
{
    foreach ($iterable as $key => $value) {
        if ((bool)call_user_func($matcher, $value, $key)) {
            return $value;
        }
    }

    return null;
}