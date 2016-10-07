<?php

namespace Jaimeeee\Panel\Fields\Date;

use App;
use Jaimeeee\Panel\Fields\Text\TextField;
use Jaimeeee\Panel\HTMLBrick;

class DateField extends TextField
{
    public static $format = 'yyyy-mm-dd';

    /**
     * Creates the field code.
     *
     * @return HTMLBrick
     */
    public function field()
    {
        $input = new HTMLBrick('input', $this->label, [
            'type'        => $this->type,
            'id'          => $this->id,
            'name'        => $this->name,
            'placeholder' => strtoupper(self::$format),
        ]);
        if ($this->value) {
            $input->attr['value'] = $this->value;
        }
        $input->addClass('form-control');
        $input->addClass('date-field');

        return $input;
    }

    /**
     * Add stuff to the header.
     *
     * @return string
     */
    public static function header()
    {
        return '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/css/bootstrap-datepicker.min.css">';
    }

    /**
     * Add stuff to the footer.
     *
     * @return string
     */
    public static function footer()
    {
        $code = '<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/js/bootstrap-datepicker.min.js"></script>'.chr(10);

        if (App::getLocale() != 'en') {
            $code .= '  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/locales/bootstrap-datepicker.'.App::getLocale().'.min.js"></script>'.chr(10);
        }

        $code .= '  <script>
    $(\'input.date-field\').datepicker({
      autoclose: true,
      format: \''.self::$format.'\',
      language: \''.App::getLocale().'\',
      todayHighlight: true
    });
  </script>';

        return $code;
    }
}
