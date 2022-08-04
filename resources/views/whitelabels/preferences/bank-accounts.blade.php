@extends('layouts.whitelabel')
@section('content')

<section class="full-page bg-color fullpage cf bank-accts">
    <div class="full-pg-content-container top-padding cf">
        <div class="wrapper cf">

            <!-- Add A New Member -->
            <div class="community-tab-content-wrapper active cf">

                <div class="breadcrumb-wrapper cf">
                    <ul class="breadcrumbs">
                        <li><a href="{{ route('wl-settings') }}" title="Settings">Settings</a></li>
                        <li class="active-page">Bank Accounts</li>
                    </ul>
                </div>

                <div class="r-side">
                    <div class="btn primary">
                        <a href="{{ route('wl-add-bank-acct')}}" title="Add Bank Account">Add Bank Account</a>
                    </div>
                    <div class="btn primary">
                        <a href="{{ route('wl-add-funding-src')}}" title="Add Funding Source">Add Funding Source</a>
                    </div>
                    <div class="btn primary">
                        <a href="{{ route('wl-add-bank-acct-manual')}}" title="Add Bank Account (manual)">Add Manual</a>
                    </div>
                </div>

                <h1 class="color h1-border">Bank Accounts</h1>
                    
                <table class="bank-accts-table">
                    <thead class="thead">
                        <tr>
                            <th class="col-1">Account Name</th>
                            <th class="col-2">Bank Name</th>
                            <th class="col-3">Account Type</th>
                            <th class="col-4" colspan="2">Options</th>
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
                                    <a href="{{ route('wl-edit-bank-acct', $bankAcct->bankAccountId) }}" class="edit-btn" title="Edit Bank Account">edit</a>
                                    @elseif ($bankAcct->add_bank_acct_method == 2)
                                    <a href="{{ route('wl-edit-funding-src', $bankAcct->bankAccountId) }}" class="edit-btn" title="Edit Funding Source">edit</a>
                                    @elseif ($bankAcct->add_bank_acct_method == 3)
                                    <a href="{{ route('wl-edit-bank-acct-manual', $bankAcct->bankAccountId) }}" class="edit-btn" title="Edit Bank Account (manual)">edit</a>
                                    @endif
                                </div>
                            </td>
                            <td class="col-5">
                                <div class="delete-icon-wrapper">
                                    @if ($bankAcct->add_bank_acct_method == 1)
                                    <a href="{{ route('wl-delete-bank-acct', $bankAcct->bankAccountId) }}" class="delete" title="Delete Bank Account">delete</a>
                                    @elseif ($bankAcct->add_bank_acct_method == 2)
                                    <a href="{{ route('wl-delete-funding-src', $bankAcct->bankAccountId) }}" class="delete" title="Delete Funding Source">delete</a>
                                    @elseif ($bankAcct->add_bank_acct_method == 3)
                                    <a href="{{ route('wl-delete-bank-acct-manual', $bankAcct->bankAccountId) }}" class="delete" title="Delete Bank Account (manual)">delete</a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

@endsection