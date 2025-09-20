<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;

use App\Models\Hosts_Setting;

class Host extends Model
{
    use HasFactory;
    
    public $timestamps = true;
    
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }    

    public function host_settings(): HasOne
    {
        return $this->hasOne(Hosts_Setting::class);
    }
}
