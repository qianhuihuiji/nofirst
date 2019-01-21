<?php

namespace App\Http\Controllers;

use App\Thread;
use Illuminate\Http\Request;
use App\Channel;
use App\Filters\ThreadsFilters;
use App\Inspections\Spam;
use Illuminate\Support\Facades\Redis;
use App\Trending;
use Zttp\Zttp;

class ThreadsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['index', 'show']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Channel $channel, ThreadsFilters $filters,Trending $trending)
    {
        $threads = $this->getThreads($channel, $filters);

        if (request()->wantsJson()) {
            return $threads;
        }

        return view('threads.index', [
            'threads' => $threads,
            'trending' => $trending->get()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('threads.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => 'required|spamfree',
            'body' => 'required|spamfree',
            'channel_id' => 'required|exists:channels,id'
        ]);

        $response = Zttp::asFormParams()->post('https://www.google.com/recaptcha/api/siteverify',[
            'secret' => config('services.recaptcha.secret'),
            'response' => $request->input('g-recaptcha-response'),
            'remoteip' => $_SERVER['REMOTE_ADDR']
        ]);

        if (! $response->json()['success']) {
            throw new \Exception('Recaptcha failed');
        }

        $thread = Thread::create([
            'user_id' => auth()->id(),
            'channel_id' => request('channel_id'),
            'title' => request('title'),
            'body' => request('body'),
            'slug' => request('title')
        ]);

        if (request()->wantsJson()) {
            return response($thread,201);
        }

        return redirect($thread->path())
            ->with('flash','Your thread has been published!');
    }

    /**
     * Display the specified thread.
     *
     * @param  \App\Thread  $thread
     * @return \Illuminate\Http\Response
     */
    public function show($channelId, Thread $thread,Trending $trending)
    {
        if(auth()->check()) {
            auth()->user()->read($thread);
        }

        $trending->push($thread);

        $thread->increment('visits');

        return view('threads.show', compact('thread'));
    }

    public function update($channel,Thread $thread)
    {
        if(request()->has('locked')) {
            if(! auth()->user()->isAdmin()) {
                return response('',403);
            }

            $thread->lock();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Thread  $thread
     * @return \Illuminate\Http\Response
     */
    public function destroy($channel,Thread $thread)
    {
        $this->authorize('update',$thread); 

        $thread->delete();

        if(request()->wantsJson()) {
            return response([],204);
        }

        return redirect('/threads');
    }

    protected function getThreads(Channel $channel, ThreadsFilters $filters)
    {
        $threads = Thread::latest()->filter($filters);

        if ($channel->exists) {
            $threads->where('channel_id', $channel->id);
        }

        $threads = $threads->paginate(20);
        return $threads;
    }
}
