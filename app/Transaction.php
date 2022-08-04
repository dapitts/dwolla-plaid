<?php

namespace App;

use Auth;
use App\Utility;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Transaction extends Model
{
    protected $table = 'transactions';
    protected $fillable = [
        'transactionId', 'wlId', 'projectId', 'dealId', 'subscriberId', 'custodianId', 'investmentId', 'projectName', 'subscriberFullName', 'amount', 'transferResource', 'cancelled', 
        'failed'
    ];

    public function addTransaction($transferInfo) {
        $this->transactionId = Utility::getRandomID('TRAN');
        $this->wlId = Auth::user()->wlId;

        if (!empty($transferInfo->projectId))
            $this->projectId = $transferInfo->projectId;

        if (!empty($transferInfo->dealId))
            $this->dealId = $transferInfo->dealId;

        if (!empty($transferInfo->subscriberId))
            $this->subscriberId = $transferInfo->subscriberId;

        if (!empty($transferInfo->custodianId))
            $this->custodianId = $transferInfo->custodianId;

        if (!empty($transferInfo->investmentId))
            $this->investmentId = $transferInfo->investmentId;

        if (!empty($transferInfo->projectName))
            $this->projectName = $transferInfo->projectName;

        if (!empty($transferInfo->subscriberFullName))
            $this->subscriberFullName = $transferInfo->subscriberFullName;

        if (!empty(floatval($transferInfo->amount)))
            $this->amount = floatval($transferInfo->amount);

        if (!empty($transferInfo->transferResource))
            $this->transferResource = $transferInfo->transferResource;

        $this->save();

        return $this->id;
    }

    public function getTransactionsByWlId() {
        return DB::table('transactions')
            ->select('projectName', 'subscriberFullName', 'amount', 'transferResource', 'created_at')
            ->where('wlId', Auth::user()->wlId)
            ->latest()
            ->paginate(10);
    }

    public function getTransactionsByWlIdAndName($searchTerm) {
        return DB::table('transactions')
            ->select('projectName', 'subscriberFullName', 'amount', 'transferResource', 'created_at')
            ->where('wlId', Auth::user()->wlId)
            ->where('subscriberFullName', 'like', '%' . $searchTerm . '%')
            ->latest()
            ->paginate(10);
    }

    public function getTransactionsBySubscriberId() {
        return DB::table('transactions')
            ->select('projectName', 'amount', 'transferResource', 'created_at')
            ->where([
                ['subscriberId', Auth::user()->subscriberId],
                ['wlId', Auth::user()->wlId]
            ])
            ->latest()
            ->paginate(10);
    }

    public function updateStatus($transferResource, $column, $value = 1) {
        return $this->where('transferResource', $transferResource)->update([$column => $value]);
    }

    public function getWlTransactionAmts() {
        return $this
            ->where([
                ['wlId', Auth::user()->wlId],
                ['cancelled', 0],
                ['failed', 0]
            ])
            ->sum('amount');
    }
}
