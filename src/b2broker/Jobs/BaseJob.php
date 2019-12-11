<?php

namespace B2Broker\Jobs;

use B2Broker\Interfaces\SingleModelInQueue;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class BaseJob implements ShouldQueue, SingleModelInQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $model;

    /**
     * Create a new job instance.
     *
     * @param $model
     */
    public function __construct($model)
    {
        $this->model = $model;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        sleep(1);
    }

    /**
     * Should return class name of job payload
     * @return string
     */
    public function getClassName()
    {
        return get_class($this->model);
    }

    /**
     * Should return model primary key of job payload
     * @return integer
     */
    public function getModelId()
    {
        return $this->model->id;
    }
}
