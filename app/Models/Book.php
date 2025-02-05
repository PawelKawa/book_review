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
}
