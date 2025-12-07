<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class LawsDocument extends Model
{
    protected $table = 'laws_documents';
    protected $fillable = [
        'law_number',
        'law_title',
        'law_description',
        'issue_date',
        'amendment_date',
        'law_category',
        'attachment',
    ];
    
    public function logalAssistantBots()
    {
        return $this->hasMany(LogalAssistantBot::class, 'source_law_id');
    }

    protected static function booted()
    {
        static::deleting(function ($law) {
            // حذف الملف من التخزين إذا موجود
            if ($law->attachment && Storage::disk('public')->exists($law->attachment)) {
                Storage::disk('public')->delete($law->attachment);
            }
        });
    }
}
