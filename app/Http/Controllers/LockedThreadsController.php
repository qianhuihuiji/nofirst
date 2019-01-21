<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Thread;

class LockedThreadsController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    public function store(Thread $thread)
    {
        $thread->update(['locked' => true]);
    }

    public function destroy(Thread $thread)
    {
        $thread->update(['locked' => false]);
    }
}
