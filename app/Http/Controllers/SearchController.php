<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Trending;
use App\Thread;

class SearchController extends Controller
{
    public function show(Trending $trending)
    {
        if (request()->expectsJson()) {
            return Thread::search(request('q'))->paginate(20);
        }

        return view('threads.search',[
            'trending' => $trending->get()
        ]);
    }
}
