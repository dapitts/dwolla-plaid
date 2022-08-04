@extends('layouts.whitelabel')
@section('content')

<section class="full-page bg-color fullpage cf upload-docs">
    <div class="full-pg-content-container top-padding cf">
        <div class="wrapper cf">
            <div class="community-tab-content-wrapper active cf">
                <div class="breadcrumb-wrapper cf">
                    <ul class="breadcrumbs">
                        <li><a href="{{ route('wl-settings') }}" title="Settings">Settings</a></li>
                        <li><a href="{{ route('wl-payment-settings') }}" title="Payment Settings">Payment Settings</a></li>
                        <li><a href="{{ route('wl-beneficial-owners') }}" title="Beneficial Owners">Beneficial Owners</a></li>
                        <li><a href="{{ route('wl-beneficial-documents', $beneficialOwner->id) }}" title="Beneficial Owner Documents">Beneficial Owner Documents</a></li>
                        <li class="active-page">Upload Beneficial Owner Documents</li>
                    </ul>
                </div>

                <h1 class="color h1-border">Upload Beneficial Owner Documents</h1>

                <p>To upload a color photo of a document, the file must be either a .jpg, .jpeg, .png, or .pdf. Files must be no larger than 10MiB in size.</p>

                <form id="upload-docs-form" class="setting-form medium cf" action="{{ route('wl-upload-beneficial-save', $beneficialOwner->id) }}" autocomplete="off" method="post" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    
                    <div id="verify-with-doc" @if (empty($beneficialOwner->_links->{'verify-with-document'})) style="display:none;" @endif>
                        <h2>Beneficial Owner Documents</h2>
                        <p>
                            For the Beneficial Owner’s identifying document to upload, a color scan of a US Government issued photo identification (e.g., a driver’s license, 
                            state ID card, or passport) is required.
                        </p>

                        @if (!empty($beneficialOwner->_links->{'verify-with-document'}))
                        <div class="upload-region cf">
                            <div class="lfloat lside">
                                <h2>Document Type</h2>
                                <select id="document-type" name="documentType">
                                    <option value="">Select Document Type</option>
                                    <option value="license">Driver's License (US persons)</option>
                                    <option value="idCard">State ID Card (US persons)</option>
                                    <option value="passport">Passport (Non-US persons)</option>
                                </select>
                            </div>
                            <div class="rfloat rside">
                                <h2>Choose Document</h2>
                                <input id="beneficial-doc" type="file" name="beneficialDoc" accept="image/jpeg, image/png, application/pdf">
                            </div>
                        </div>
                        @endif
                    </div>
                </form>

                <div class="button-wrapper cf">
                    <div id="upload-docs-cancel" class="label-wrapper lfloat">
						<button class="btn btn-danger" type="button">Cancel</button>
                    </div>
                    <div id="upload-docs-save" class="label-wrapper rfloat save-btn-wrapper">
                        <button class="btn btn-success" type="button">Upload</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<script>
    $(document).ready(function() {
        $('#upload-docs-cancel').click(function() {
            location = location.protocol + '//' + location.hostname + '/whitelabel/settings/beneficial-documents/{{ $beneficialOwner->id }}';
        });

        $('#upload-docs-save').click(function() {
            if (validateUploadDocs())
                $('#upload-docs-form').submit();
        });

        $('#beneficial-doc').on('change', function() {
            let files = $(this)[0].files;

            if (files.length == 1 && files[0].size > 10485760)
                alert('The file ' + files[0].name + ' exceeds the 10MiB size limit!');
        });

        function validateUploadDocs() {
            if ($('#verify-with-doc').is(':visible')) {
                let documentType = $('#document-type'),
                    files = $('#beneficial-doc')[0].files;

                if (documentType.val() != '' && files.length == 1 && files[0].size <= 10485760)
                    return true;
                else
                    return false;
            }
        }
    });
</script>
@endsection