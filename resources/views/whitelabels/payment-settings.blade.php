@extends('layouts.whitelabel')
@section('content')

<section class="full-page bg-color fullpage cf payment-settings">
    <div class="full-pg-content-container top-padding cf">
        <div class="wrapper cf">

            <!-- Add A New Member -->
            <div class="community-tab-content-wrapper active cf">

                <div class="breadcrumb-wrapper cf">
                    <ul class="breadcrumbs">
                        <li><a href="{{ route('wl-settings') }}" title="Settings">Settings</a></li>
                        <li class="active-page">Payment Settings</li>
                    </ul>
                </div>

                <h1 class="color h1-border">Payment Settings</h1>
                    
                <span class="customer-status">Customer Status: {{ $custInfo->status or '' }}</span>

                @if (!empty($custInfo->status) && ($custInfo->status == 'deactivated' || $custInfo->status == 'suspended'))
                <p>Please contact <a href="mailto:technicalsupport@evesttech.com">eVest Technology, LLC</a> for assistance.</p>
                @endif

                @if (!Auth::user()->acceptDwollaTOS && !Auth::user()->acceptDwollaPP && empty(Auth::user()->dwollaLocationResource))
                <p>The first step is to read and accept the Dwolla Terms of Service and Privacy Policy and then create a customer.</p>
                <a id="createCustLink" href="{{ route('wl-create-cust') }}" class="underline" title="Create Customer">Create Customer</a>
                @endif

                @if (!empty($custInfo->status) && !empty($custInfo->_links->{'retry-verification'}))
                <p>
                    A retry status indicates that some information may have been miskeyed during the initial Customer creation. You have one more opportunity to correct any 
                    mistakes. All fields that were required in the initial Customer creation attempt will be required in the retry attempt, along with the full 9-digit SSN.
                </p>
                <a id="retryCustLink" href="{{ route('wl-retry-cust') }}" class="underline" title="Create Customer (retry)">Create Customer (retry)</a>
                @endif

                @if (!empty($custInfo->status) && !empty($custInfo->_links->edit) && $custInfo->status != 'retry' && $custInfo->status != 'deactivated')
                <p>A limited set of information can be updated on an existing created business verified Customer.</p>
                <a id="updateCustLink" href="{{ route('wl-update-cust') }}" class="underline" title="Edit Customer">Edit Customer</a>
                @endif

                @if (!empty($custInfo->status) && !empty($custInfo->_links->{'document-form'}))
                <p>
                    A document status indicates that additional identifying documents need to be uploaded for manual review in order for the controller, the business, or both 
                    the controller and business to be verified. The document(s) will then be reviewed by Dwolla and may take up to 1-2 business days to be approved or rejected.
                </p>
                <a id="customerDocsLink" href="{{ route('wl-customer-documents') }}" class="underline" title="Customer Documents">Customer Documents</a>
                @endif

                @if (!empty($custInfo->status) && !empty($custInfo->_links->{'beneficial-owners'}) && (Auth::user()->dwollaBusinessType == 4 || 
                Auth::user()->dwollaBusinessType == 7 || Auth::user()->dwollaBusinessType == 8) && $custInfo->status != 'deactivated' && $custInfo->status != 'suspended')
                <p>
                    The {{ Auth::user()->wlDba }} business structure ({{ $businessTypeName or '' }}) requires the addition of beneficial owners (if applicable) and also certifying 
                    beneficial ownership.
                </p>
                <a id="beneficialOwnersLink" href="{{ route('wl-beneficial-owners') }}" class="underline" title="Beneficial Owners">Beneficial Owners</a>
                @endif

                @if (!empty($custInfo->status) && !empty($custInfo->_links->{'funding-sources'}) && $custInfo->status != 'deactivated')
                <p>In order to send funds to another customer, one or more funding sources must be added.</p>
                <a id="fundingSourcesLink" href="{{ route('wl-funding-sources') }}" class="underline" title="Funding Sources">Funding Sources</a>
                @endif

                @if (!empty($custInfo->status) && !empty($custInfo->_links->send) && empty($custInfo->_links->{'verify-beneficial-owners'}) && empty($custInfo->_links->{'certify-beneficial-ownership'}) && $custInfo->status == 'verified')
                <p>Send funds to an investor.</p>
                <a id="transferFundsLink" href="{{ route('wl-transfer-funds') }}" class="underline" title="Transfer Funds">Transfer Funds</a>

                <p>View transactions.</p>
                <a id="transactionsLink" href="{{ route('wl-transactions') }}" class="underline" title="Transactions">Transactions</a>
                @endif
            </div>
        </div>
    </div>
</section>

@endsection