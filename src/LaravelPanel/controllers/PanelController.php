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
    private $ignoredTypes = ['file', 'line'];
    
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
     * @return \Illuminate\Http\Response
     */
    public function list($entity)
    {
        if ($entity = $this->entityFromYamlFile($entity)) {
            $list = new FormList($entity);
            
            return $list->view();
        }
        else
            abort(404);
    }
    
    /**
     * Show an entity list
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
     * Show an entity list
     * @return \Illuminate\Http\Response
     */
    public function publish(Request $request, $entity)
    {
        if ($entity = $this->entityFromYamlFile($entity)) {
            
            $validationRules = [];
            foreach ($entity->fields as $name => $options) {
                if (isset($options['validate']))
                    $validationRules[$name] = $options['validate'];
            }
            $this->validate($request, $validationRules);
            
            // If data is validated and everything seems good, lets create the record
            $record = new $entity->class();
            foreach ($entity->fields as $name => $options) {
                if (!in_array($options['type'], $this->ignoredTypes))
                    $record->$name = $request->input($name);
            }
            $record->save();
            
            return redirect(config('panel.url') . '/' . $entity->url . '?created=1');
        }
        else
            abort(404);
    }
    
    /**
     * Show an entity list
     * @return \Illuminate\Http\Response
     */
    public function edit($entity, $id)
    {
        if ($entity = $this->entityFromYamlFile($entity)) {
            // Get the record from the database, or fail if it is not found
            $record = $entity->class::findOrFail($id);
            
            $form = new Form($entity, $record, Session::get('errors'));
            
            return $form->view();
        }
        else
            abort(404);
    }
    
    /**
     * Show an entity list
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $entity, $id)
    {
        if ($entity = $this->entityFromYamlFile($entity)) {
            // Get the record from the database, or fail if it is not found
            $record = $entity->class::findOrFail($id);
            
            $validationRules = [];
            foreach ($entity->fields as $name => $options) {
                if (isset($options['validate']))
                    $validationRules[$name] = $options['validate'];
                if (isset($options['validateAtEdit']))
                    $validationRules[$name] = $options['validateAtEdit'];
            }
            $this->validate($request, $validationRules);
            
            // If data is validated and everything seems good, lets update the record
            foreach ($entity->fields as $name => $options) {
                if (!in_array($options['type'], $this->ignoredTypes))
                    $record->$name = $request->input($name);
            }
            $record->save();
            
            return redirect(config('panel.url') . '/' . $entity->url . '?updated=1');
        }
        else
            abort(404);
    }
    
    /**
     * Show an entity list
     * @return \Illuminate\Http\Response
     */
    public function delete($entity, $id)
    {
        if ($entity = $this->entityFromYamlFile($entity)) {
            // Get the record from the database, or fail if it is not found
            $record = $entity->class::findOrFail($id);
            
            // TODO: Find and delete created images
            
            $record->delete();
            
            return redirect(config('panel.url') . '/' . $entity->url . '?deleted=1');
        }
        else
            abort(404);
    }
    
    /**
     * Get an Entity object from a Yaml file
     * @param  string $entity The entity name
     * @return \Jaimeeee\Panel\Entity
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
}
