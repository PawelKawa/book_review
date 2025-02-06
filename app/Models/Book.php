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
	public function scopePopular(EloquentBuilder $query): EloquentBuilder
	{
		return $query->withCount('reviews')->orderBy('reviews_count', 'desc');
	}
	public function scopeHighestRated(EloquentBuilder $query): EloquentBuilder
	{
		return $query->withAvg('reviews', 'rating')
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
}
