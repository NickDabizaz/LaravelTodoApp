<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Todo extends Model
{
    protected $table = "todos";

    protected $fillable = [
        'title', 
        'details', 
        'isDone',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}