<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SiteTourItemImage extends Model
{
    use HasFactory;

    protected $table = 'site_tour_item_images';

    protected $fillable = [
        'site_tour_item_id',
        'image_path',
    ];

    public function siteTourItem()
    {
        return $this->belongsTo(SiteTourItem::class);
    }
}
