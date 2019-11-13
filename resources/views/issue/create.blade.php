@extends('layouts.app')

@section('content')
    <h1 class="text-4xl border-b mb-4">Add Issue</h1>

    <form method="POST" action="{{ route('add.issue') }}">
        @csrf
        <div class="flex items-center mb-6">
            <div class="w-1/3">
                <label for="title" class="text-gray-500 font-bold p-4">Title</label>
            </div>
            <div class="flex flex-col w-2/3">
                <input id="title" name="title" type="text" value="{{ old('title') }}" placeholder="Title" class="w-full p-2 rounded border shadow @error('title') border-red-500 @enderror" required>
                @error('title')
                    @component('components.error') {{ $message }} @endcomponent
                @enderror
            </div>
        </div>
        <div class="flex items-center mb-6">
            <div class="w-1/3">
                <label for="release_date" class="text-gray-500 font-bold p-4">Release Date</label>
            </div>
            <div class="flex flex-col w-2/3">
                <input id="release_date" name="release_date" type="date" value="{{ old('release_date', $set_release_date) }}" placeholder="Select Release Date" class="w-full p-2 rounded border shadow @error('release_date') border-red-500 @enderror" required>
                @error('release_date')
                    @component('components.error') {{ $message }} @endcomponent
                @enderror
            </div>
        </div>
        <div class="flex items-center mb-6">
            <div class="w-1/3">
                <label for="description" class="text-gray-500 font-bold p-4">Description</label>
            </div>
            <div class="flex flex-col w-2/3">
                <textarea id="description" name="description" placeholder="Description..." class="w-full p-2 rounded border shadow @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                @error('description')
                    @component('components.error') {{ $message }} @endcomponent
                @enderror
            </div>
        </div>
        <div class="flex items-center justify-center mb-6">
            <button class="shadow bg-blue-500 hover:bg-blue-400 focus:shadow-outline focus:outline-none text-white font-bold py-2 px-4 rounded" type="submit">Add Issue</button>
        </div>
    </form>
@endsection