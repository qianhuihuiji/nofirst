@forelse($threads as $thread)
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="level">
                <h4 class="flex">
                    <a href="{{ $thread->path() }}">
                        @if(auth()->check() && $thread->hasUpdatedFor(auth()->user()))
                            <strong>
                                {{ $thread->title }}
                            </strong>
                        @else
                            {{ $thread->title }}
                        @endif
                    </a>
                </h4>

                <a href="{{ $thread->path() }}">
                    {{ $thread->replies_count }} {{ str_plural('reply',$thread->replies_count) }}
                </a>
            </div>
        </div>

        <div class="panel-body">{!! $thread->body !!} </div>

        <div class="panel-footer">
            {{ $thread->visits }} Visits
        </div>
    </div>
@empty
    <p>There are no relevant results at this time.</p>
@endforelse