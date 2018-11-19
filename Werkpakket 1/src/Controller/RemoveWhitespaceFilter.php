<?php

namespace App\Controller;

class RemoveWhitespaceFilter implements \Waavi\Sanitizer\Contracts\Filter
{
    public function apply($value, $options = [])
    {
        return str_replace(' ', '', $value);
    }
}