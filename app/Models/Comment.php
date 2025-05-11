<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = ['recipe_id', 'author', 'content'];

    public function recipe()
    {
        return $this->belongsTo(Recipe::class);
    }
}
