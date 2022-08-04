@extends('layouts.whitelabel')
@section('content')

<section class="full-page bg-color fullpage cf customer-documents">
    <div class="full-pg-content-container top-padding cf">
        <div class="wrapper cf">

            <!-- Add A New Member -->
            <div class="community-tab-content-wrapper active cf">

                <div class="breadcrumb-wrapper cf">
                    <ul class="breadcrumbs">
                        <li><a href="{{ route('wl-settings') }}" title="Settings">Settings</a></li>
                        <li><a href="{{ route('wl-payment-settings') }}" title="Payment Settings">Payment Settings</a></li>
                        <li class="active-page">Customer Documents</li>
                    </ul>
                </div>

                <div class="r-side">
                    <div class="btn primary">
                        <a href="{{ route('wl-upload-cust-docs')}}" title="Upload Documents">Upload Documents</a>
                    </div>
                </div>

                <h1 class="color h1-border">Customer Documents</h1>
                    
                <table class="customer-docs-table">
                    <thead class="thead">
                        <tr>
                            <th class="col-1">Document Type</th>
                            <th class="col-2">File Name</th>
                            <th class="col-3">Mime Type</th>
                            <th class="col-4">Status</th>
                            <th class="col-5">Failure Reason</th>
                            <th class="col-6">Upload Date</th>
                        </tr>
                    </thead>
                    <tbody class="tbody">
                        @foreach ($customerDocs as $customerDoc)
                        <tr>
                            <td class="col-1">{{ $customerDoc->documentType }}</td>
                            <td class="col-2">{{ $customerDoc->fileName }}</td>
                            <td class="col-3">{{ $customerDoc->mimeType }}</td>
                            <td class="col-4">{{ $customerDoc->status or '' }}</td>
                            <td class="col-5">{{ $customerDoc->failureReason or '' }}</td>
                            @if (!empty(Auth::user()->sponsorTimezone))
                            <td class="col-6">{{ Carbon\Carbon::parse($customerDoc->created_at)->setTimezone(Auth::user()->sponsorTimezone)->format('n/d/Y g:i A') }}</td>
                            @else
                            <td class="col-6">{{ Carbon\Carbon::parse($customerDoc->created_at)->format('F j, Y') }}</td>
                            @endif
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                <p>The following is an explanation of the Failure Reason codes:</p>
                <ul>
                    <li><strong>ScanNotReadable</strong> - The photo was blurry, parts of the image were cut off, or the photo had glares on it preventing information from being read.</li>
                    <li><strong>ScanNotUploaded</strong> - A photo was uploaded, but it was not an ID.</li>
                    <li><strong>ScanIdTypeNotSupported</strong> - An ID was uploaded, but it is not a form of ID that is accepted.</li>
                    <li><strong>ScanNameMismatch</strong> - The name on the ID does not match the name on the account.</li>
                    <li><strong>ScanDobMismatch</strong> - The date of birth on the ID does not match the date of birth on the account.</li>
                </ul>
            </div>
        </div>
    </div>
</section>

@endsection