<?php

namespace App;

use Auth;
use Illuminate\Support\Facades\DB;

class Subscribers extends Authenticatable
{
    public function saveDwollaInfo($locationResource) {
        return $this->where('subscriberId', Auth::user()->subscriberId)->update(['dwollaLocationResource' => $locationResource]);
    }

    public function getSubscribersByProjectId($projectId) {
        return DB::table('subscribers')
            ->select('subscribers.subscriberFullName', 'subscribers.subscriberId')
            ->join('investments', 'subscribers.subscriberId', '=', 'investments.subscriberId')
            ->where([
                ['investments.wlId', Auth::user()->wlId],
                ['investments.projectId', $projectId],
                ['investments.funded', 1],
                ['investments.enabled', 1],
                ['investments.stage', 5]
            ])
            ->orderBy('subscribers.subscriberFullName', 'asc')
            ->distinct()
            ->get()
            ->toArray();
    }

    public function resetSubscriber($subscriberId) {
        return $this
            ->where('subscriberId', $subscriberId)
            ->update([
                'dwollaLocationResource' => null
            ]);
    }
}
