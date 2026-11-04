<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
class Content extends Model
{
    use HasFactory;
    protected $table = "contents";

     protected $fillable = [
            'type',
            'title',
            'text',
            'image',
        ];
    
        public const TYPES = [
            'logo'   => 'Logo',
            'banner' => 'Banner'
        ];
    
        /**
         * Get full image URL.
         */
        public function getImageUrlAttribute(): ?string
        {
            if ($this->image && Storage::disk('public')->exists($this->image)) {
                return asset('storage/' . $this->image);
            }
            return null;
        }
    
        /**
         * Get latest content by type.
         */
        public static function ofType(string $type): ?self
        {
            return static::where('type', $type)->latest()->first();
        }
}