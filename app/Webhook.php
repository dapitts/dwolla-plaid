<?php

namespace App;

use Auth;
use App\Utility;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Webhook extends Model
{
    protected $table = 'webhooks';
    protected $fillable = [
        'webhookId', 'webhookResource', 'deleted'
    ];

    public function addWebhook($webhookResource) {
        $this->webhookId = Utility::getRandomID('WBHK');

        if (!empty($webhookResource))
            $this->webhookResource = $webhookResource;

        $this->save();

        return $this->id;
    }

    public function deleteWebhook($webhookResource) {
        return $this
            ->where('webhookResource', $webhookResource)
            ->update([
                'deleted' => 1
            ]);
    }
}