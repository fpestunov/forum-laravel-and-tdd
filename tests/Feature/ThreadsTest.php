<?php

namespace Tests\Feature;

use Tests\TestCase;
// use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ThreadsTest extends TestCase
{
    use DatabaseMigrations;

    public function setUp()
    {
        parent::setUp();

        $this->thread = factory('App\Thread')->create();
    }
    public function test_a_user_can_browse_threads()
    {
        $response = $this->get('/threads');
        $response->assertStatus(200);
    }

    public function test_a_user_can_view_all_threads()
    {
        $response = $this->get('/threads');
        $response->assertSee($this->thread->title);
    }    

    public function test_a_user_can_read_a_single_thread()
    {
        $response = $this->get('/threads/' .$this->thread->id);
        $response->assertSee($this->thread->title);
    }    

    public function test_a_user_can_read_replies_that_are_associated_with_thread()
    {
        // Given we have a thread = setUp()

        // And that thread includes replies 
        $reply = factory('App\Reply')
            ->create(['thread_id' => $this->thread->id]);

        // When we visit a thread page
        // $response = $this->get('/threads/' .$this->thread->id);

        // Then we should see the replies 
        $this->get('/threads/' .$this->thread->id)
            ->assertSee($reply->body);


    }    
}
