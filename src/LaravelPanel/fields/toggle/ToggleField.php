<?php

namespace Jaimeeee\Panel\Fields\Toggle;

use Jaimeeee\Panel\HTMLBrick;
use Jaimeeee\Panel\Fields\BaseField;

class ToggleField extends BaseField
{
    public $type = 'select';
    
    /**
     * Creates the field code
     * @return HTMLBrick
     */
    public function field()
    {
        $yesOption = '<option value="1"' . ($this->value === 1 ? ' selected="selected"' : null) . '>' .
            trans('panel::global.yes') . '</option>';
        $noOption = '<option value="0"' . ($this->value === 0 ? ' selected="selected"' : null) . '>' .
            trans('panel::global.no') . '</option>';
        
        $input = new HTMLBrick('select', $yesOption . $noOption, [
            'id' => $this->id,
            'name' => $this->name,
        ]);
        $input->addClass('form-control');
        
        return $input;
    }
}
