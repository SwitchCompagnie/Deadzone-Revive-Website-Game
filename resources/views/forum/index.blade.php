@extends('forum.layout')

@section('title', 'Forum Home')

@section('content')
<div class="mb-8">
    <h1 class="text-2xl sm:text-3xl font-bold">Forum</h1>
    <p class="text-gray-400 mt-2">Welcome to the Deadzone Revive community forum</p>
</div>

@forelse($categories as $category)
    <div class="mb-6 bg-gray-900 border border-gray-800 rounded-lg overflow-hidden">
        <div class="bg-gray-800 px-4 sm:px-6 py-3 border-b border-gray-700">
            <h2 class="text-lg sm:text-xl font-semibold">{{ $category->name }}</h2>
            @if($category->description)
                <p class="text-gray-400 text-sm mt-1">{{ $category->description }}</p>
            @endif
        </div>
        
        <div class="divide-y divide-gray-800">
            @if($category->children->count() > 0)
                @foreach($category->children as $subcategory)
                    <div class="px-4 sm:px-6 py-4 hover:bg-gray-800 transition-colors">
                        <div class="flex flex-col sm:flex-row justify-between items-start gap-2 sm:gap-0">
                            <div class="flex-1">
                                <a href="{{ route('forum.category', $subcategory->slug) }}" class="text-base sm:text-lg font-medium text-red-500 hover:text-red-400">
                                    {{ $subcategory->name }}
                                </a>
                                @if($subcategory->description)
                                    <p class="text-gray-400 text-sm mt-1">{{ $subcategory->description }}</p>
                                @endif
                            </div>
                            <div class="text-left sm:text-right sm:ml-4">
                                <div class="text-sm text-gray-400">
                                    {{ $subcategory->threads_count ?? 0 }} threads
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="px-4 sm:px-6 py-4 hover:bg-gray-800 transition-colors">
                    <div class="flex flex-col sm:flex-row justify-between items-start gap-2 sm:gap-0">
                        <div class="flex-1">
                            <a href="{{ route('forum.category', $category->slug) }}" class="text-base sm:text-lg font-medium text-red-500 hover:text-red-400">
                                View all threads
                            </a>
                        </div>
                        <div class="text-left sm:text-right sm:ml-4">
                            <div class="text-sm text-gray-400">
                                {{ $category->threads_count ?? 0 }} threads
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@empty
    <div class="bg-gray-900 border border-gray-800 rounded-lg p-8 text-center">
        <p class="text-gray-400">No forum categories available yet.</p>
    </div>
@endforelse
@endsection
