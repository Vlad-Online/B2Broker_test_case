<?php
/**
 * @package     B2Broker\Models
 * @subpackage
 *
 * @copyright   A copyright
 * @license     A "Slug" license name e.g. GPL2
 */

namespace B2Broker\Models;


use Illuminate\Queue\Worker;
use Illuminate\Queue\WorkerOptions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FunnelWorker extends Worker
{
    public function process($connectionName, $job, WorkerOptions $options)
    {
        //$payload = $event->job->payload();
        $payload    = $job->payload();
        $myJob      = unserialize($payload['data']['command']);
        $modelClass = $myJob->getClassName();
        $modelId    = $myJob->getModelId();

        return DB::transaction(function () use ($modelClass, $modelId, $myJob, $connectionName, $job, $options) {
            if (FunnelQueue::where('model_id', $modelId)->where('model_class', $modelClass)->count()) {
                Log::notice('Model '.$modelClass.' already processing');
                $job->release();

                return;
            } else {
                FunnelQueue::create([
                    'model_id'    => $modelId,
                    'model_class' => $modelClass
                ]);

                return parent::process($connectionName, $job, $options);
            }
        });

    }
}
