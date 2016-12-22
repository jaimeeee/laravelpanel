<?php

namespace Jaimeeee\Panel\Fields\Select;

use Jaimeeee\Panel\Fields\BaseField;
use Jaimeeee\Panel\HTMLBrick;

class SelectField extends BaseField
{
    public $type = 'select';

    /**
     * Creates the field code.
     *
     * @return HTMLBrick
     */
    public function field()
    {
        $optionsCode = '';
        
        if (is_array($this->options['options'])) {
            foreach ($this->options['options'] as $key => $value) {
                $optionsCode .= '<option value="'.$key.'"'.($this->value == $key ?
                    ' selected="selected"' : null).'>'.
                    $value.'</option>';
            }
        } elseif ($optionClass = $this->options['options']) {
            $options = $optionClass::orderBy((isset($this->options['optionsSort']) ? $this->options['optionsSort'] : 'id'),
                                              (isset($this->options['optionsSortOrder']) ? $this->options['optionsSortOrder'] : 'asc'))->get();
            
            foreach ($options as $option) {
                $optionValue = isset($this->options['optionsValue']) ? $this->options['optionsValue'] : 'id';
                $optionShow = isset($this->options['optionsShow']) ? $this->options['optionsShow'] : 'name';

                $optionsCode .= '<option value="'.$option->$optionValue.'"'.($this->value == $option->$optionValue ?
                    ' selected="selected"' : null).'>'.
                    $option->$optionShow.'</option>';
            }
        }

        $input = new HTMLBrick('select', $optionsCode, [
            'id'   => $this->id,
            'name' => $this->name,
        ]);
        $input->addClass('form-control');

        return $input;
    }
}
