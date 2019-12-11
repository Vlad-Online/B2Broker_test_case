<?php

namespace B2Broker\Laravel;

use B2Broker\Models\FunnelQueue;
use B2Broker\Models\FunnelWorker;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Queue\Events\JobProcessing;
use Illuminate\Queue\Worker;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__.'/../Migrations');
        $this->app->singleton('queue.worker', function () {
            $isDownForMaintenance = function () {
                return $this->app->isDownForMaintenance();
            };

            return new FunnelWorker(
                $this->app['queue'],
                $this->app['events'],
                $this->app[ExceptionHandler::class],
                $isDownForMaintenance
            );
        });

        Queue::after(function (JobProcessed $event) {
            $payload    = $event->job->payload();
            $job        = unserialize($payload['data']['command']);
            $modelClass = $job->getClassName();
            $modelId    = $job->getModelId();
            FunnelQueue::where([
                'model_id'    => $modelId,
                'model_class' => $modelClass
            ])->delete();
        });
    }
}
