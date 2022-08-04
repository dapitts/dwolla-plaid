<?php

namespace App;

use Auth;
use App\Utility;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CustomerDoc extends Model
{
    protected $table = 'customer_docs';
    
    protected $fillable = [
        'customerDocId', 'wlId', 'locationResource', 'documentResource', 'documentType', 'fileName', 'mimeType'
    ];

    public function addCustomerDoc($documentResource, $documentType, $fileName, $mimeType) {
        $this->customerDocId = Utility::getRandomID('CDOC');
        $this->wlId = Auth::user()->wlId;

        if (!empty(Auth::user()->dwollaLocationResource))
            $this->locationResource = Auth::user()->dwollaLocationResource;

        if (!empty($documentResource))
            $this->documentResource = $documentResource;

        if (!empty($documentType))
            $this->documentType = $documentType;

        if (!empty($fileName))
            $this->fileName = $fileName;

        if (!empty($mimeType))
            $this->mimeType = $mimeType;

        $this->save();

        return $this->id;
    }

    public function getCustomerDocs() {
        return DB::table('customer_docs')
            ->where([
                ['wlId', Auth::user()->wlId],
                ['locationResource', Auth::user()->dwollaLocationResource]
            ])
            ->latest()
            ->get();
    }
}
