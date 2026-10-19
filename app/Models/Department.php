<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'status',
        'monitor_token',
    ];

    protected static function booted()
    {
        static::creating(function ($department) {
            if (empty($department->slug)) {
                $baseSlug = \Illuminate\Support\Str::slug($department->name);
                $slug = $baseSlug;
                $counter = 1;
                while (static::where('slug', $slug)->exists()) {
                    $slug = $baseSlug . '-' . $counter++;
                }
                $department->slug = $slug;
            }

            if (empty($department->monitor_token)) {
                $department->monitor_token = \Illuminate\Support\Str::random(32);
            }
        });
    }

    public function getMonitorUrlAttribute()
    {
        return route('monitor.bidang', [
            'department' => $this->slug,
            'token' => $this->monitor_token,
        ]);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
