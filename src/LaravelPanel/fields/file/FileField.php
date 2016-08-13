<?php

namespace Jaimeeee\Panel\Fields\File;

use Jaimeeee\Panel\HTMLBrick;
use Jaimeeee\Panel\Fields\InputField;

class FileField extends InputField
{
    public static $ignore = true;
    public $type = 'file';
    
    /**
     * Creates the field code
     * @return HTMLBrick
     */
    public function field()
    {
        $input = new HTMLBrick('input', $this->label, [
            'type' => $this->type,
            'id' => $this->id,
            'name' => $this->name,
        ]);
        $input->addClass('form-control');
        
        return $input;
    }
}
