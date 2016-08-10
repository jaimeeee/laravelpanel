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
        $token = new HiddenField('_token', [
                        'value' => csrf_token(),
                    ]);
        $code = $token->field();
        
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
        $submitButton = new HTMLBrick('button', ($this->record ? 'Edit ' : 'Save ') . $this->entity->name, [
                            'type' => 'submit',
                            'style' => 'margin-left: 8px',
                        ]);
        $submitButton->addClass('btn btn-primary');
        
        $cancelButton = new HTMLBrick('a', 'Cancel', [
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
     * Return the form view
     * @return \Illuminate\Http\Response
     */
    public function view()
    {
        $code = $this->code();
        
        return view('panel::form', [
                        'entity' => $this->entity,
                        'record' => $this->record,
                        'title' => $this->entity->title,
                        'formCode' => $code,
                    ]);
    }
}
