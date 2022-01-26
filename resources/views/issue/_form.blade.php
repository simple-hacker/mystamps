<form method="POST" action="{{ $action }}">
    @csrf
    <div class="flex items-center mb-6">
        <div class="w-1/3">
            <label for="title" class="text-gray-500 font-bold p-4">Title</label>
        </div>
        <div class="flex flex-col w-2/3">
            <input id="title" name="title" type="text" value="{{ old('title', $issue->title) }}" placeholder="Title" class="w-full p-2 rounded border shadow @error('title') border-red-500 @enderror" required>
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
            <input id="release_date" name="release_date" type="date" value="{{ old('release_date', $issue->release_date) }}" placeholder="Select Release Date" class="w-full p-2 rounded border shadow @error('release_date') border-red-500 @enderror" required>
            @error('release_date')
                @component('components.error') {{ $message }} @endcomponent
            @enderror
        </div>
    </div>
    <div class="flex items-center mb-6">
        <div class="w-1/3">
            <label for="subject" class="text-gray-500 font-bold p-4">Subject</label>
        </div>
        <div class="flex flex-col w-2/3">
            <input id="subject" name="subject" type="text" value="{{ old('subject', $issue->subject) }}" placeholder="Subject" class="w-full p-2 rounded border shadow @error('subject') border-red-500 @enderror">
            @error('subject')
                @component('components.error') {{ $message }} @endcomponent
            @enderror
        </div>
    </div>
    <div class="flex items-center mb-6">
        <div class="w-1/3">
            <label for="monarch_id" class="text-gray-500 font-bold p-4">Monarch</label>
        </div>
        <div class="flex flex-col w-2/3">
            <select name="monarch_id" class="w-full p-2 bg-white rounded border shadow @error('monarch_id') border-red-500 @enderror">
                <option hidden selected value=""> Select a Monarch </option>
                @foreach ($monarchs as $monarch)
                    <option value="{{ $monarch->id }}" @if($monarch->id === $issue->monarch_id) selected @endif>{{ $monarch->abbreviation}} - {{ $monarch->monarch }}</option>
                @endforeach
            </select>
            @error('monarch_id')
                @component('components.error') {{ $message }} @endcomponent
            @enderror
        </div>
    </div>
    <div class="flex items-center mb-6">
        <div class="w-1/3">
            <label for="category_id" class="text-gray-500 font-bold p-4">Category</label>
        </div>
        <div class="flex flex-col w-2/3">
            <select name="category_id" class="w-full p-2 bg-white rounded border shadow @error('category_id') border-red-500 @enderror">
                <option hidden selected value=""> Select a category </option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" @if($category->id === $issue->category_id) selected @endif>{{ $category->category }}</option>
                @endforeach
            </select>
            @error('category_id')
                @component('components.error') {{ $message }} @endcomponent
            @enderror
        </div>
    </div>
    <div class="flex items-center mb-6">
        <div class="w-1/3">
            <label for="designer" class="text-gray-500 font-bold p-4">Designer</label>
        </div>
        <div class="flex flex-col w-2/3">
            <input id="designer" name="designer" type="text" value="{{ old('designer', $issue->designer) }}" placeholder="Designer" class="w-full p-2 rounded border shadow @error('designer') border-red-500 @enderror">
            @error('designer')
                @component('components.error') {{ $message }} @endcomponent
            @enderror
        </div>
    </div>
    <div class="flex items-center mb-6">
        <div class="w-1/3">
            <label for="printer" class="text-gray-500 font-bold p-4">Printer</label>
        </div>
        <div class="flex flex-col w-2/3">
            <input id="printer" name="printer" type="text" value="{{ old('printer', $issue->printer) }}" placeholder="Printer" class="w-full p-2 rounded border shadow @error('printer') border-red-500 @enderror">
            @error('printer')
                @component('components.error') {{ $message }} @endcomponent
            @enderror
        </div>
    </div>
    <div class="flex items-center mb-6">
        <div class="w-1/3">
            <label for="print_process" class="text-gray-500 font-bold p-4">Print Process</label>
        </div>
        <div class="flex flex-col w-2/3">
            <input id="print_process" name="print_process" type="text" value="{{ old('print_process', $issue->print_process) }}" placeholder="Print Process" class="w-full p-2 rounded border shadow @error('print_process') border-red-500 @enderror">
            @error('print_process')
                @component('components.error') {{ $message }} @endcomponent
            @enderror
        </div>
    </div>
    <div class="flex items-center mb-6">
        <div class="w-1/3">
            <label for="size" class="text-gray-500 font-bold p-4">Size</label>
        </div>
        <div class="flex flex-col w-2/3">
            <input id="size" name="size" type="text" value="{{ old('size', $issue->size) }}" placeholder="Size" class="w-full p-2 rounded border shadow @error('size') border-red-500 @enderror">
            @error('size')
                @component('components.error') {{ $message }} @endcomponent
            @enderror
        </div>
    </div>
    <div class="flex items-center mb-6">
        <div class="w-1/3">
            <label for="perforations" class="text-gray-500 font-bold p-4">Perforations</label>
        </div>
        <div class="flex flex-col w-2/3">
            <input id="perforations" name="perforations" type="text" value="{{ old('perforations', $issue->perforations) }}" placeholder="Perforations" class="w-full p-2 rounded border shadow @error('perforations') border-red-500 @enderror">
            @error('perforations')
                @component('components.error') {{ $message }} @endcomponent
            @enderror
        </div>
    </div>
    <div class="flex items-center mb-6">
        <div class="w-1/3">
            <label for="gum" class="text-gray-500 font-bold p-4">Gum</label>
        </div>
        <div class="flex flex-col w-2/3">
            <input id="gum" name="gum" type="text" value="{{ old('gum', $issue->gum) }}" placeholder="Gum" class="w-full p-2 rounded border shadow @error('gum') border-red-500 @enderror">
            @error('gum')
                @component('components.error') {{ $message }} @endcomponent
            @enderror
        </div>
    </div>
    <div class="flex items-center mb-6">
        <div class="w-1/3">
            <label for="description" class="text-gray-500 font-bold p-4">Description</label>
        </div>
        <div class="flex flex-col w-2/3">
            <textarea id="description" name="description" placeholder="Description"  rows="8" class="w-full p-2 rounded border shadow @error('description') border-red-500 @enderror">{{ old('description', $issue->description) }}</textarea>
            @error('description')
                @component('components.error') {{ $message }} @endcomponent
            @enderror
        </div>
    </div>
    <div class="flex items-center mb-6">
        <div class="w-1/3">
            <label for="url" class="text-gray-500 font-bold p-4">URL</label>
        </div>
        <div class="flex flex-col w-2/3">
            <input id="url" name="url" type="url" value="{{ old('url', $issue->url) }}" placeholder="URL" class="w-full p-2 rounded border shadow @error('url') border-red-500 @enderror">
            @error('url')
                @component('components.error') {{ $message }} @endcomponent
            @enderror
        </div>
    </div>
    <div class="flex items-center mb-6">
        <div class="w-1/3">
            <label for="sg_url" class="text-gray-500 font-bold p-4">Stanley Gibbons URL</label>
        </div>
        <div class="flex flex-col w-2/3">
            <input id="sg_url" name="sg_url" type="url" value="{{ old('sg_url', $issue->sg_url) }}" placeholder="Stanley Gibbons URL" class="w-full p-2 rounded border shadow @error('sg_url') border-red-500 @enderror">
            @error('sg_url')
                @component('components.error') {{ $message }} @endcomponent
            @enderror
        </div>
    </div>
    <div class="flex items-center justify-center mb-6">
        <button type="submit" class="shadow bg-darker hover:bg-dark focus:shadow-outline focus:outline-none text-white font-bold py-3 px-5 rounded">{{ $button_text }}</button>
        <a href="{{ url()->previous() }}" class="ml-2 border-2 border-dark bg-white hover:bg-light focus:shadow-outline focus:outline-none text-dark font-bold py-3 px-5 rounded">Cancel</a>
    </div>
</form>
