<?php

namespace Jaimeeee\Panel\Controllers;

use Illuminate\Http\Request;
use Jaimeeee\Panel\Entity;
use Jaimeeee\Panel\Form;
use Jaimeeee\Panel\FormList;
use Symfony\Component\Yaml\Yaml;

use File;
use Session;
use App\Http\Controllers\Controller;
use App\Http\Requests;

class PanelController extends Controller
{
    /**
     * Create a new controller instance, using the auth middleware
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    /**
     * Show the panel dashboard
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
     * Show an entity list
     * 
     * @return \Illuminate\Http\Response
     */
    public function formList($entity)
    {
        if ($entity = $this->entityFromYamlFile($entity)) {
            $list = new FormList($entity);
            
            return $list->view();
        }
        else
            abort(404);
    }
    
    /**
     * Show the form to create a new record
     * 
     * @return \Illuminate\Http\Response
     */
    public function create($entity)
    {
        if ($entity = $this->entityFromYamlFile($entity)) {
            $form = new Form($entity, null, Session::get('errors'));
            
            return $form->view();
        }
        else
            abort(404);
    }
    
    /**
     * Submit the new record
     * 
     * @return \Illuminate\Http\Response
     */
    public function publish(Request $request, $entity)
    {
        if ($entity = $this->entityFromYamlFile($entity)) {
            
            // Get validation from entity rules and validate
            $this->validate($request, $this->validationRules($entity->fields));
            
            // If data is validated and everything seems good, lets create the record
            $record = new $entity->class();
            
            foreach ($entity->fields as $field => $options) {
                $type = ucwords($options['type']);
                $className = 'Jaimeeee\\Panel\\Fields\\' . $type . '\\' . $type . 'Field';
                
                if (!isset($className::$ignore) || !$className::$ignore)
                    $record->$field = $request->input($field);
                
                // Lets see if the call method exists, and if it does, we should trust the field ¯\_(ツ)_/¯
                if (method_exists($className, 'call'))
                    $className::call($request, $entity, $record);
            }
            
            $record->save();
            
            // Upload single images
            // $this->uploadImages($request, $record, $entity);
            
            return redirect(config('panel.url') . '/' . $entity->url . '?created=1');
        }
        else
            abort(404);
    }
    
    /**
     * Show the form to edit a record
     * 
     * @return \Illuminate\Http\Response
     */
    public function edit($entity, $id)
    {
        if ($entity = $this->entityFromYamlFile($entity)) {
            // Get the record from the database, or fail if it is not found
            $entityClass = $entity->class;
            $record = $entityClass::findOrFail($id);
            
            $form = new Form($entity, $record, Session::get('errors'));
            
            return $form->view();
        }
        else
            abort(404);
    }
    
    /**
     * Update a record
     * 
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $entity, $id)
    {
        if ($entity = $this->entityFromYamlFile($entity)) {
            // Get the record from the database, or fail if it is not found
            $entityClass = $entity->class;
            $record = $entityClass::findOrFail($id);
            
            // Get validation from entity rules and validate
            $this->validate($request, $this->validationRules($entity->fields, true));
            
            // If data is validated and everything seems good, lets update the record
            foreach ($entity->fields as $field => $options) {
                $type = ucwords($options['type']);
                $className = 'Jaimeeee\\Panel\\Fields\\' . $type . '\\' . $type . 'Field';
                
                if (!isset($className::$ignore) || !$className::$ignore)
                    $record->$field = $request->input($field);
                
                // Lets see if the call method exists, and if it does, we should trust the field ¯\_(ツ)_/¯
                if (method_exists($className, 'call'))
                    $className::call($request, $record, $field, $options);
            }
            
            $record->save();
            
            // Upload single images
            // $this->uploadImages($request, $record, $entity);
            
            return redirect(config('panel.url') . '/' . $entity->url . '?updated=1');
        }
        else
            abort(404);
    }
    
    /**
     * Delete a record
     * 
     * @return \Illuminate\Http\Response
     */
    public function delete($entity, $id)
    {
        if ($entity = $this->entityFromYamlFile($entity)) {
            // Get the record from the database, or fail if it is not found
            $entityClass = $entity->class;
            $record = $entityClass::findOrFail($id);
            
            // TODO: Find and delete created images
            
            $record->delete();
            
            return redirect(config('panel.url') . '/' . $entity->url . '?deleted=1');
        }
        else
            abort(404);
    }
    
    /**
     * Get an Entity object from a Yaml file
     * 
     * @param  string $entity The entity name
     * @return \Panel\Entity
     */
    private function entityFromYamlFile($entity)
    {
        $path = config_path('panel/' . ucwords(str_singular($entity)) . '.yml');
        
        if (file_exists($path)) {
            $entity = new Entity($path);
            return $entity;
        }
        else
            return false;
    }
    
    /**
     * Return the validation rules for each field
     * 
     * @param  array    $fields  Array of fields
     * @param  boolean  $edit    If it needs to read additional rules at edit
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
                if ($edit && isset($options['validateAtEdit']))
                    $validationRules[$name] = $options['validateAtEdit'];
                
                if ($options['type'] == 'image') {
                    $rules = explode('|', $validationRules[$name]);
                    
                    // If there isn't a validation rule for the image, add it
                    if (!in_array('image', $rules))
                        $rules[] = 'image';
                    
                    $validationRules[$name] = implode('|', $rules);
                }
            }
        }
        
        return $validationRules;
    }
}
