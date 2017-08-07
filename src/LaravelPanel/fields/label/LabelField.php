<?php

namespace Jaimeeee\Panel\Fields\Label;

use Jaimeeee\Panel\HTMLBrick;

class LabelField
{
    public static $ignore = true;

    public $label;
    public $value;

    public function __construct($name, $options)
    {
        if (isset($options['label'])) {
            $this->label = $options['label'];
        }
        if (isset($options['value'])) {
            $this->value = $options['value'];
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
        $label->addClass('col-lg-2');
        $label->addClass('control-label');

        return $label;
    }

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
