@extends('forum.layout')

@section('title', $thread->title)

@section('content')
<div class="mb-8">
    <div class="text-sm text-gray-400 mb-2">
        <a href="{{ route('forum.index') }}" class="hover:text-white">Forum</a>
        <span class="mx-2">/</span>
        <a href="{{ route('forum.category', $thread->category->slug) }}" class="hover:text-white">{{ $thread->category->name }}</a>
        <span class="mx-2">/</span>
        <span>{{ $thread->title }}</span>
    </div>
    <div class="flex flex-col sm:flex-row justify-between items-start gap-4">
        <div class="flex-1">
            <h1 class="text-2xl sm:text-3xl font-bold">{{ $thread->title }}</h1>
            <div class="flex flex-wrap items-center gap-2 sm:gap-4 mt-2 text-sm text-gray-400">
                <span>by <span class="text-gray-300">{{ $thread->user->name }}</span></span>
                <span>{{ $thread->created_at->format('M d, Y') }}</span>
                <span>{{ number_format($thread->views) }} views</span>
            </div>
        </div>
        @auth
            <form action="{{ route('forum.thread.like', $thread) }}" method="POST">
                @csrf
                <button type="submit" class="flex items-center space-x-2 px-4 py-2 border border-gray-700 hover:bg-gray-800 rounded-lg transition-colors">
                    <svg class="w-5 h-5 {{ $thread->likes->where('user_id', auth()->id())->count() > 0 ? 'text-red-500' : 'text-gray-400' }}" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M2 10.5a1.5 1.5 0 113 0v6a1.5 1.5 0 01-3 0v-6zM6 10.333v5.43a2 2 0 001.106 1.79l.05.025A4 4 0 008.943 18h5.416a2 2 0 001.962-1.608l1.2-6A2 2 0 0015.56 8H12V4a2 2 0 00-2-2 1 1 0 00-1 1v.667a4 4 0 01-.8 2.4L6.8 7.933a4 4 0 00-.8 2.4z"/>
                    </svg>
                    <span class="text-gray-300">{{ $thread->likes->count() }}</span>
                </button>
            </form>
        @endauth
    </div>
</div>

<div class="space-y-4">
    @foreach($posts as $post)
        <div class="bg-gray-900 border border-gray-800 rounded-lg overflow-hidden">
            <div class="flex flex-col sm:flex-row">
                <div class="w-full sm:w-48 bg-gray-800 p-4 border-b sm:border-b-0 sm:border-r border-gray-700">
                    <div class="flex sm:flex-col items-center sm:text-center gap-4 sm:gap-0">
                        <div class="w-12 h-12 sm:w-16 sm:h-16 bg-gray-700 rounded-full sm:mx-auto sm:mb-2 flex items-center justify-center flex-shrink-0">
                            <span class="text-xl sm:text-2xl text-gray-300">{{ substr($post->user->name, 0, 1) }}</span>
                        </div>
                        <div class="flex-1 sm:flex-none">
                            <div class="font-medium text-white">{{ $post->user->name }}</div>
                            <div class="text-xs text-gray-400 mt-1">
                                Joined {{ $post->user->created_at->format('M Y') }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex-1 p-4 sm:p-6">
                    <div class="flex justify-between items-start mb-4">
                        <div class="text-sm text-gray-400">
                            Posted {{ $post->created_at->diffForHumans() }}
                        </div>
                        <div class="flex items-center space-x-2">
                            @auth
                                @if($post->user_id === auth()->id() || auth()->user()->is_admin)
                                    <form action="{{ route('forum.post.destroy', $post) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this post?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:text-red-400 text-sm">Delete</button>
                                    </form>
                                @endif
                            @endauth
                        </div>
                    </div>

                    <div class="text-gray-300 prose prose-invert max-w-none break-words">
                        {!! nl2br(e($post->content)) !!}
                    </div>

                    <div class="flex items-center justify-between mt-4 pt-4 border-t border-gray-800">
                        @auth
                            <form action="{{ route('forum.post.like', $post) }}" method="POST">
                                @csrf
                                <button type="submit" class="flex items-center space-x-2 text-sm hover:text-red-400 transition-colors">
                                    <svg class="w-4 h-4 {{ $post->likes->where('user_id', auth()->id())->count() > 0 ? 'text-red-500' : 'text-gray-400' }}" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M2 10.5a1.5 1.5 0 113 0v6a1.5 1.5 0 01-3 0v-6zM6 10.333v5.43a2 2 0 001.106 1.79l.05.025A4 4 0 008.943 18h5.416a2 2 0 001.962-1.608l1.2-6A2 2 0 0015.56 8H12V4a2 2 0 00-2-2 1 1 0 00-1 1v.667a4 4 0 01-.8 2.4L6.8 7.933a4 4 0 00-.8 2.4z"/>
                                    </svg>
                                    <span class="text-gray-300">{{ $post->likes->count() }}</span>
                                </button>
                            </form>
                        @else
                            <div class="flex items-center space-x-2 text-sm text-gray-400">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M2 10.5a1.5 1.5 0 113 0v6a1.5 1.5 0 01-3 0v-6zM6 10.333v5.43a2 2 0 001.106 1.79l.05.025A4 4 0 008.943 18h5.416a2 2 0 001.962-1.608l1.2-6A2 2 0 0015.56 8H12V4a2 2 0 00-2-2 1 1 0 00-1 1v.667a4 4 0 01-.8 2.4L6.8 7.933a4 4 0 00-.8 2.4z"/>
                                </svg>
                                <span>{{ $post->likes->count() }}</span>
                            </div>
                        @endauth
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>

@if($posts->hasPages())
    <div class="mt-6">
        {{ $posts->links() }}
    </div>
@endif
@endsection
