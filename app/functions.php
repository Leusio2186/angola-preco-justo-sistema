<?php

function html(mixed $value)
{
    return htmlspecialchars($value);
}

function dd(mixed $value)
{
    echo "<pre>";
    var_dump($value);
    echo "</pre>";
}
