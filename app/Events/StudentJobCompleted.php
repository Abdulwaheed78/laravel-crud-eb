<?php

namespace App\Events;

class StudentJobCompleted
{
    public string $action;
    public array $data;

    public function __construct(string $action, array $data)
    {
        $this->action = $action;
        $this->data = $data;
    }
}
