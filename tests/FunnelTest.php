<?php

namespace Tests\Unit;

use App\User;
use B2Broker\Jobs\AccountAnotherJob;
use B2Broker\Jobs\AccountSomeJob;
use B2Broker\Jobs\AccountYetAnotherJob;
use B2Broker\Models\FunnelQueue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class FunnelTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function testFunnel()
    {
        //Queue::fake();
        DB::table('jobs')->truncate();
        FunnelQueue::truncate();
        $a1 = User::findOrFail(1);
        $a2 = User::findOrFail(2);

        // Добавили первую задачу
        AccountSomeJob::dispatch($a1);

        // Проверили что задача добавилась
        $this->assertEquals(1, DB::table('jobs')->count());

        // Эмитируем, что какая то задача с такой моделью выполняется
        FunnelQueue::create([
            'model_id'    => $a1->id,
            'model_class' => get_class($a1)
        ]);

        // Запускаем выполнение одной задачи
        Artisan::call('queue:work', ['--once' => true, '--tries' => 10]);

        // Проверяем, что задача осталась в очереди
        $this->assertEquals(1, DB::table('jobs')->count());

        // Удаляем эмитацию задачи
        FunnelQueue::truncate();

        // Запускаем выполнение одной задачи
        Artisan::call('queue:work', ['--once' => true, '--tries' => 10]);

        // Проверяем, что очередь пуста
        $this->assertEquals(0, DB::table('jobs')->count());

//        AccountAnotherJob::dispatch($a1);
//        AccountYetAnotherJob::dispatch($a1);
//        AccountYetAnotherJob::dispatch($a2);
//        AccountSomeJob::dispatch($a2);
//        AccountAnotherJob::dispatch($a2);
    }
}
