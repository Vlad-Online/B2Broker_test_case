<?php

namespace B2Broker\Jobs;

use B2Broker\Models\ThrottleQueue;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Bus\PendingDispatch;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class Throttle implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $model;

    /**
     * Create a new job instance.
     *
     * @param $someModel
     */
    public function __construct($someModel)
    {
        $this->model = $someModel;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        sleep(5);
    }


    public static function dispatch()
    {
        $model = func_get_arg(0);
        DB::transaction(function () use ($model) {
            $modelClass = get_class($model);
            if (ThrottleQueue::where('model_id', $model->id)->where('model_class', $modelClass)->count()) {
                throw new \Exception('Job already in queue');
            } else {
                ThrottleQueue::create([
                    'model_id'    => $model->id,
                    'model_class' => $modelClass
                ]);

                return new PendingDispatch(new static($model));
            }
        });
    }
}
