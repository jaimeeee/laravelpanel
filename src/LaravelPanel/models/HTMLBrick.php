<?php

namespace Jaimeeee\Panel;

use Jaimeeee\Panel\HTML;

class HTMLBrick
{
    public $tag = '';
    public $attr = [];
    public $html = '';
    
    /**
     * Create a new HTML Brick
     * @param string $tag  <tag>
     * @param string $html Brick HTML content
     * @param array  $attr Attributes array
     */
    public function __construct($tag, $html = '', $attr = [])
    {
        $this->tag = $tag;
        $this->html = $html;
        $this->attr = $attr;
    }
    
    /**
     * Show the Brick as a string of HTML code
     * @return string HTML code
     */
    public function __toString()
    {
        return HTML::tag($this->tag, $this->attr, $this->html);
    }
    
    /**
     * Add class to the "class" attribute
     * @param string $class Class name
     */
    public function addClass($class)
    {
        if (isset($this->attr['class']))
        {
            $classes = explode(' ', $this->attr['class']);
            if (!in_array($class, $classes))
                $classes[] = $class;
            
            $this->attr['class'] = implode(' ', $classes);
        }
        else
            $this->attr['class'] = $class;
    }
    
    /**
     * Remove class from the "class" attribute
     * @param  string $class Class to remove
     */
    public function removeClass($class)
    {
        if (isset($this->attr['class']))
        {
            $classes = explode(' ', $this->attr['class']);
            if (in_array($class, $classes))
                $classes = array_diff($classes, [$class]);
            
            if (empty($classes))
                unset($this->attr['class']);
            else
                $this->attr['class'] = implode(' ', $classes);
        }
    }
    
    /**
     * Replace class A with class B
     * @param  string $classA Class to be replaced
     * @param  string $classB Class to replace with
     */
    public function replaceClass($classA, $classB)
    {
        $this->removeClass($classA);
        $this->addClass($classB);
    }
}
