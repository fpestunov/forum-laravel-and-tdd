<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class CreateThreadsTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function test_guests_may_not_create_threads()
    {
        $this->withExceptionHandling();

        $this->get('/threads/create')
            ->assertRedirect('/login');

        $this->post('/threads')
            ->assertRedirect('/login');
    }

    /** @test */
    public function test_guests_cannot_see_the_create_thread_page()
    {
        $this->withExceptionHandling()
            ->get('/threads/create')
            ->assertRedirect('/login');
    }
    
    /** @test */
    public function test_an_authenticated_user_can_create_new_forum_thread()
    {
        // Given we have a signed in user
        // $this->actingAs(create('App\User'));
        $this->signIn();

        // When we hit the endpoint to create a new thread
        $thread = create('App\Thread');

        $this->post('/threads', $thread->toArray());

        // Then, when we visit the thread page
        $this->get($thread->path())
        // We should see the new thread
            ->assertSee($thread->title)
            ->assertSee($thread->body);
    }
}
