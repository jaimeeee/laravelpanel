<?php

namespace Jaimeeee\Panel;

use Jaimeeee\Panel\Entity;

class FormList
{
    private $entity;
    private $parentRecord;
    private $childEntity;
    private $childre;

    public function __construct($entity, $parent = false, $childEntity = false)
    {
        $this->entity = $entity;
        $this->parentRecord = $parent;
        $this->childEntity = $childEntity;
    }

    public function childView()
    {
        $records = $this->parentRecord->{$this->childEntity->child};

        $actions = [];
        if ($this->childEntity->deletable) {
            $actions[] = 'delete';
        }
        if ($this->childEntity->editable) {
            $actions[] = 'edit';
        }

        return view('panel::list', [
                        'actions'          => $actions,
                        'parentEntity'     => $this->entity,
                        'parentRecord'     => $this->parentRecord,
                        'entity'           => $this->childEntity,
                        'hideCreateRecord' => $this->childEntity->hideCreate,
                        'records'          => $records,
                        'rows'             => $this->childEntity->list,
                        'title'            => $this->childEntity->title,
                    ]);
    }

    public function view()
    {
        $records = $this->entity->all();

        $actions = [];
        if ($this->entity->deletable) {
            $actions[] = 'delete';
        }
        if ($this->entity->editable) {
            $actions[] = 'edit';
        }

        return view('panel::list', [
                        'actions'          => $actions,
                        'entity'           => $this->entity,
                        'hideCreateRecord' => $this->entity->hideCreate,
                        'records'          => $records,
                        'rows'             => $this->entity->list,
                        'title'            => $this->entity->title,
                    ]);
    }
}
