<?php

namespace Jaimeeee\Panel\Fields\Label;

use Jaimeeee\Panel\Fields\BaseField;
use Jaimeeee\Panel\HTMLBrick;

class LabelField extends BaseField
{
    public $placeholder;

    /**
     * Creates the field code.
     *
     * @return HTMLBrick
     */
    public function field()
    {
        $label = new HTMLBrick('p', $this->value);
        $label->addClass('form-control-static');

        return $label;
    }
}
