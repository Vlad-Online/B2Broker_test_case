# Задание
Предположим, у нас есть базовая сущность Account (пользовательский
счет), и мы хотим производить с ним некоторые (потенциально длительные)
действия.
Используя стандартный механизм очередей Laravel, необходимо реализовать
следующий дополнительный функционал:
1. Throttle
Задача ставится в очередь, только если в очереди нет задач с таким же
ключом (ключом может быть, например, номер аккаунта)
Пример:
$a1 = Account::findOrFail(1);
$a2 = Account::findOrFail(2);
BalanceUpdate::dispatch($a1); // - поставилась, ждет отработки
BalanceUpdate::dispatch($a2); // - поставилась
BalanceUpdate::dispatch($a1); // - не поставилась
BalanceUpdate::dispatch($a1); // - не поставилась
2. Funnel
Очередь обслуживается несколькими воркерами.
Задачи ставятся в очередь всегда, но по одному ключу только одна задача
может находиться в работе.
Пример:
$a1 = Account::findOrFail(1);
$a2 = Account::findOrFail(2);
AccountSomeJob::dispatch($a1);
AccountAnotherJob::dispatch($a1);
AccountYetAnotherJob::dispatch($a1);
AccountYetAnotherJob::dispatch($a2);
AccountSomeJob::dispatch($a2);
AccountAnotherJob::dispatch($a2);Работают, например, десять воркеров, но в работе должны быть максимум
две джобы: одна по $a1 и одна по $a2.
Особенности реализации:
-----------------------
1. Хранилище очередей - БД
2. Функционал Throttle и Funnel не связан между собой, и может
использоваться как раздельно, так и вместе
3. Установка в систему - через композер-пакеты
Критерии приемки:
-----------------
1. Работает
2. Красиво выглядит
3. Удобно пользоваться программисту
# Установка 
Добавьте в файл `composer.json` репозиторий пакета:

    {
        "repositories": [
            {
                "type": "vcs",
                "url": "https://github.com/Vlad-Online/B2Broker_test_case.git"
            }
        ],
        "require": {
            "vlad-online/b2broker": "dev-master"
        }
    }

Установите пакет

    composer update

Настройте соединение с базой в файле `.env` и запустите миграции базы данных

    php artisan migrate


# Использование
## Throttle
Статический метод `Throttle::dispatch()` выбрасывает исключение `\Exception` в случае если задача с такими же параметрами уже есть в очереди.

## Funnel
Ваша задача должна удовлетворять интерфейсу `SingleModelInQueue` и реализовывать два метода:

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
     


# Тестирование
Добавьте в базу тестовые данные

    php artisan db:seed --class=B2Broker\\Seeds\\UsersTableSeeder
    
Замените в файле `phpunit.xml` строчку

    <server name="QUEUE_CONNECTION" value="sync"/>
    
на следующую

    <server name="QUEUE_CONNECTION" value="database"/>
    
Запустите тесты
      
    ./vendor/bin/phpunit ./vendor/vlad-online/b2broker/tests/
