<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Trending;
use App\Thread;

class SearchController extends Controller
{
    public function show(Trending $trending)
    {
        $search = request('q');

        $threads = Thread::search($search)->paginate(20);

        if(request()->expectsJson()) {
            return $threads;
        }

        return view('threads.index',[
            'threads' => $threads,
            'trending' => $trending->get()
        ]);
    }
}
