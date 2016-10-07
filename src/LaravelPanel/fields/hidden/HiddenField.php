<?php

namespace Jaimeeee\Panel\Fields\Hidden;

use Jaimeeee\Panel\Fields\InputField;
use Jaimeeee\Panel\HTMLBrick;

class HiddenField extends InputField
{
    public $type = 'hidden';

    /**
     * Creates the field code.
     *
     * @return HTMLBrick
     */
    public function field()
    {
        $input = new HTMLBrick('input', $this->label, [
            'type'  => $this->type,
            'id'    => $this->id,
            'name'  => $this->name,
            'value' => $this->value,
        ]);
        $input->addClass('form-control');

        return $input;
    }
}
