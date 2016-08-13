<?php

namespace Jaimeeee\Panel\Fields\Image;

use Jaimeeee\Panel\HTMLBrick;
use Jaimeeee\Panel\Fields\InputField;
use Intervention\Image\ImageManagerStatic as Image;

class ImageField extends InputField
{
    public static $ignore = true;
    public $type = 'file';
    
    /**
     * Creates the field code
     * @return HTMLBrick
     */
    public function field()
    {
        $input = new HTMLBrick('input', $this->label, [
            'type' => $this->type,
            'id' => $this->id,
            'name' => $this->name,
        ]);
        $input->addClass('form-control');
        
        return $input;
    }
    
    /**
     * Upload images to desired route
     * 
     * @param  Request    $request  Request sent by the form
     * @param  AnyObject  $record   The object that was created or updated
     * @param  string     $field    The field's name
     * @param  array      $options  The field's options
     * @return boolean              A boolean showing if the upload was successful or not
     */
    static function call(\Illuminate\Http\Request $request, $record, $field, $options)
    {
        if ($request->hasFile($field)) {
            $directory = public_path(isset($options['path']) ? $options['path'] : 'storage/images') . '/';
            
            $filename = $record->id; // TODO: Allow file format changes; ex: {id}_{rand:5}_{timestamp}
            
            $originalFilename = $filename . '_o.' . $request->file($field)->guessExtension();
            
            $request->file($field)->move($directory, $originalFilename);
            
            if (isset($options['sizes'])) {
                foreach ($options['sizes'] as $value) {
                    $method = 'resize';
                    if (is_array($value)) {
                        reset($value);
                        $method = key($value);
                        $sizeValue = $value[$method];
                    } else {
                        $sizeValue = $value;
                    }
                    
                    $size = explode('x', $sizeValue);
                    
                    Image::make($directory . $originalFilename)
                        ->$method($size[0], isset($size[1]) ? $size[1] : null, function ($constraints) {
                            $constraints->aspectRatio();
                        })
                        ->interlace()
                        ->save($directory .
                               (isset($value['prefix']) ? $value['prefix'] : null) .
                               $filename . '_' .
                               (isset($value['suffix']) ? $value['suffix'] : $sizeValue) .
                               '.jpg')
                        ->destroy();
                }
            }
        }
    }
}
