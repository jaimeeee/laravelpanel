<?php

namespace Jaimeeee\Panel\Fields\Select;

use Jaimeeee\Panel\HTMLBrick;
use Jaimeeee\Panel\Fields\BaseField;

class SelectField extends BaseField
{
    public $type = 'select';
    
    /**
     * Creates the field code
     * @return HTMLBrick
     */
    public function field()
    {
        if (is_array($this->options['options'])) {
            // TODO: Show custom options, not from a database
        }
        else if ($optionClass = $this->options['options']) {
            $options = $optionClass::orderBy((isset($this->options['optionsSort']) ? $this->options['optionsSort'] : 'id'),
                                              (isset($this->options['optionsSortOrder']) ? $this->options['optionsSortOrder'] : 'asc'))->get();
        }
        
        $optionsCode = '';
        foreach ($options as $option) {
            $optionValue = isset($this->options['optionsValue']) ? $this->options['optionsValue'] : 'id';
            $optionShow = isset($this->options['optionsShow']) ? $this->options['optionsShow'] : 'name';
            
            $optionsCode .= '<option value="' . $option->$optionValue . '"' . ($this->value == $option->$optionValue ?
                ' selected="selected"' : null) . '>' .
                $option->$optionShow . '</option>';
        }
        
        $input = new HTMLBrick('select', $optionsCode, [
            'id' => $this->id,
            'name' => $this->name,
        ]);
        $input->addClass('form-control');
        
        return $input;
    }
}
