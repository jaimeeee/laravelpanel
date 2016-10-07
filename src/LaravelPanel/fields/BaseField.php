<?php

namespace Jaimeeee\Panel\Fields;

use Jaimeeee\Panel\HTMLBrick;

class BaseField
{
    public $id;
    public $name;
    public $label;
    public $options;
    public $value;
    public $disabled = false;
    public $readOnly = false;
    public $hasError = false;

    public function __construct($name, $options)
    {
        $this->id = $name;
        $this->name = $name;
        $this->options = $options;

        if (isset($options['label'])) {
            $this->label = $options['label'];
        }
        if (isset($options['placeholder'])) {
            $this->placeholder = $options['placeholder'];
        }
        if (isset($options['value'])) {
            $this->value = $options['value'];
        }
        if (isset($options['readOnly'])) {
            $this->readOnly = $options['readOnly'] ? true : false;
        }
        if (isset($options['disabled'])) {
            $this->disabled = $options['disabled'] ? true : false;
        }
        if (isset($options['hasError'])) {
            $this->hasError = $options['hasError'] ? true : false;
        }
    }

    /**
     * Show the div container.
     *
     * @return string HTML code
     */
    public function code()
    {
        $fieldWrapper = new HTMLBrick('div', $this->field());
        $fieldWrapper->addClass('col-lg-9');

        $formGroup = new HTMLBrick('div', $this->label().$fieldWrapper);
        $formGroup->addClass('form-group');
        if ($this->hasError) {
            $formGroup->addClass('has-error');
        }

        return $formGroup;
    }

    /**
     * Creates the label code.
     *
     * @return HTMLBrick
     */
    public function label()
    {
        if (!$this->label) {
            return;
        }

        $label = new HTMLBrick('label', $this->label);
        $label->attr['for'] = $this->id;
        $label->addClass('col-lg-2');
        $label->addClass('control-label');

        return $label;
    }
}
