<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BookController extends Controller
{
	/**
	 * Display a listing of the resource.
	 */
	public function index(Request $request)
	{
		$title = $request->input('title');
		$filter = $request->input('filter', '');

		$books = Book::when(
			$title,
			fn($query, $title) => $query->title($title)
		);
		$books = match ($filter) {
			'popular_last_month' => $books->popularLastMonth(),
			'popular_last_6_months' => $books->popularLast6Months(),
			'highest_rated_last_month' => $books->highestRatedLastMonth(),
			'highest_rated_last_6_months' => $books->highestRatedLast6Months(),
			default => $books->latest()->withAverageRating()->withReviewsCount(),
		};
		// $books = $books->get();

		$cache_key = 'books:' . $title . ':' . $filter;
		$books = cache()->remember($cache_key, now()->addMinutes(60), function () use ($books) {
			return $books->get();
		});
		Log::info('Cache Key:', ['key' => $cache_key]);
		Log::info('Cache Value:', ['value' => cache()->get($cache_key)]);
		Log::info('Books retrieved:', ['books' => $books->toArray()]);
		return view('books.index', compact('books')); //only one parameter
		// return view ('books.index', ['books' => $books, hello => 'world']); //two parameters
	}


	/**
	 * Show the form for creating a new resource.
	 */
	public function create()
	{
		//
	}

	/**
	 * Store a newly created resource in storage.
	 */
	public function store(Request $request)
	{
		//
	}

	/**
	 * Display the specified resource.
	 */
	public function show(int $id)
	{
		$cacheKey = 'book:' . $id;

		$book = cache()->remember($cacheKey, 3600, function () use ($id) {
			return Book::with([
				'reviews' => fn($query) => $query->latest()
			])->withAverageRating()->withReviewsCount()->findOrFail($id);
		});
		return view('books.show', [
			'book' => $book,
			// 'averageRating' => $book->reviews->avg('rating'),
			// 'numberOfReviews' => $book->reviews->count()
		]);
	}

	/**
	 * Show the form for editing the specified resource.
	 */
	public function edit(string $id)
	{
		//
	}

	/**
	 * Update the specified resource in storage.
	 */
	public function update(Request $request, string $id)
	{
		//
	}

	/**
	 * Remove the specified resource from storage.
	 */
	public function destroy(string $id)
	{
		//
	}
}
