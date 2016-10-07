<?php

namespace Jaimeeee\Panel\Fields\Textarea;

use Jaimeeee\Panel\Fields\BaseField;
use Jaimeeee\Panel\HTMLBrick;

class TextareaField extends BaseField
{
    public $type = 'hidden';
    public $rows = 8;

    public function __construct($name, $options)
    {
        parent::__construct($name, $options);

        if (isset($options['rows'])) {
            $this->rows = $options['rows'];
        }
    }

    /**
     * Creates the field code.
     *
     * @return HTMLBrick
     */
    public function field()
    {
        $input = new HTMLBrick('textarea', $this->value, [
            'id'   => $this->id,
            'name' => $this->name,
            'rows' => $this->rows,
        ]);
        $input->addClass('form-control');
        $input->addClass('rich');

        return $input;
    }
}
