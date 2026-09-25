<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Badge extends Model
{
    protected $table = 'badge';
    protected $fillable = ['nama', 'deskripsi', 'ikon', 'syarat_poin'];

    public function murids(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Murid::class, 'murid_badge')
                    ->withPivot('diperoleh_pada')
                    ->withTimestamps();
    }
}
