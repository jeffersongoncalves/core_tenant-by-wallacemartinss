<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Enums\Evolution\StatusConnectionEnum;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WhatsappInstance extends Model
{
   protected $fillable = [
        'organization_id',
        'name',
        'number',
        'instance_id',
        'hash',
        'status',
        'reject_call',
        'msg_call',
        'groups_ignore',
        'always_online',
        'read_messages',
        'read_status',
        'sync_full_history',
        'qr_code'
   ];

   protected $casts = [
        'qr_code' => 'array',
        'groups_ignore' => 'boolean',
        'reject_call' => 'boolean',
        'always_online' => 'boolean',
        'read_messages' => 'boolean',
        'read_status' => 'boolean',
        'sync_full_history' => 'boolean',
        'status' => StatusConnectionEnum::class,
   ];


   public function setQrCodeAttribute($value)
   {
       $this->attributes['qr_code'] = $value;
   }
   public function organization(): BelongsTo
   {
        return $this->belongsTo(Organization::class);
   }

}

