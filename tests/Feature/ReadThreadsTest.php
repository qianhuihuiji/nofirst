<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\Thread;

class ReadThreadsTest extends TestCase
{
    use DatabaseMigrations;

    public function setUp()
    {
        parent::setUp();

        $this->thread = create('App\Thread');
    }

    /** @test */
    public function a_user_can_view_all_threads()
    {
        $this->get('/threads')
            ->assertSee($this->thread->title);
    }

    /** @test */
    public function a_user_can_read_a_single_thread()
    {
        $this->get($this->thread->path())
            ->assertSee($this->thread->title);
    }

    /** @test */
    public function a_user_can_filter_threads_by_any_username()
    {
        $this->signIn(create('App\User', ['name' => 'NoNo1']));

        $threadByNoNo1 = create('App\Thread', ['user_id' => auth()->id()]);
        $threadNotByNoNo1 = create('App\Thread');

        $this->get('/threads?by=NoNo1')
            ->assertSee($threadByNoNo1->title)
            ->assertDontSee($threadNotByNoNo1->title);
    }

    /** @test */
    public function a_user_can_filter_threads_by_popularity()
    {
        $threadWithTwoReplies = create('App\Thread');
        create('App\Reply', ['thread_id' => $threadWithTwoReplies->id], 2);

        $threadWithThreeReplies = create('App\Thread');
        create('App\Reply', ['thread_id' => $threadWithThreeReplies->id], 3);

        $threadWithNoReplies = $this->thread;

        $response = $this->getJson('threads?popularity=1')->json();

        $this->assertEquals([3, 2, 0], array_column($response, 'replies_count'));
    }

    /** @test */
    public function a_user_can_filter_threads_by_those_that_are_unanswered()
    {
        $thread = create('App\Thread');
        create('App\Reply',['thread_id' => $thread->id]);

        $response = $this->getJson('threads?unanswered=1')->json();

        $this->assertCount(1,$response);
    }

    /** @test */
    public function a_user_can_request_all_replies_for_a_given_thread()
    {
        $thread = create('App\Thread');
        create('App\Reply',['thread_id' => $thread->id],20);

        $response = $this->getJson($thread->path() . '/replies')->json();

        $this->assertCount(10,$response['data']);
        $this->assertEquals(20,$response['total']);
    }
}
