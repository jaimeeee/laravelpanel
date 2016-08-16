<?php

namespace Jaimeeee\Panel;

use Jaimeeee\Panel\Fields\Hidden\HiddenField;
use Jaimeeee\Panel\HTMLBrick;

class Form extends HTMLBrick
{
    protected $entity;
    protected $record;
    protected $errors;
    
    /**
     * Create a new instance for the entity
     * @param Entity   $entity
     * @param Eloquent $record An eloquent object of a record
     */
    public function __construct($entity, $record = null, $errors = null)
    {
        $this->entity = $entity;
        $this->record = $record;
        $this->errors = $errors;
    }
    
    /**
     * Return the HTML code of the form
     * @return string HTML code
     */
    public function code()
    {
        // Set the CSRF token field
        $token = new HiddenField('_token', [
                        'value' => csrf_token(),
                    ]);
        $code = $token->field();
        
        // Go through each field on the blueprint
        foreach ($this->entity->fields as $name => $options) {
            $type = ucwords($options['type']);
            $className = 'Jaimeeee\\Panel\\Fields\\' . $type . '\\' . $type . 'Field';
            $field = new $className($name, $options);
            
            if ($this->record) $field->value = $this->record->$name;
            if (old($name)) $field->value = old($name);
            if ($this->errors && $this->errors->has($name)) $field->hasError = true;
            
            $code .= $field->code();
        }
        
        /**
         * Submit button
         */
        $submitButton = new HTMLBrick('button',
                            $this->record ? trans('panel::global.edit_entity', ['entity' => $this->entity->name()]) :
                            trans('panel::global.save_entity', ['entity' => $this->entity->name()]),
                        [
                            'type' => 'submit',
                            'style' => 'margin-left: 8px',
                        ]);
        $submitButton->addClass('btn btn-primary');
        
        $cancelButton = new HTMLBrick('a', trans('panel::global.cancel'), [
                            'href' => url(config('panel.url') . '/' . $this->entity->url),
                        ]);
        $cancelButton->addClass('btn btn-default');
        
        $buttonsContainer = new HTMLBrick('div', $cancelButton . $submitButton);
        $buttonsContainer->addClass('col-lg-offset-2 col-lg-9');
        $buttonsContainer = new HTMLBrick('div', $buttonsContainer);
        $buttonsContainer->addClass('form-group');
        
        $code .= $buttonsContainer;
        
        /**
         * Form
         */
        if ($this->record)
            $action = url(config('panel.url') . '/' . $this->entity->url . '/' . $this->record->id);
        else
            $action = url(config('panel.url') . '/' . $this->entity->url . '/create');
        
        $formCode = new HTMLBrick('form', $code, [
            'action' => $action,
            'method' => 'post',
            'enctype' => 'multipart/form-data',
        ]);
        $formCode->addClass('form-horizontal');
        
        return $formCode;
    }
    
    /**
     * Return each fields' header code
     * 
     * @return string
     */
    private function header()
    {
        $fields = collect($this->entity->fields)->unique('type')->all();
        $codeArray = [];
        
        foreach ($fields as $name => $options) {
            $type = ucwords($options['type']);
            $className = 'Jaimeeee\\Panel\\Fields\\' . $type . '\\' . $type . 'Field';
            
            if (method_exists($className, 'header'))
                $codeArray[] = '  ' . $className::header();
        }
        
        return implode(chr(10), $codeArray);
    }
    
    /**
     * Return each fields' footer code
     * 
     * @return string
     */
    private function footer()
    {
        $fields = collect($this->entity->fields)->unique('type')->all();
        $codeArray = [];
        
        foreach ($fields as $name => $options) {
            $type = ucwords($options['type']);
            $className = 'Jaimeeee\\Panel\\Fields\\' . $type . '\\' . $type . 'Field';
            
            if (method_exists($className, 'footer'))
                $codeArray[] = '  ' . $className::footer();
        }
        
        return implode(chr(10), $codeArray);
    }
    
    /**
     * Return the form view
     * @return \Illuminate\Http\Response
     */
    public function view()
    {
        $code = $this->code();
        
        return view('panel::form', [
                        'entity' => $this->entity,
                        'header' => $this->header(),
                        'record' => $this->record,
                        'title' => $this->entity->title,
                        'panel' => $this->record ? trans('panel::global.edit_entity', ['entity' => $this->entity->name()]) : trans('panel::global.save_entity', ['entity' => $this->entity->name()]),
                        'footer' => $this->footer(),
                        'formCode' => $code,
                    ]);
    }
}
