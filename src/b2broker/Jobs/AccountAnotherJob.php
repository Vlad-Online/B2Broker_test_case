<?php

namespace B2Broker\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;

class AccountAnotherJob extends BaseJob implements ShouldQueue
{
    use SerializesModels;
}
