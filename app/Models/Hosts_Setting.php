<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Host;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class Hosts_Setting extends Model
{
    use HasFactory;
    
    protected $table = 'hosts_settings';

    public $timestamps = true;
    
    public function host(): BelongsTo
    {
        return $this->belongsTo(Host::class);
    }    
}
