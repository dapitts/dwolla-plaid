<?php

namespace App;

use Auth;
use Illuminate\Support\Facades\DB;

class Whitelabel extends Authenticatable
{
    public function saveDwollaInfo($locationResource, $request) {
        return $this
            ->where('wlId', Auth::user()->wlId)
            ->update([
                'acceptDwollaTOS' => (!empty($request->acceptDwollaTOS)) ? $request->acceptDwollaTOS : 0,
                'acceptDwollaPP' => (!empty($request->acceptDwollaPP)) ? $request->acceptDwollaPP : 0,
                'dwollaBusinessType' => $request->businessType,
                'dwollaLocationResource' => $locationResource
            ]);
    }

    public function updateDwollaBusType($businessType) {
        return $this
            ->where('wlId', Auth::user()->wlId)
            ->update([
                'dwollaBusinessType' => $businessType
            ]);
    }

    public function resetWhitelabel($wlId) {
        return $this
            ->where('wlId', $wlId)
            ->update([
                'acceptDwollaTOS' => 0,
                'acceptDwollaPP' => 0,
                'dwollaBusinessType' => 0,
                'dwollaLocationResource' => null
            ]);
    }
}
