##1. 1-Initial-Database-Setup-With-Seeding

Let's begin by reviewing the most minimal requirements for a forum. If you think about it, we couldn't possibly construct a forum without users, threads, and replies. So let's tackle those first.

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

Now that we have our seed data in place, we can move on to our first small feature: "a user should be able to read threads." Simple enough! We'll start with a basic test, and then scaffold the necessary views to make it pass.

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
## 3. A Thread Can Have Replies

Now that we've added a basic feature for users to read forum threads, we can next move on to viewing all replies associated with each thread. As before, we'll start with a test to describe our desired outcome.

Сделали в тесте функцию `setUp` и вынесли туда повторяющийся код.

Дописываем view вывода Replies. Thread добавляем связь один-ко-многим с Reply.
Делаем красивый вывод даты написания Reply.

Создадим новый тест:
php artisan make:test ReplyTest --unit

...и дорабатываем внешний вид страницы Поста с Ответами.

## 4-A-User-May-Response-To-Threads
Сначала рефакторинг:
- выносим кусок Комментариев в reply.blade.php, код становится чище;

Добавляем тест:
- Пост имеет пользователя создателя;
- App\Thread добавляем метод creator()

Запуск теста одного метода (все очень долго):
./vendor/bin/phpunit --filter test_a_thread_has_a_creator

После того, как написали тест, добавляем в шаблон вывод создателя статьи:
<a href="#">{{ $thread->creator->name }}</a> posted:

OK!!!

Переходим к новому тесту:
php artisan make:test ParticipateInForum

Handler.php добавляем для избегания ошибок:
if (app()->environment() === 'testing') throw $exception;

Добавляем web.php -> post route
Добавляем RepliesController

Добавляем в модели Reply, Thread. Чтобы без ошибки добавлялись поля в методе addReply()
protected $guarded = [];

Тесты работают!

## 5 The Reply Form
Now that we've tested the end-point for adding a new reply, the only remaining step is to create the HTML for the form. In the process, we'll also ensure that only logged-in users are able to see it.

Если пользователь авторизован, выводим форму добавления Reply.
@if (auth()->check())
И регистрируемся.

Теперь добавим в навигации ссылку на посты и лого.

## A User May Publish Threads

So far, a user can read and reply to threads, but they don't yet have the ability to publish their own threads. Let's begin fixing that in this episode.

Пользователи могут не только читать, но и создавать Посты.
php artisan make:test CreateThreadsTest
- используем actingAs() vs be()
- в фабриках пишем make() или raw(), в первом случае конвертируем в массив toArray()

Как работает фабрика?
php artisan tinker
factory('App\User')->make();
factory('App\User')->raw();

При написании метода store() в контроллере Threads, запускаем локальное тестирование... А что приходит?
public function store(Request $request)
{
    dd($request->all());
}

Работает! Теперь сделаем тест - неавторизованные не могут добавлять Посты.
- пишем тест
- пишем код:
    В контроллер добавляем ограничение.
    $this->middleware('auth')->only('store');

## 7. Let's Make Some Testing Helpers

I'm a big fan of making the process of writing tests as natural as humanly possible. The harder it is to construct a test, the more likely it is that you simply...won't. With that in mind, let's extract a few helpers and snippets to assist us.

Начинаем создание хелпера:
- делаем в композере запись, чтобы файл в Деве загружался автоматически: 
"autoload-dev": { "files": ["tests/utilities/functions.php"] }
- пишем функции-хелперы в файле tests/utilities/functions.php
- исправляем файл с тестами
- делаем composer dump-autoload
- проверяем тесты, ОК!

Теперь надо упростить создание нового пользователя:
- было - $this->actingAs(create('App\User'));
- надо - $this->signIn(), для этого пишем метод в TestCase.php (потому что CreateThreadsTest extends TestCase) и остальные тестовые классы будут "расширяться" от него;

## 8. The Exception Handling Conundrum

Now that our endpoint tests are returning green, we can construct the HTML form to publish a new thread. However, in the process, we'll stumble upon an odd exception handling issue that needs to be addressed. Luckily, Adam Wathan has a useful solution that we can implement.

a) web.php, много роутов у постов, а это значит надо сделать resource controller
Проверяем тесты... работают!

б) добавим страницу добавления постов и в контроллере поставим ограничение доступа к странице:
$this->middleware('auth')->only(['create', 'store']);
лучше так, потому что там много других экшнэв, кт не должны быть доступны другим
$this->middleware('auth')->except(['index', 'show']);

в) напишем тест, Гость не может видеть страницу добавления поста;
exception error, так... лечим рецептом Адама Воттана
https://gist.github.com/adamwathan/125847c7e3f16b88fa33a9f8b42333da
все тесты работают!!!

