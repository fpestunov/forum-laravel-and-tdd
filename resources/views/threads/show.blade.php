@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ $thread->title }}</div>

                <div class="card-body">
                    {{ $thread->body }}
                </div>
            </div>
        </div>
    </div>
<br>
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                @foreach ($thread->replies as $reply)
                <div class="card-body">
                    <a href="#">{{ $reply->owner->name }}</a> said {{ $reply->created_at->diffForHumans() }}...
                    <br>
                    {{ $reply->body }}
                </div>
                <hr>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
