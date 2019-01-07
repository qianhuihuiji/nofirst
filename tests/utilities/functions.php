<?php

function create($class, $attributes = [], $times = null)
{
    return factory($class, $times)->create($attributes);
}

function make($class, $attributes = [], $times = null)
{
    return factory($class, $times)->make($attributes);
}

function row($class, $attributes = [], $times = null)
{
    return factory($class, $times)->row($attributes);
}
