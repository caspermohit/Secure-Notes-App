<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'title',
        'content',
        'color',
        'is_archived',
        'is_pinned',
        'last_edited_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_archived' => 'boolean',
        'is_pinned' => 'boolean',
        'last_edited_at' => 'datetime',
    ];

    /**
     * Get the user that owns the note.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the tags for the note.
     */
    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'note_tags');
    }

    /**
     * Scope a query to only include non-archived notes.
     */
    public function scopeActive($query)
    {
        return $query->where('is_archived', false);
    }

    /**
     * Scope a query to only include archived notes.
     */
    public function scopeArchived($query)
    {
        return $query->where('is_archived', true);
    }

    /**
     * Scope a query to only include pinned notes.
     */
    public function scopePinned($query)
    {
        return $query->where('is_pinned', true);
    }
}
