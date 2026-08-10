<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Attachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'attachable_id',
        'attachable_type',
        'name',
        'file',
        'type',
        'size',
    ];

    /**
     * Get the parent attachable model
     */
    public function attachable()
    {
        return $this->morphTo();
    }

    /**
     * Get the icon of the file
     */
    public function getIconAttribute()
    {
        $ext = strtolower(pathinfo($this->file, PATHINFO_EXTENSION));

        return match($ext){
            'jpg', 'jpeg', 'png', 'webp', 'gif', 'bmp', 'svg' => 'fa-file-image',
            'mp4', 'mov', 'avi', 'mkv', 'webm' => 'fa-file-video',
            'pdf' => 'fa-file-pdf',
            'json' => 'fa-file-lines',
            'doc', 'docx' => 'fa-file-word',
            'xls', 'xlsx' => 'fa-file-excel',
            'ppt', 'pptx' => 'fa-file-powerpoint',
            'zip', 'rar', '7z' => 'fa-file-zipper',
            default => 'fa-file',
        };
    }
}
