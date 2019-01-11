<?php

namespace App\Filters;

use App\User;

class ThreadsFilters extends Filters
{
    protected $filters = ['by','popularity','unanswered'];

    protected function by($username)
    {
        $user = User::where('name',$username)->firstOrFail();

        return $this->builder->where('user_id',$user->id);
    }

    protected function popularity()
    {
        $this->builder->getQuery()->orders = [];

        return $this->builder->orderBy('replies_count','desc');
    }

    public function unanswered()
    {
        return $this->builder->where('replies_count',0);
    }
}