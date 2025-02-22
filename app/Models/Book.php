<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Book extends Model
{
	use HasFactory;
	public function reviews()
	{
		return $this->hasMany(Review::class);
	}
	public function scopeTitle(EloquentBuilder $query, string $title): EloquentBuilder
	{
		return $query->where('title', 'LIKE', '%' . $title . '%');
	}

	public function scopeWithReviewsCount(EloquentBuilder $query, $from = null, $to = null): EloquentBuilder
	{
		return $query->withCount([
			'reviews' => fn(EloquentBuilder $q) => $this->dateRangeFilter($q, $from, $to)
		]);
	}
	public function scopeWithAverageRating(EloquentBuilder $query, $from = null, $to = null): EloquentBuilder
	{
		return $query->withAvg([
			'reviews' => fn(EloquentBuilder $q) => $this->dateRangeFilter($q, $from, $to)
		], 'rating');
	}
	public function scopePopular(EloquentBuilder $query): EloquentBuilder
	{
		return $query->WithReviewsCount()->orderBy('reviews_count', 'desc');
	}
	public function scopeHighestRated(EloquentBuilder $query): EloquentBuilder
	{
		return $query->WithAverageRating()->orderBy('reviews_avg_rating', 'desc');
	}



	public function scopeMinReviews(EloquentBuilder $query, int $minReviews): EloquentBuilder
	{
		return $query->withCount('reviews')
		->having('reviews_count', '>=', $minReviews);
	}
	private function dateRangeFilter(EloquentBuilder $query, $from = null, $to = null): EloquentBuilder
	{
		if ($from && $to) {
			return $query->whereBetween('created_at', [$from, $to]);
		} elseif ($from) {
			return $query->where('created_at', '>=', $from);
		} elseif ($to) {
			return $query->where('created_at', '<=', $to);
		}
		return $query;
	}
	public function scopePopularLastMonth(EloquentBuilder $query): EloquentBuilder
	{
		// return $query->popular(now()->subMonth(), now())
		// 	->highestRated(now()->subMonth(), now())
		// 	->minReviews(2);
		return
			Book::withCount([
				'reviews' => function ($query) {
					$query->whereBetween('created_at', [now()->subMonth(), now()]);
				}
			])
			->having('reviews_count', '>=', 2)
			->withAvg(
				[
					'reviews' => function ($query) {
						$query->whereBetween('created_at', [now()->subMonth(), now()]);
					}
				],
				'rating'
			)
			->orderBy('reviews_count', 'desc')
			->orderBy('reviews_avg_rating', 'desc');
	}
	public function scopePopularLast6Months(EloquentBuilder $query): EloquentBuilder
	{
		// return $query->popular(now()->subMonths(6), now())
		// 	->highestRated(now()->subMonths(6), now())
		// 	->minReviews(5);
		return Book::withCount([
			'reviews' => function ($query) {
				$query->whereBetween('created_at', [now()->subMonths(6), now()]);
			}
		])
			->having('reviews_count', '>=', 5)
			->withAvg(
				[
					'reviews' => function ($query) {
						$query->whereBetween('created_at', [now()->subMonths(6), now()]);
					}
				],
				'rating'
			)
			->orderBy('reviews_count', 'desc')
			->orderBy('reviews_avg_rating', 'desc');
	}
	public function scopeHighestRatedLastMonth(EloquentBuilder $query): EloquentBuilder
	{
		return $query->highestRated(now()->subMonth(), now())
			->popular(now()->subMonth(), now())
			->minReviews(2);
	}
	public function scopeHighestRatedLast6Months(EloquentBuilder $query): EloquentBuilder
	{
		return $query->highestRated(now()->subMonths(6), now())
			->popular(now()->subMonths(6), now())
			->minReviews(5);
	}
	protected static function booted()
	{
		static::updated(fn(Book $book) => cache()->forget('book:' . $book->id));
		static::deleted(fn(Book $book) => cache()->forget('book:' . $book->id));
	}
}
