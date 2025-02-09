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
	public function scopePopular(EloquentBuilder $query, $from = null, $to = null): EloquentBuilder
	{
		return $query->withCount([
			'reviews' => fn(EloquentBuilder $q) => $this->dateRangeFilter($q, $from, $to)
		])->orderBy('reviews_count', 'desc');
	}
	public function scopeHighestRated(EloquentBuilder $query, $from = null, $to = null): EloquentBuilder
	{
		return $query->withAvg([
			'reviews' => fn(EloquentBuilder $q) => $this->dateRangeFilter($q, $from, $to)
		], 'rating')
		->withCount('reviews')
		->orderBy('reviews_avg_rating', 'desc');
	}
	public function scopeHighestRatedAndReviews(EloquentBuilder $query): EloquentBuilder
	{
		return $query->withAvg('reviews', 'rating')
		->withCount('reviews')
		->orderBy('reviews_avg_rating', 'desc')
		->orderBy('reviews_count', 'desc');
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
}
