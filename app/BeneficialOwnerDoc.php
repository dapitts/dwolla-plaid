<?php

namespace App;

use Auth;
use App\Utility;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class BeneficialOwnerDoc extends Model
{
    protected $table = 'beneficial_docs';

    protected $fillable = [
        'beneficialDocId', 'wlId', 'beneficialOwnerId', 'documentResource', 'documentType', 'fileName', 'mimeType'
    ];

    public function addBeneficialOwnerDoc($beneficialOwnerId, $documentResource, $documentType, $fileName, $mimeType) {
        $this->beneficialDocId = Utility::getRandomID('BDOC');
        $this->wlId = Auth::user()->wlId;

        if (!empty($beneficialOwnerId))
            $this->beneficialOwnerId = $beneficialOwnerId;

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

    public function getBeneficialOwnerDocs($beneficialOwnerId) {
        return DB::table('beneficial_docs')
            ->where([
                ['wlId', Auth::user()->wlId],
                ['beneficialOwnerId', $beneficialOwnerId]
            ])
            ->latest()
            ->get();
    }
}
