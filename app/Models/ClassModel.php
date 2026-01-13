<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ClassModel extends Model
{
    use HasFactory;

    protected $table = 'classes';

    protected $fillable = [
        'name',
        'subject',
        'description',
        'class_code',
        'created_by',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'class_user', 'class_id', 'user_id')
                    ->using(ClassUser::class)
                    ->withPivot('joined_at')
                    ->withTimestamps();
    }

    public function announcements(): HasMany
    {
        return $this->hasMany(Announcement::class, 'class_id')
                    ->orderBy('created_at', 'desc');
    }

    public function materials(): HasMany
    {
        return $this->hasMany(Material::class, 'class_id')
                    ->orderBy('created_at', 'desc');
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(Assignment::class, 'class_id')
                    ->orderBy('due_date', 'asc');
    }

    public static function generateClassCode(): string
    {
        do {
            $code = strtoupper(substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 6));
        } while (self::where('class_code', $code)->exists());

        return $code;
    }
}