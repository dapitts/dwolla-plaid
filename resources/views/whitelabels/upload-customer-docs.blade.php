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
                        <li><a href="{{ route('wl-customer-documents') }}" title="Customer Documents">Customer Documents</a></li>
                        <li class="active-page">Upload Customer Documents</li>
                    </ul>
                </div>

                <h1 class="color h1-border">Upload Customer Documents</h1>

                <p>
                    To upload a color photo of a document, the file must be either a .jpg, .jpeg, .png, or .pdf. Files must be no larger than 10MiB in size. 
                    <span @if (!empty($custInfo->_links->{'verify-with-document'})) style="display:none;" @endif>
                        A maximum of four documents can be uploaded until they are reviewed by Dwolla. 
                    </span>
                </p>

                <form id="upload-docs-form" class="setting-form medium cf" action="{{ route('wl-upload-cust-docs-save') }}" autocomplete="off" method="post" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    
                    <div id="verify-with-doc" @if (empty($custInfo->_links->{'verify-with-document'})) style="display:none;" @endif>
                        <h2>Controller Documents</h2>
                        <p>
                            For the Controller’s identifying document to upload, a color scan of a US Government issued photo identification (e.g., a driver’s license, 
                            passport, or state ID card) is required.
                        </p>

                        @if (!empty($custInfo->_links->{'verify-with-document'}))
                        <input type="hidden" name="uploadType" value="verify-controller">

                        <div class="upload-region cf">
                            <div class="lfloat lside">
                                <h2>Document Type</h2>
                                <select id="document-type" name="documentType">
                                    <option value="">Select Document Type</option>
                                    <option value="license">Driver's License</option>
                                    <option value="passport">Passport</option>
                                    <option value="idCard">State ID Card</option>
                                </select>
                            </div>
                            <div class="rfloat rside">
                                <h2>Choose Document</h2>
                                <input id="controller-doc" type="file" name="controllerDoc" accept="image/jpeg, image/png, application/pdf">
                            </div>
                        </div>
                        @endif
                    </div>

                    <div id="verify-bus-with-doc" @if (empty($custInfo->_links->{'verify-business-with-document'})) style="display:none;" @endif>
                        <h2>Business Documents</h2>

                        <p>Recommended business identifying documents to upload can include the following:</p>

                        @if (!empty($custInfo->controller))
                        <p>An EIN confirmation letter is the preferred document to upload for the following business structures:</p>
                        <ul>
                            <li><strong>Partnership, General Partnership</strong>: EIN Letter (IRS-issued SS4 confirmation letter).</li>
                            <li><strong>Limited Liability Corporation (LLC), Corporation</strong>: EIN Letter (IRS-issued SS4 confirmation letter).</li>
                        </ul>
                        @else
                        <ul class="sole-proprietor">
                            <li>
                                <strong>Sole Proprietorship</strong>: One or more of the following, as applicable to your sole proprietorship: Fictitious Business Name Statement, 
                                Certificate of Assumed Name, Business License, Sales/Use Tax License, Registration of Trade Name, EIN documentation (IRS-issued SS4 confirmation 
                                letter), or a color copy of a valid government-issued photo ID (e.g., a driver’s license, passport, or state ID card).
                            </li>
                        </ul>
                        @endif

                        <p>
                            Other business documents that are applicable includes any US government entity (federal, state, local) issued business formation or any business 
                            formation documents exhibiting the name of the business entity in addition to being filed and stamped by a US government entity. Examples include:
                        </p>

                        <ul>
                            <li>Filed and stamped Articles of Organization or Incorporation</li>
                            <li>Sales/Use Tax License</li>
                            <li>Business License</li>
                            <li>Certificate of Good Standing</li>
                        </ul>

                        @if (!empty($custInfo->_links->{'verify-business-with-document'}))
                        <input type="hidden" name="documentType" value="other">
                        <input type="hidden" name="uploadType" value="verify-business">

                        <div class="upload-region cf">
                            <div class="lfloat lside">
                                <h2>Choose Documents</h2>
                                <input id="business-docs" type="file" name="businessDocs[]" accept="image/jpeg, image/png, application/pdf" multiple>
                            </div>
                            <div class="rfloat rside">
                                <h2>Selected Files</h2>
                                <div id="selected-files"></div>
                            </div>
                        </div>
                        @endif
                    </div>

                    <div id="verify-cntlr-and-bus-with-doc" @if (empty($custInfo->_links->{'verify-controller-and-business-with-document'})) style="display:none;" @endif>
                        <h2>Controller and Business Documents</h2>
                        <div class="content-region cf">
                            <div class="lfloat lside">
                                <h2>Controller Documents</h2>
                                <p>
                                    For the Controller’s identifying document to upload, a color scan of a US Government issued photo identification (e.g., a driver’s license, 
                                    passport, or state ID card) is required.
                                </p>

                                @if (!empty($custInfo->_links->{'verify-controller-and-business-with-document'}))
                                <input type="hidden" name="uploadType" value="verify-cntlr-and-business">

                                <div class="upload-region cf">
                                    <h2>Document Type</h2>
                                    <select id="cntlr-doc-type" name="cntlrDocumentType">
                                        <option value="">Select Document Type</option>
                                        <option value="license">Driver's License</option>
                                        <option value="passport">Passport</option>
                                        <option value="idCard">State ID Card</option>
                                    </select>

                                    <h2>Choose Document</h2>
                                    <input id="controller-doc" type="file" name="controllerDoc" accept="image/jpeg, image/png, application/pdf">
                                </div>
                                @endif
                            </div>
                            <div class="rfloat rside">
                                <h2>Business Documents</h2>
                                
                                <p>Recommended business identifying documents to upload can include the following:</p>

                                @if (!empty($custInfo->controller))
                                <p>An EIN confirmation letter is the preferred document to upload for the following business structures:</p>
                                <ul>
                                    <li><strong>Partnership, General Partnership</strong>: EIN Letter (IRS-issued SS4 confirmation letter).</li>
                                    <li><strong>Limited Liability Corporation (LLC), Corporation</strong>: EIN Letter (IRS-issued SS4 confirmation letter).</li>
                                </ul>
                                @else
                                <ul class="sole-proprietor">
                                    <li>
                                        <strong>Sole Proprietorship</strong>: One or more of the following, as applicable to your sole proprietorship: Fictitious Business Name Statement, 
                                        Certificate of Assumed Name, Business License, Sales/Use Tax License, Registration of Trade Name, EIN documentation (IRS-issued SS4 confirmation 
                                        letter), or a color copy of a valid government-issued photo ID (e.g., a driver’s license, passport, or state ID card).
                                    </li>
                                </ul>
                                @endif

                                <p>
                                    Other business documents that are applicable includes any US government entity (federal, state, local) issued business formation or any business 
                                    formation documents exhibiting the name of the business entity in addition to being filed and stamped by a US government entity. Examples include:
                                </p>

                                <ul>
                                    <li>Filed and stamped Articles of Organization or Incorporation</li>
                                    <li>Sales/Use Tax License</li>
                                    <li>Business License</li>
                                    <li>Certificate of Good Standing</li>
                                </ul>

                                @if (!empty($custInfo->_links->{'verify-controller-and-business-with-document'}))
                                <input type="hidden" name="busDocumentType" value="other">

                                <div class="upload-region cf">
                                    <h2>Choose Documents</h2>
                                    <input id="business-docs" type="file" name="businessDocs[]" accept="image/jpeg, image/png, application/pdf" multiple>

                                    <h2>Selected Files</h2>
                                    <div id="selected-files"></div>
                                </div>
                                @endif
                            </div>
                        </div>
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
            location = location.protocol + '//' + location.hostname + '/whitelabel/settings/customer-documents';
        });

        $('#upload-docs-save').click(function() {
            if (validateUploadDocs())
                $('#upload-docs-form').submit();
        });

        $('#business-docs').on('change', function() {
            let files = $(this)[0].files,
                len = files.length,
                file_names = '';

            for (let i = 0; i < len; i++) {
                file_names += files[i].name;

                if (files[i].size > 10485760)
                    alert('The file ' + files[i].name + ' exceeds the 10MiB size limit!');

                if (i != len - 1)
                    file_names += '<br>';
            }

            $('#selected-files').html(file_names);
        });

        $('#controller-doc').on('change', function() {
            let files = $(this)[0].files;

            if (files.length == 1 && files[0].size > 10485760)
                alert('The file ' + files[0].name + ' exceeds the 10MiB size limit!');
        });

        function validateUploadDocs() {
            if ($('#verify-with-doc').is(':visible')) {
                let documentType = $('#document-type'),
                    files = $('#controller-doc')[0].files;

                if (documentType.val() != '' && files.length == 1 && files[0].size <= 10485760)
                    return true;
                else
                    return false;
            } else if ($('#verify-bus-with-doc').is(':visible')) {
                let files = $('#business-docs')[0].files,
                    sizeExceeded = false;

                for (let i = 0; i < files.length; i++) {
                    if (files[i].size > 10485760) {
                        sizeExceeded = true;
                        break;
                    }
                }

                if (files.length && !sizeExceeded)
                    return true;
                else
                    return false;
            } else if ($('#verify-cntlr-and-bus-with-doc').is(':visible')) {
                let cntlrDocumentType = $('#cntlr-doc-type'),
                    cntlrFiles = $('#controller-doc')[0].files,
                    busFiles = $('#business-docs')[0].files,
                    sizeExceeded = false;

                for (let i = 0; i < busFiles.length; i++) {
                    if (busFiles[i].size > 10485760) {
                        sizeExceeded = true;
                        break;
                    }
                }

                if (cntlrDocumentType.val() != '' && cntlrFiles.length == 1 && cntlrFiles[0].size <= 10485760 && busFiles.length && !sizeExceeded)
                    return true;
                else
                    return false;
            }
        }
    });
</script>
@endsection