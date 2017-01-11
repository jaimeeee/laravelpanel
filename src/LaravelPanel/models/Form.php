<?php

namespace Jaimeeee\Panel;

use Jaimeeee\Panel\Fields\Hidden\HiddenField;

class Form extends HTMLBrick
{
    protected $entity;
    protected $record;
    protected $errors;
    protected $parent;
    protected $parentEntity;

    /**
     * Create a new instance for the entity.
     *
     * @param Entity   $entity
     * @param Eloquent $record An eloquent object of a record
     */
    public function __construct($entity, $record = null, $errors = null, $parentEntity = null, $parent = null)
    {
        $this->entity = $entity;
        $this->record = $record;
        $this->errors = $errors;
        $this->parentEntity = $parentEntity;
        $this->parent = $parent;
    }

    /**
     * Return the HTML code of the form.
     *
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
            $className = 'Jaimeeee\\Panel\\Fields\\'.$type.'\\'.$type.'Field';
            $field = new $className($name, $options);

            if ($this->record) {
                $field->value = $this->record->$name;
            }
            if (old($name)) {
                $field->value = old($name);
            }
            if ($this->errors && $this->errors->has($name)) {
                $field->hasError = true;
            }

            $code .= $field->code();
        }

        /*
         * Submit button
         */
        $submitButton = new HTMLBrick('button',
                            $this->record ? trans('panel::global.edit_entity', ['entity' => $this->entity->name()]) :
                            trans('panel::global.save_entity', ['entity' => $this->entity->name()]),
                        [
                            'type'  => 'submit',
                            'style' => 'margin-left: 8px',
                        ]);
        $submitButton->addClass('btn btn-primary');

        if ($this->parentEntity) {
            $cancelButton = new HTMLBrick('a', trans('panel::global.cancel'), [
                                'href' => url(config('panel.url').'/'.$this->parentEntity->url.'/'.$this->parent->id.'/'.$this->entity->url),
                            ]);
        } else {
            $cancelButton = new HTMLBrick('a', trans('panel::global.cancel'), [
                                'href' => url(config('panel.url').'/'.$this->entity->url),
                            ]);
        }
        $cancelButton->addClass('btn btn-default');

        $buttonsContainer = new HTMLBrick('div', $cancelButton.$submitButton);
        $buttonsContainer->addClass('col-lg-offset-2 col-lg-9');
        $buttonsContainer = new HTMLBrick('div', $buttonsContainer);
        $buttonsContainer->addClass('form-group');

        $code .= $buttonsContainer;

        /*
         * Form
         */
        if ($this->parentEntity) {
            if ($this->record) {
                $action = url(config('panel.url').'/'.$this->parentEntity->url.'/'.$this->parent->id.'/'.$this->entity->url.'/edit/'.$this->record->id);
            } else {
                $action = url(config('panel.url').'/'.$this->parentEntity->url.'/'.$this->parent->id.'/'.$this->entity->url.'/create');
            }
        } else {
            if ($this->record) {
                $action = url(config('panel.url').'/'.$this->entity->url.'/edit/'.$this->record->id);
            } else {
                $action = url(config('panel.url').'/'.$this->entity->url.'/create');
            }
        }

        $formCode = new HTMLBrick('form', $code, [
            'action'  => $action,
            'method'  => 'post',
            'enctype' => 'multipart/form-data',
        ]);
        $formCode->addClass('form-horizontal');

        return $formCode;
    }

    /**
     * Return each fields' header code.
     *
     * @return string
     */
    private function header()
    {
        $fields = collect($this->entity->fields)->unique('type')->all();
        $codeArray = [];

        foreach ($fields as $name => $options) {
            $type = ucwords($options['type']);
            $className = 'Jaimeeee\\Panel\\Fields\\'.$type.'\\'.$type.'Field';

            if (method_exists($className, 'header')) {
                $codeArray[] = '  '.$className::header();
            }
        }

        return implode(chr(10), $codeArray);
    }

    /**
     * Return each fields' footer code.
     *
     * @return string
     */
    private function footer()
    {
        $fields = collect($this->entity->fields)->unique('type')->all();
        $codeArray = [];

        foreach ($fields as $name => $options) {
            $type = ucwords($options['type']);
            $className = 'Jaimeeee\\Panel\\Fields\\'.$type.'\\'.$type.'Field';

            if (method_exists($className, 'footer')) {
                $codeArray[] = '  '.$className::footer();
            }
        }

        return implode(chr(10), $codeArray);
    }

    /**
     * Return the form view.
     *
     * @return \Illuminate\Http\Response
     */
    public function view()
    {
        $code = $this->code();

        // If there are images supposed to appear on the editor, lets find them
        $imageList = [];
        if (isset($this->entity->images['class']) && $imageClass = $this->entity->images['class']) {
            $images = $imageClass::orderBy(isset($this->entity->images['field']) ? $this->entity->images['field'] : 'created_at',
                                           isset($this->entity->images['order']) ? $this->entity->images['order'] : 'desc')
                      ->get();

            foreach ($images as $image) {
                $value = isset($this->entity->images['value']) ?
                                $image->$this->entity->images['value'] :
                                $image->image_name;

                $imageList[] = [
                    'title' => (isset($this->entity->images['title']) ? $image->$this->entity->images['title'] : $image->description) ?: $value,
                    'value' => asset(rtrim($this->entity->images['path'], '/').'/'.$value),
                ];
            }
        }

        return view('panel::form', [
                        'entity'    => $this->entity,
                        'header'    => $this->header(),
                        'record'    => $this->record,
                        'title'     => $this->entity->title,
                        'panel'     => $this->record ? trans('panel::global.edit_entity', ['entity' => $this->entity->name()]) : trans('panel::global.save_entity', ['entity' => $this->entity->name()]),
                        'footer'    => $this->footer(),
                        'formCode'  => $code,
                        'imageList' => $imageList,
                    ]);
    }
}
