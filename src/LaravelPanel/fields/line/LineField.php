<?php

namespace Jaimeeee\Panel\Fields\Line;

use Jaimeeee\Panel\HTMLBrick;

class LineField
{
    public static $ignore = true;

    /**
     * Creates the field code.
     *
     * @return HTMLBrick
     */
    public function code()
    {
        return '<hr>';
    }
}
