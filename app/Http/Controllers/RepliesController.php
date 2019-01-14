<?php

namespace App\Http\Controllers;

use App\Reply;
use Illuminate\Http\Request;
use App\Thread;
use App\Inspections\Spam;

class RepliesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth',['except' => 'index']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($channelId,Thread $thread)
    {
        return $thread->replies()->paginate(10);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store($channelId, Thread $thread,Spam $spam)
    {
        $this->validateReply();

        $reply = $thread->addReply([
            'body' => request('body'),
            'user_id' => auth()->id(),
        ]);

        if(request()->wantsJson()) {
            return $reply->load('owner');
        }

        return back()->with('flash', 'Your reply has been left.');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Reply  $reply
     * @return \Illuminate\Http\Response
     */
    public function update(Reply $reply,Spam $spam)
    {
        $this->authorize('update',$reply);

        $this->validateReply();

        $reply->update(request(['body']));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Reply  $reply
     * @return \Illuminate\Http\Response
     */
    public function destroy(Reply $reply)
    {
        $this->authorize('update',$reply);

        $reply->delete();

        if (request()->expectsJson()){
            return response(['status' => 'Reply deleted']);
        }

        return back();
    }

    protected function validateReply()
    {
        $this->validate(request(),['body' => 'required']);

        resolve(Spam::class)->detect(request('body'));
    }
}
