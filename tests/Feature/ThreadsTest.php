<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ThreadsTest extends TestCase
{
    use DatabaseMigrations;

    protected $thread;

    public function setUp()
    {
        parent::setUp();

        $this->thread = factory('App\Thread')->create();
    }

    public function test_a_thread_can_make_a_string_path()
    {
        $thread = create('App\Thread');
        // $this->assertEquals('/threads/' . $thread->channel->slug . '/' . $thread->id, $thread->path());
        $this->assertEquals(
            "/threads/{$thread->channel->slug}/{$thread->id}", $thread->path()
        ); // так чище
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
        $response = $this->get("/threads/{$this->thread->channel->slug}/" .$this->thread->id);
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
        $this->get("/threads/{$this->thread->channel->slug}/" . $this->thread->id)
            ->assertSee($reply->body);


    }    

    public function test_a_thread_has_a_creator()
    {
        $this->assertInstanceOf('App\User', $this->thread->creator);
    }

    public function test_a_thread_can_add_a_reply()
    {
        $this->thread->addReply([
            'body' => 'Foobar',
            'user_id' => 1
        ]);

        $this->assertCount(1, $this->thread->replies);
    }

    public function test_a_thread_belongs_to_a_channel()
    {
        $thread = create('App\Thread');

        $this->assertInstanceOf('App\Channel', $thread->channel);
    }

    public function test_a_user_can_filter_threads_according_to_a_channel()
    {
        $this->withExceptionHandling();

        $channel = create('App\Channel');
        $threadInChannel = create('App\Thread', ['channel_id' => $channel->id]);
        $threadNotInChannel = create('App\Thread');

        $this->get('/threads/' . $channel->slug)
            ->assertSee($threadInChannel->title)
            ->assertDontSee($threadNotInChannel->title);

    }

    public function test_a_user_can_filter_threads_by_any_username()
    {
        $this->signIn(create('App\User', ['name' => 'JohnDoe']));
         $threadByJohn = create('App\Thread', ['user_id' => auth()->id()]);
        $threadNotByJohn = create('App\Thread');

        $this->get('threads?by=JohnDoe')
            ->assertSee($threadByJohn->title)
            ->assertDontSee($threadNotByJohn->title);
    }
}
