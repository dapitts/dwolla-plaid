@extends('layouts.whitelabel')
@section('content')

<section class="full-page bg-color fullpage cf funding-sources">
    <div class="full-pg-content-container top-padding cf">
        <div class="wrapper cf">

            <!-- Add A New Member -->
            <div class="community-tab-content-wrapper active cf">

                <div class="breadcrumb-wrapper cf">
                    <ul class="breadcrumbs">
                        <li><a href="{{ route('wl-settings') }}" title="Settings">Settings</a></li>
                        <li><a href="{{ route('wl-payment-settings') }}" title="Payment Settings">Payment Settings</a></li>
                        <li class="active-page">Funding Sources</li>
                    </ul>
                </div>

                <h1 class="color h1-border">Funding Sources</h1>

                @if ($bankAccts->count())
                <p>
                    To create a funding source from a bank account (added via Plaid), click the "Create Funding Source" icon under the Options column. To remove a bank account 
                    (added via Plaid) as a funding source, click the "Remove Funding Source" icon under the Options column. Customers can have a maximum of 2 active funding sources.
                </p>

                <table class="funding-sources-table">
                    <thead class="thead">
                        <tr>
                            <th class="col-1">Account Name</th>
                            <th class="col-2">Bank Name</th>
                            <th class="col-3">Account Type</th>
                            <th class="col-4">Options</th>
                        </tr>
                    </thead>
                    <tbody class="tbody">
                        @foreach ($bankAccts as $bankAcct)
                        <tr>
                            <td class="col-1">{{ $bankAcct->accountName }}</td>
                            <td class="col-2">{{ $bankAcct->bankName }}</td>
                            <td class="col-3">{{ $bankAcct->accountType }}</td>
                            <td class="col-4">
                                <div class="edit-icon-wrapper">
                                    @if ($bankAcct->add_bank_acct_method == 1)
                                    @if (empty($bankAcct->funding_source_url))
                                    <a href="{{ route('wl-create-funding-src', $bankAcct->bankAccountId) }}" class="funding-add-btn" title="Create Funding Source"></a>
                                    @elseif (!empty($bankAcct->funding_source_url))
                                    <a href="{{ route('wl-remove-funding-src', $bankAcct->bankAccountId) }}" class="funding-remove-btn" title="Remove Funding Source"></a>
                                    @endif
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @else
                <span class="no-sources">No bank accounts added. Please navigate to <a href="{{ route('wl-bank-accounts') }}">Bank Accounts</a> to add a bank account or a funding source.</span>
                @endif

                @if (!empty($dwollaBalance) && $dwollaBalance->balance->value != '0.00' && $dwollaBalance->total->value != '0.00')
                <div id="dwolla-balance-wrapper">
                    <h2>Dwolla Balance</h2>
                    <table class="dwolla-balance-table">
                        <tbody>
                            <tr>
                                <th>Available Balance</th>
                                <td>${{ $dwollaBalance->balance->value }}</td>
                            </tr>
                            <tr>
                                <th>Total Balance</th>
                                <td>${{ $dwollaBalance->total->value }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                @endif
            </div>
        </div>
    </div>
</section>

@endsection