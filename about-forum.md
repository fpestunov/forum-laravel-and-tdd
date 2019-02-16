##1. 1-Initial-Database-Setup-With-Seeding

### Forum
1. Thread
2. Reply
3. User

A. Thread is created by a user
B. A reply belongs to a thread and belongs to a user

Создадим - модель, миграцию, resource controller
php artisan make:model Thread -mr
Правим миграцию - добавляем необходимые поля.
Правим .env файл - параметры БД.
Создаем БД.
Делаем миграцию.
php artisan migrate

Теперь создадим Reply - модель, миграцию и контроллер
php artisan make:model Reply -mc
Правим миграцию - добавляем необходимые поля.
Делаем миграцию.
php artisan migrate

Теперь заполним данными из Фэйкера
//database//factories/UserFactory.php - добавляем фэйкер для ответов и постов
php artisan tinker
factory('App\Thread', 50)->create() - отлично, БД заполнилась

Обновим таблицы
php artisan migrate:refresh
php artisan tinker
$threads = factory('App\Thread', 50)->create() - отлично, БД заполнилась новыми данными
$threads->each(function ($thread) { factory('App\Reply', 10)->create(['thread_id' => $thread->id]); }); - *что это за конструкция?*

Отлично! Конец первого урока.

##2-Test-Driving-Threads
Дополнительная литература:
- https://phpunit.readthedocs.io/ru/latest/index.html
- https://leanpub.com/u/lex111

Приступаем к написанию тестов:
- Пользователи могут просматривать Посты
- Пользователи должны видеть все Посты
- Пользователи могут читать Посты

Настраиваем конфигурации:
- .env - конфигурация соединения с БД
- phpunit.xml - конфигурация соединения с тестированием

Создадим авторизацию
php artisan make:auth

Для работы постов настраиваем `routes/web.php`, `app/Http/Controllers/ThreadsController.php` и соответствующие views.

Делаем вывод пути к постам через метод `path()` модели `Thread`.
``
<a href="{{ $thread->path() }}">
    {{ $thread->title}}
</a>
``
