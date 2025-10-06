<?php

namespace App\Events;

use Throwable;

class StudentJobFailed
{
    public string $action;
    public array $data;
    public Throwable $exception;

    public function __construct(string $action, array $data, Throwable $exception)
    {
        $this->action = $action;
        $this->data = $data;
        $this->exception = $exception;
    }
}
