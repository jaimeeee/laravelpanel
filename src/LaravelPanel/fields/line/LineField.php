<?php

namespace Jaimeeee\Panel\Fields\Line;

use Jaimeeee\Panel\HTMLBrick;

class LineField
{
    public $type = 'hidden';
    
    /**
     * Creates the field code
     * @return HTMLBrick
     */
    public function code()
    {
        return '<hr>';
    }
}
