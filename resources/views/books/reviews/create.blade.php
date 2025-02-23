@extends('layouts.app')

@section('content')
    <h1 class="mb-10 text-2xl">Add Review for: <span class="font-bold block">{{ $book->title }}</span> </h1>
	<div class="flex items-center justify-between">
		<a href="{{ route('books.show', $book) }}" class="reset-link">Cancel</a>
		<a href="{{ route('books.index') }}" class="reset-link">Back to books</a>
	</div>
    <form method="POST" action="{{ route('books.reviews.store', $book) }}" class="mt-8">
        @csrf
        <label for="review">Review</label>
        <textarea name="review" id="review" required class="input mb-4">{{ old('review') }}</textarea>
        @error('review')
            <span class="text-red-500 text-sm">{{ $message }}</span>
        @enderror
        <label class="block" for="rating">Rating</label>

        <select name="rating" id="rating" class="input mb-4" required>
            <option value="">Select a Rating</option>
            @for ($i = 1; $i <= 5; $i++)
                <option value="{{ $i }}" {{ old('rating') == $i ? 'selected' : '' }}>{{ $i }}
                </option>
            @endfor
        </select>
        @error('rating')
            <span class="text-red-500 text-sm">{{ $message }}</span>
        @enderror

        <button type="submit" class="btn">Add Review</button>
    </form>
@endsection
