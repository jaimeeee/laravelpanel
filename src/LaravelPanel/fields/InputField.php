<?php

namespace Jaimeeee\Panel\Fields;

use Jaimeeee\Panel\HTMLBrick;
use Jaimeeee\Panel\Fields\BaseField;

class InputField extends BaseField
{
    public $placeholder;
    
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
        if ($this->placeholder) $input->attr['placeholder'] = $this->placeholder;
        if ($this->value) $input->attr['value'] = $this->value;
        $input->addClass('form-control');
        
        return $input;
    }
}
