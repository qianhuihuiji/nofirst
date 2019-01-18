<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Activity;

class CreateThreadsTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function a_thread_requires_a_title()
    {
        $this->publishThread(['title' => null])
            ->assertSessionHasErrors('title');
    }

    /** @test */
    public function a_thread_requires_a_body()
    {
        $this->publishThread(['body' => null])
            ->assertSessionHasErrors('body');
    }

    /** @test */
    public function a_thread_requires_a_valid_channel()
    {
        factory('App\Channel',2)->create();

        $this->publishThread(['channel_id' => null])
            ->assertSessionHasErrors('channel_id');

        $this->publishThread(['channel_id' => 999])
        ->assertSessionHasErrors('channel_id');
    }

    /** @test */
    public function guests_may_not_create_threads()
    {
        $this->withExceptionHandling();

        $this->get('/threads/create')
            ->assertRedirect('/login');

        $this->post('/threads',[])
            ->assertRedirect('/login');
    }

    /** @test */
    public function authenticated_users_must_first_confirm_their_email_address_before_creating_threads()
    {
        $this->publishThread()
            ->assertRedirect('/threads')
            ->assertSessionHas('flash','You must first confirm your email address.');
    }

    /** @test */
    public function an_authenticated_user_can_create_new_forum_threads()
    {
        $this->signIn();

        $thread = make('App\Thread');
        $response = $this->post('/threads',$thread->toArray());

        $this->get($response->headers->get('Location'))
            ->assertSee($thread->title)
            ->assertSee($thread->body);  
    }

    /** @test */
    public function a_user_can_filter_threads_accroding_to_a_channel()
    {
        $channel = create('App\Channel');
        $threadInChannel = create('App\Thread',['channel_id' => $channel->id]);
        $threadNotInChannel = create('App\Thread');

        $this->get('/threads/' . $channel->slug)
            ->assertSee($threadInChannel->title)
            ->assertDontSee($threadNotInChannel->title);
    }

    /** @test */
    public function guests_can_not_delete_threads()
    {
        $this->withExceptionHandling();

        $thread = create('App\Thread');

        $response = $this->delete($thread->path());

        $response->assertRedirect('/login');
    }

    /** @test */
    public function unauthorized_users_may_not_delete_threads()
    {
        $this->withExceptionHandling();

        $thread = create('App\Thread');

        $this->delete($thread->path())->assertRedirect('/login');

        $this->signIn();
        $this->delete($thread->path())->assertStatus(403);
    }

    /** @test */
    public function authorized_users_can_delete_threads()
    {
        $this->signIn();

        $thread = create('App\Thread',['user_id' => auth()->id()]);
        $reply = create('App\Reply',['thread_id' => $thread->id]);

        $response = $this->json('DELETE',$thread->path());

        $response->assertStatus(204);

        $this->assertDatabaseMissing('threads',['id' => $thread->id]);
        $this->assertDatabaseMissing('replies',['id' => $reply->id]);

        $this->assertEquals(0,Activity::count());
    }

    protected function publishThread($overrides = [])
    {
        $this->withExceptionHandling()->signIn();

        $thread = make('App\Thread',$overrides );

        return $this->post('/threads',$thread->toArray());
    }
}
