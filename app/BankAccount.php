<?php

namespace App;

use Auth;
use App\Utility;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class BankAccount extends Model
{
    protected $table = 'bank_accounts';
    protected $fillable = [
        'bankAccountId', 'wlId', 'subscriberId', 'accountName', 'accountType', 'accountNumber', 'account_id', 'abaRoutingTransitNumber', 'wireTransferRoutingNumber', 
        'wireRoutingNull', 'bankName', 'bankStreet', 'bankCity', 'bankState', 'bankZipCode', 'access_token', 'item_id', 'request_id', 'add_bank_acct_method', 'funding_source_url', 
        'deleted'
    ];

    public function addBankAccount($authInfo) {
        $this->bankAccountId = Utility::getRandomID('ACCT');
        $this->wlId = Auth::user()->wlId;

        if (!empty(Auth::user()->subscriberId))
            $this->subscriberId = Auth::user()->subscriberId;

        if (!empty($authInfo->account_name))
            $this->accountName = $authInfo->account_name;

        if (!empty($authInfo->account_type))
            $this->accountType = $authInfo->account_type;

        if (!empty($authInfo->account)) {
            try {
                $this->accountNumber = Crypt::encryptString($authInfo->account);
            } catch (EncryptException $e) {}
        }

        if (!empty($authInfo->account_id))
            $this->account_id = $authInfo->account_id;

        if (!empty($authInfo->routing)) {
            try {
                $this->abaRoutingTransitNumber = Crypt::encryptString($authInfo->routing);
            } catch (EncryptException $e) {}
        }

        if (!empty($authInfo->wire_routing)) {
            try {
                $this->wireTransferRoutingNumber = Crypt::encryptString($authInfo->wire_routing);
            } catch (EncryptException $e) {}
        }

        $this->wireRoutingNull = empty($authInfo->wire_routing) ? 1 : 0;

        if (!empty($authInfo->bank_name))
            $this->bankName = $authInfo->bank_name;

        if (!empty($authInfo->access_token))
            $this->access_token = $authInfo->access_token;

        if (!empty($authInfo->item_id))
            $this->item_id = $authInfo->item_id;

        if (!empty($authInfo->request_id))
            $this->request_id = $authInfo->request_id;

        $this->add_bank_acct_method = 1;

        $this->save();

        return [$this->id, $this->bankAccountId];
    }

    public function updateBankAccount($authInfo) {
        $bankAcct = $this
            ->where('bankAccountId', $authInfo->bankAccountId)
            ->first();

        if (!empty($authInfo->account_name))
            $bankAcct->accountName = $authInfo->account_name;

        if (!empty($authInfo->account_type))
            $bankAcct->accountType = $authInfo->account_type;

        if (!empty($authInfo->account)) {
            try {
                $bankAcct->accountNumber = Crypt::encryptString($authInfo->account);
            } catch (EncryptException $e) {}
        }

        if (!empty($authInfo->account_id))
            $bankAcct->account_id = $authInfo->account_id;

        if (!empty($authInfo->routing)) {
            try {
                $bankAcct->abaRoutingTransitNumber = Crypt::encryptString($authInfo->routing);
            } catch (EncryptException $e) {}
        }

        if (!empty($authInfo->wire_routing)) {
            try {
                $bankAcct->wireTransferRoutingNumber = Crypt::encryptString($authInfo->wire_routing);
            } catch (EncryptException $e) {}
        } else
            $bankAcct->wireTransferRoutingNumber = null;

        $bankAcct->wireRoutingNull = empty($authInfo->wire_routing) ? 1 : 0;

        if (!empty($authInfo->bank_name))
            $bankAcct->bankName = $authInfo->bank_name;

        if (!empty($authInfo->access_token))
            $bankAcct->access_token = $authInfo->access_token;

        if (!empty($authInfo->item_id))
            $bankAcct->item_id = $authInfo->item_id;

        if (!empty($authInfo->request_id))
            $bankAcct->request_id = $authInfo->request_id;

        if (!empty($bankAcct->funding_source_url))
            $bankAcct->funding_source_url = null;

        $bankAcct->save();

        return $bankAcct->id;
    }

    public function editBankAccount($request) {
        $bankAcct = $this
            ->where('bankAccountId', $request->bankAccountId)
            ->first();
        
        if (!empty($request->accountName))
            $bankAcct->accountName = $request->accountName;

        if (!empty($request->accountType))
            $bankAcct->accountType = $request->accountType;

        if (!empty($request->accountNumber)) {
            try {
                $bankAcct->accountNumber = Crypt::encryptString($request->accountNumber);
            } catch (EncryptException $e) {}
        }

        if (!empty($request->abaRoutingTransitNumber)) {
            try {
                $bankAcct->abaRoutingTransitNumber = Crypt::encryptString($request->abaRoutingTransitNumber);
            } catch (EncryptException $e) {}
        }

        if (!empty($request->wireTransferRoutingNumber)) {
            try {
                $bankAcct->wireTransferRoutingNumber = Crypt::encryptString($request->wireTransferRoutingNumber);
            } catch (EncryptException $e) {}
        }

        if (!empty($request->bankName))
            $bankAcct->bankName = $request->bankName;

        if (!empty($request->bankStreet))
            $bankAcct->bankStreet = $request->bankStreet;

        if (!empty($request->bankCity))
            $bankAcct->bankCity = $request->bankCity;

        if (!empty($request->bankState))
            $bankAcct->bankState = $request->bankState;

        if (!empty($request->bankZipCode))
            $bankAcct->bankZipCode = $request->bankZipCode;

        $bankAcct->save();

        return $bankAcct->id;
    }

    public function addFundingSource($request, $method = 2) {
        $this->bankAccountId = Utility::getRandomID('ACCT');
        $this->wlId = Auth::user()->wlId;

        if (!empty(Auth::user()->subscriberId))
            $this->subscriberId = Auth::user()->subscriberId;

        if (!empty($request->accountName))
            $this->accountName = $request->accountName;

        if (!empty($request->accountType))
            $this->accountType = $request->accountType;

        if (!empty($request->accountNumber)) {
            try {
                $this->accountNumber = Crypt::encryptString($request->accountNumber);
            } catch (EncryptException $e) {}
        }

        if (!empty($request->abaRoutingTransitNumber)) {
            try {
                $this->abaRoutingTransitNumber = Crypt::encryptString($request->abaRoutingTransitNumber);
            } catch (EncryptException $e) {}
        }

        if (!empty($request->wireTransferRoutingNumber)) {
            try {
                $this->wireTransferRoutingNumber = Crypt::encryptString($request->wireTransferRoutingNumber);
            } catch (EncryptException $e) {}
        }

        if (!empty($request->bankName))
            $this->bankName = $request->bankName;

        if (!empty($request->bankStreet))
            $this->bankStreet = $request->bankStreet;

        if (!empty($request->bankCity))
            $this->bankCity = $request->bankCity;

        if (!empty($request->bankState))
            $this->bankState = $request->bankState;

        if (!empty($request->bankZipCode))
            $this->bankZipCode = $request->bankZipCode;

        $this->add_bank_acct_method = $method;

        if (!empty($request->fundingSourceURL))
            $this->funding_source_url = $request->fundingSourceURL;

        $this->save();

        return $this->id;
    }

    public function getBankAccountsByWlId() {
        return $this
            ->select('bankAccountId', 'accountName', 'accountType', 'bankName', 'add_bank_acct_method', 'funding_source_url')
            ->where([
                ['wlId', Auth::user()->wlId], 
                ['deleted', 0]
            ])
            ->whereNull('subscriberId')
            ->latest()
            ->get();
    }

    public function getFundingSrcBankAccts() {
        return DB::table('bank_accounts')
            ->select('bankAccountId', 'accountName', 'accountType', 'bankName', 'add_bank_acct_method', 'funding_source_url')
            ->where([
                ['wlId', Auth::user()->wlId], 
                ['deleted', 0]
            ])
            ->whereIn('add_bank_acct_method', [1, 2])  // Plaid, Dwolla funding source
            ->whereNull('subscriberId')
            ->latest()
            ->get();
    }

    public function getBankAcctsBySubscriberId() {
        return $this
            ->select('bankAccountId', 'accountName', 'accountType', 'bankName', 'add_bank_acct_method', 'funding_source_url')
            ->where([
                ['subscriberId', Auth::user()->subscriberId], 
                ['deleted', 0]
            ])
            ->latest()
            ->get();
    }

    public function getBankAccount($bankAccountId) {
        $bankAcct = $this
            ->where([
                ['bankAccountId', $bankAccountId],
                ['deleted', 0]
            ])
            ->first();

        if ($bankAcct) {
            if (strlen($bankAcct->accountNumber) > 150) {
                try {
                    $bankAcct->accountNumber = Crypt::decryptString($bankAcct->accountNumber);
                } catch (DecryptException $e) {}
            }

            if (strlen($bankAcct->abaRoutingTransitNumber) > 150) {
                try {
                    $bankAcct->abaRoutingTransitNumber = Crypt::decryptString($bankAcct->abaRoutingTransitNumber);
                } catch (DecryptException $e) {}
            }

            if (strlen($bankAcct->wireTransferRoutingNumber) > 150) {
                try {
                    $bankAcct->wireTransferRoutingNumber = Crypt::decryptString($bankAcct->wireTransferRoutingNumber);
                } catch (DecryptException $e) {}
            }
        }

        return $bankAcct;
    }

    public function getBankAccountById($id) {
        $bankAcct = $this->where('id', intval($id))->first();

        if ($bankAcct) {
            if (strlen($bankAcct->accountNumber) > 150) {
                try {
                    $bankAcct->accountNumber = Crypt::decryptString($bankAcct->accountNumber);
                } catch (DecryptException $e) {}
            }

            if (strlen($bankAcct->abaRoutingTransitNumber) > 150) {
                try {
                    $bankAcct->abaRoutingTransitNumber = Crypt::decryptString($bankAcct->abaRoutingTransitNumber);
                } catch (DecryptException $e) {}
            }

            if (strlen($bankAcct->wireTransferRoutingNumber) > 150) {
                try {
                    $bankAcct->wireTransferRoutingNumber = Crypt::decryptString($bankAcct->wireTransferRoutingNumber);
                } catch (DecryptException $e) {}
            }
        }

        return $bankAcct;
    }

    public function removeFundingSource($fundingSourceURL) {
        return $this->where('funding_source_url', $fundingSourceURL)->update(['funding_source_url' => null]);
    }

    public function deleteBankAccount($bankAccountId, $hasFundingSourceURL = false) {
        if ($hasFundingSourceURL)
            return $this->where('bankAccountId', $bankAccountId)->update(['deleted' => 1, 'funding_source_url' => null]);
        else
            return $this->where('bankAccountId', $bankAccountId)->update(['deleted' => 1]);
    }

    public function setFundingSourceURL($id, $fundingSourceURL) {
        $bankAcct = $this->find(intval($id));

        if (!empty($fundingSourceURL))
            $bankAcct->funding_source_url = $fundingSourceURL;

        $bankAcct->save();

        return $bankAcct->id;
    }

    public function deleteFundingSourceURL($bankAccountId) {
        return $this->where('bankAccountId', $bankAccountId)->update(['funding_source_url' => null]);
    }
}