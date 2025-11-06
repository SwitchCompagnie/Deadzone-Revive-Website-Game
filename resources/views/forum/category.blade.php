@extends('forum.layout')

@section('title', $category->name)

@section('content')
<div class="mb-8 flex justify-between items-center">
    <div>
        <div class="text-sm text-gray-400 mb-2">
            <a href="{{ route('forum.index') }}" class="hover:text-white">Forum</a>
            <span class="mx-2">/</span>
            <span>{{ $category->name }}</span>
        </div>
        <h1 class="text-3xl font-bold">{{ $category->name }}</h1>
        @if($category->description)
            <p class="text-gray-400 mt-2">{{ $category->description }}</p>
        @endif
    </div>
    @auth
        <a href="{{ route('forum.thread.create', $category->slug) }}" 
           class="px-4 py-2 bg-red-600 hover:bg-red-700 rounded-lg text-white font-medium transition-colors">
            New Thread
        </a>
    @endauth
</div>

<div class="bg-gray-900 border border-gray-800 rounded-lg overflow-hidden">
    <div class="bg-gray-800 px-6 py-3 border-b border-gray-700">
        <div class="flex items-center text-sm font-semibold text-gray-400">
            <div class="flex-1">Thread</div>
            <div class="w-24 text-center">Replies</div>
            <div class="w-24 text-center">Likes</div>
            <div class="w-24 text-center">Views</div>
        </div>
    </div>

    <div class="divide-y divide-gray-800">
        @forelse($threads as $thread)
            <div class="px-6 py-4 hover:bg-gray-800 transition-colors">
                <div class="flex items-center">
                    <div class="flex-1">
                        <div class="flex items-center space-x-2">
                            @if($thread->is_pinned)
                                <svg class="w-4 h-4 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10 2a1 1 0 011 1v1.323l3.954 1.582 1.599-.8a1 1 0 01.894 1.79l-1.233.616 1.738 5.42a1 1 0 01-.285 1.05A3.989 3.989 0 0115 15a3.989 3.989 0 01-2.667-1.019 1 1 0 01-.285-1.05l1.738-5.42-1.233-.617a1 1 0 01.894-1.788l1.599.799L11 4.323V3a1 1 0 011-1zm-5 8.274l-.818 2.552c-.25.78.128 1.626.847 1.916.719.29 1.555-.028 1.805-.808L7.77 11.03 5 10.274zm14.158 3.096a1 1 0 01.684 1.07l-1 6A1 1 0 0117.848 21H2.152a1 1 0 01-.994-.86l-1-6a1 1 0 011.686-.98l1.523 1.524 4.674-8.011a1 1 0 011.697 0l4.674 8.011 1.523-1.524a1 1 0 011.002-.094z"/>
                                </svg>
                            @endif
                            @if($thread->is_locked)
                                <svg class="w-4 h-4 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                                </svg>
                            @endif
                            <a href="{{ route('forum.thread.show', $thread->slug) }}" 
                               class="text-lg font-medium text-red-500 hover:text-red-400">
                                {{ $thread->title }}
                            </a>
                        </div>
                        <div class="text-sm text-gray-400 mt-1">
                            by <span class="text-gray-300">{{ $thread->user->name }}</span>
                            • {{ $thread->created_at->diffForHumans() }}
                        </div>
                    </div>
                    <div class="w-24 text-center text-gray-300">
                        {{ $thread->posts_count ?? 0 }}
                    </div>
                    <div class="w-24 text-center text-gray-300">
                        {{ $thread->likes_count ?? 0 }}
                    </div>
                    <div class="w-24 text-center text-gray-300">
                        {{ number_format($thread->views) }}
                    </div>
                </div>
            </div>
        @empty
            <div class="px-6 py-8 text-center text-gray-400">
                No threads in this category yet.
                @auth
                    <a href="{{ route('forum.thread.create', $category->slug) }}" class="text-red-500 hover:text-red-400">
                        Be the first to create one!
                    </a>
                @endauth
            </div>
        @endforelse
    </div>
</div>

@if($threads->hasPages())
    <div class="mt-6">
        {{ $threads->links() }}
    </div>
@endif
@endsection
