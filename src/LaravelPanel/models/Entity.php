<?php

namespace Jaimeeee\Panel;

use File;
use Lang;
use Symfony\Component\Yaml\Yaml;

class Entity
{
    private $data;
    private $name;
    private $paginate;
    private $sort;
    public $class;
    public $deletable = true;
    public $editable = true;
    public $fields;
    public $hidden;
    public $hideCreate = false;
    public $icon;
    public $list;
    public $title;
    public $url;
    public $images;

    /**
     * Create a new instance from the Yaml data.
     *
     * @param string $filePath The file path
     */
    public function __construct($filePath)
    {
        $this->data = Yaml::parse(file_get_contents($filePath));

        $this->class = $this->data['class'];

        // Get model's name
        $classParts = explode('\\', $this->class);
        $this->name = strtolower(is_array($classParts) ? end($classParts) : $classParts);

        $this->fields = $this->data['fields'];
        $this->hidden = isset($this->data['hidden']) && $this->data['hidden'] ? true : false;
        $this->hideCreate = isset($this->data['create']) && !$this->data['create'] ? true : false;
        $this->icon = isset($this->data['icon']) ? $this->data['icon'] : '';
        $this->list = $this->data['list'];
        $this->paginate = isset($this->data['paginate']) ? $this->data['paginate'] : config('panel.paginate');
        $this->sort = isset($this->data['sort']) ? $this->data['sort'] : null;
        $this->title = isset($this->data['title']) ? $this->data['title'] : $this->name(true);
        $this->url = strtolower(str_plural($this->name));
        $this->images = isset($this->data['images']) ? $this->data['images'] : '';

        // Properties
        $this->deletable = isset($this->data['deletable']) && !$this->data['deletable'] ? false : true;
        $this->editable = isset($this->data['editable']) && !$this->data['editable'] ? false : true;
    }

    /**
     * Get all items for an entity paginated or not.
     *
     * @return Collection
     */
    public function all()
    {
        $thisClass = $this->class;

        $query = '';
        if (isset($this->sort['field'])) {
            $query = $thisClass::orderBy($this->sort['field'],
                        isset($this->sort['order']) && $this->sort['order'] == 'desc' ? 'desc' : 'asc');
        }

        if ($this->paginate) {
            $rows = $query ? $query->paginate($this->paginate) : $thisClass::paginate($this->paginate);
        } else {
            $rows = $query ? $query->get() : $thisClass::get();
        }

        return $rows;
    }

    /**
     * Return the list of blueprints as entities.
     *
     * @return array Array of Entities
     */
    public static function entityList()
    {
        $files = File::files(config_path('panel'));

        $list = [];
        foreach ($files as $file) {
            $list[] = new self($file);
        }

        return collect($list)->sortBy(function ($entity) {
            return $entity->name();
        });
    }

    /**
     * Get the name attributed translate and properly pluralized if needed.
     *
     * @param bool $plural
     *
     * @return string
     */
    public function name($plural = false)
    {
        if (Lang::has('panel::entities.'.$this->name)) {
            return trans_choice('panel::entities.'.$this->name, $plural ? 2 : 1);
        } else {
            return $plural ? str_plural(ucwords($this->name)) : ucwords($this->name);
        }
    }

    /**
     * Get entity's panel URL.
     *
     * @param string $path Prepend path
     *
     * @return string URL
     */
    public function url($path = '')
    {
        // Return full URL with or without path
        return url(config('panel.url').'/'.$this->url.($path ? '/'.ltrim($path, '/') : ''));
    }
}
