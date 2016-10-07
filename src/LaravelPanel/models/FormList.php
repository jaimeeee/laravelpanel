<?php

namespace Jaimeeee\Panel;

class FormList
{
    private $entity;

    public function __construct($entity)
    {
        $this->entity = $entity;
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
