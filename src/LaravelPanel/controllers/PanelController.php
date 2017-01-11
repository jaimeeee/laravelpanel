<?php

namespace Jaimeeee\Panel\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Jaimeeee\Panel\Entity;
use Jaimeeee\Panel\Form;
use Jaimeeee\Panel\FormList;
use Session;

class PanelController extends Controller
{
    /**
     * Create a new controller instance, using the auth middleware.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the panel dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function dashboard()
    {
        return view('panel::home', [
                        'title' => 'Dashboard',
                    ]);
    }

    /**
     * Show an entity list.
     *
     * @return \Illuminate\Http\Response
     */
    public function formList($entity)
    {
        if ($entity = Entity::fromYamlFile($entity)) {
            $list = new FormList($entity);

            return $list->view();
        } else {
            abort(404);
        }
    }

    public function childrenList($entity, $id, $child)
    {
        if (($parentEntity = Entity::fromYamlFile($entity)) && ($childEntity = Entity::fromYamlFile($child))) {
            $parentRecord = $parentEntity->class::findOrFail($id);

            $list = new FormList($parentEntity, $parentRecord, $childEntity);

            return $list->childView();
        } else {
            abort(404);
        }
    }

    /**
     * Show the form to create a new record.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($entity, $record = null, $child = null)
    {
        if (!$record && !$child && ($entityObject = Entity::fromYamlFile($entity))) {
            $form = new Form($entityObject, null, Session::get('errors'));

            return $form->view();
        } elseif (($parentEntity = Entity::fromYamlFile($entity)) && ($entityObject = Entity::fromYamlFile($child))) {
            // Get parent's record
            $parentRecord = $parentEntity->class::findOrFail($record);

            $form = new Form($entityObject, null, Session::get('errors'), $parentEntity, $parentRecord);

            return $form->view();
        } else {
            abort(404);
        }
    }

    /**
     * Submit the new record.
     *
     * @return \Illuminate\Http\Response
     */
    public function publish(Request $request, $entity, $record = null, $child = null)
    {
        if (!$record && !$child && ($entityObject = Entity::fromYamlFile($entity))) {
        } elseif (($parentEntity = Entity::fromYamlFile($entity)) && ($entityObject = Entity::fromYamlFile($child))) {
            // Get parent's record
            $parentRecord = $parentEntity->class::findOrFail($record);
        } else {
            abort(404);
        }

        // Get validation from entity rules and validate
        $this->validate($request, $this->validationRules($entityObject->fields));

        // If data is validated and everything seems good, lets create the record
        $record = new $entityObject->class();

        foreach ($entityObject->fields as $field => $options) {
            $type = ucwords($options['type']);
            $className = 'Jaimeeee\\Panel\\Fields\\'.$type.'\\'.$type.'Field';

            if (!isset($className::$ignore) || !$className::$ignore) {
                $record->$field = $request->input($field);
            }
        }

        // Save slug
        if (isset($entityObject->slug['field']) && $entityObject->slug['field'] &&
            isset($entityObject->slug['column']) && $entityObject->slug['column']) {
            $field = $entityObject->slug['field'];
            $column = $entityObject->slug['column'];

            $record->$column = str_slug($record->$field,
                                            isset($entityObject->slug['separator']) ? $entityObject->slug['separator'] : '-');
        }

        // Save parent
        if (isset($parentRecord) && $parentRecord) {
            $row = snake_case(class_basename($parentEntity->class)).'_id';

            $record->$row = $parentRecord->id;
        }

        $record->save();

        foreach ($entityObject->fields as $field => $options) {
            $type = ucwords($options['type']);
            $className = 'Jaimeeee\\Panel\\Fields\\'.$type.'\\'.$type.'Field';

            // Lets see if the call method exists, and if it does, we should trust the field ¯\_(ツ)_/¯
            if (method_exists($className, 'call')) {
                $className::call($request, $record, $field, $options);
            }
        }

        $record->save();

        // Upload single images
        // $this->uploadImages($request, $record, $entityObject);

        if (isset($parentRecord) && $parentRecord) {
            return redirect(config('panel.url').'/'.$parentEntity->url.'/'.$parentRecord->id.'/'.$entityObject->url.'?created=1');
        } else {
            return redirect(config('panel.url').'/'.$entityObject->url.'?created=1');
        }
    }

    /**
     * Show the form to edit a record.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($entity, $id, $child = null, $record = null)
    {
        if (!$record && !$child && ($entityObject = Entity::fromYamlFile($entity))) {
            // Get the record from the database, or fail if it is not found
            $entityClass = $entityObject->class;
            $record = $entityClass::findOrFail($id);

            $form = new Form($entityObject, $record, Session::get('errors'));

            return $form->view();
        } elseif (($parentEntity = Entity::fromYamlFile($entity)) && ($entityObject = Entity::fromYamlFile($child))) {
            // Get parent's record
            $parentRecord = $parentEntity->class::findOrFail($id);

            // Get the record from the database, or fail if it is not found
            $entityClass = $entityObject->class;
            $childObject = $entityClass::findOrFail($record);

            $form = new Form($entityObject, $childObject, Session::get('errors'), $parentEntity, $parentRecord);

            return $form->view();
        } else {
            abort(404);
        }
    }

    /**
     * Update a record.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $entity, $id, $child = null, $record = null)
    {
        if (!$record && !$child && ($entityObject = Entity::fromYamlFile($entity))) {
            // Get the record from the database, or fail if it is not found
            $entityClass = $entityObject->class;
            $recordObject = $entityClass::findOrFail($id);
        } elseif (($parentEntity = Entity::fromYamlFile($entity)) && ($entityObject = Entity::fromYamlFile($child))) {
            // Get parent's record
            $parentRecord = $parentEntity->class::findOrFail($id);

            // Get the record from the database, or fail if it is not found
            $entityClass = $entityObject->class;
            $recordObject = $entityClass::findOrFail($record);
        } else {
            abort(404);
        }

        // Get validation from entity rules and validate
        $this->validate($request, $this->validationRules($entityObject->fields, true));

        // If data is validated and everything seems good, lets update the record
        foreach ($entityObject->fields as $field => $options) {
            $type = ucwords($options['type']);
            $className = 'Jaimeeee\\Panel\\Fields\\'.$type.'\\'.$type.'Field';

            if (!isset($className::$ignore) || !$className::$ignore) {
                $recordObject->$field = $request->input($field);
            }

            // Lets see if the call method exists, and if it does, we should trust the field ¯\_(ツ)_/¯
            if (method_exists($className, 'call')) {
                $className::call($request, $recordObject, $field, $options);
            }
        }

        // Edit slug
        if (isset($entityObject->slug['field']) && $entityObject->slug['field'] &&
            isset($entityObject->slug['column']) && $entityObject->slug['column']) {
            $field = $entityObject->slug['field'];
            $column = $entityObject->slug['column'];

            $recordObject->$column = str_slug($recordObject->$field,
                                            isset($entityObject->slug['separator']) ? $entityObject->slug['separator'] : '-');
        }

        $recordObject->save();

        // Upload single images
        // $this->uploadImages($request, $recordObject, $entityObject);

        if (isset($parentRecord) && $parentRecord) {
            return redirect(config('panel.url').'/'.$parentEntity->url.'/'.$parentRecord->id.'/'.$entityObject->url.'?updated=1');
        } else {
            return redirect(config('panel.url').'/'.$entityObject->url.'?updated=1');
        }
    }

    /**
     * Delete a record.
     *
     * @return \Illuminate\Http\Response
     */
    public function delete($entity, $id, $child = null, $record = null)
    {
        if (!$record && !$child && ($entityObject = Entity::fromYamlFile($entity))) {
            // Get the record from the database, or fail if it is not found
            $entityClass = $entityObject->class;
            $recordObject = $entityClass::findOrFail($id);

            // TODO: Find and delete created images

            $recordObject->delete();

            return redirect(config('panel.url').'/'.$entity->url.'?deleted=1');
        } elseif (($parentEntity = Entity::fromYamlFile($entity)) && ($entityObject = Entity::fromYamlFile($child))) {
            // Get parent's record
            $parentRecord = $parentEntity->class::findOrFail($id);

            // Get the record from the database, or fail if it is not found
            $entityClass = $entityObject->class;
            $childObject = $entityClass::findOrFail($record);

            // TODO: Find and delete created images

            $childObject->delete();

            return redirect(config('panel.url').'/'.$parentEntity->url.'/'.$parentRecord->id.'/'.$entityObject->url.'?deleted=1');
        } else {
            abort(404);
        }
    }

    /**
     * Return the validation rules for each field.
     *
     * @param array $fields Array of fields
     * @param bool  $edit   If it needs to read additional rules at edit
     *
     * @return string
     */
    private function validationRules($fields, $edit = false)
    {
        $validationRules = [];

        // Go through each field to find validation rules
        foreach ($fields as $name => $options) {
            if (isset($options['validate'])) {
                $validationRules[$name] = $options['validate'];

                // Change validation rules if there's validations as edit
                if ($edit && isset($options['validateAtEdit'])) {
                    $validationRules[$name] = $options['validateAtEdit'];
                }

                if ($options['type'] == 'image') {
                    $rules = explode('|', $validationRules[$name]);

                    // If there isn't a validation rule for the image, add it
                    if (!in_array('image', $rules)) {
                        $rules[] = 'image';
                    }

                    $validationRules[$name] = implode('|', $rules);
                }
            }
        }

        return $validationRules;
    }
}
