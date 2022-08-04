@extends('layouts.whitelabel')
@section('content')

<section class="full-page bg-color fullpage cf transactions">
    <div class="full-pg-content-container top-padding cf">
        <div class="wrapper cf">

            <div class="community-tab-content-wrapper active cf">

                <div class="breadcrumb-wrapper cf">
                    <ul class="breadcrumbs">
                        <li><a href="{{ route('wl-settings') }}" title="Settings">Settings</a></li>
                        <li><a href="{{ route('wl-payment-settings') }}" title="Payment Settings">Payment Settings</a></li>
                        <li class="active-page">Transactions</li>
                    </ul>
                </div>

                <h1 class="color h1-border">Transactions</h1>
                
                <p>
                    When the Cancel Transfer icon appears under the Options column, the transfer is eligible for cancellation. A transfer is cancellable up until 4pm CT on the 
                    same business day if the transfer was initiated prior to 4PM CT. If a transfer was initiated after 4pm CT, it can be cancelled anytime before 4pm CT on the 
                    following business day.
                </p>

                @if ($transactions->count())
                <div class="search-wrapper cf">
                    <input id="search-term" placeholder="First Name or Last Name or Full Name" value="@if(!empty(request()->query('search'))){{request()->query('search')}}@endif">
                    <div class="search-icon rfloat" title="Search"></div>
                </div>
                @endif

                <table class="transactions-table">
                    <thead class="thead">
                        <tr>
                            <th class="col-1">Investor</th>
                            <th class="col-2">Project Name</th>
                            <th class="col-3">Amount</th>
                            <th class="col-4">Status</th>
                            <th class="col-5">ACH ID</th>
                            <th class="col-6">Transfer Date</th>
                            <th class="col-7">Options</th>
                        </tr>
                    </thead>
                    <tbody class="tbody">
                        @foreach ($transactions as $transaction)
                        <tr>
                            <td class="col-1">{{ $transaction->subscriberFullName }}</td>
                            <td class="col-2">{{ $transaction->projectName }}</td>
                            <td class="col-3">-${{ $transaction->amount }}</td>
                            <td class="col-4">{{ $transaction->status or 'pending' }}</td>
                            <td class="col-5">{{ $transaction->individualAchId or '' }}</td>
                            @if (!empty(Auth::user()->sponsorTimezone))
                            <td class="col-6">{{ Carbon\Carbon::parse($transaction->created_at)->setTimezone(Auth::user()->sponsorTimezone)->format('n/d/Y g:i A') }}</td>
                            @else
                            <td class="col-6">{{ Carbon\Carbon::parse($transaction->created_at)->format('F j, Y') }}</td>
                            @endif
                            <td class="col-7">
                                @if (!empty($transaction->cancelLink) && $transaction->cancelLink)
                                <div class="cancel-icon-wrapper">
                                    <a href="{{ route('wl-cancel-transfer', $transaction->transferId) }}" class="cancel-button" title="Cancel Transfer">cancel</a>
                                </div>
                                @elseif (!empty($transaction->failureLink) && $transaction->failureLink)
                                <div class="failure-icon-wrapper">
                                    <a href="javascript:void(0)" data-xfer-id="{{ $transaction->transferId }}" class="failure-icon" title="Failure Reason">failure</a>
                                </div>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                {{ $transactions->appends(request()->only(['search']))->links() }}
            </div>
        </div>
    </div>
    <div id="failure-reason-dlg" title="Transfer Failure Reason">
        <table class="transfer-failure-table">
            <tbody>
                <tr>
                    <th>Code</th>
                    <td class="xfer-failure-code"></td>
                </tr>
                <tr>
                    <th>Description</th>
                    <td class="xfer-failure-desc"></td>
                </tr>
                <tr>
                    <th>Explanation</th>
                    <td class="xfer-failure-explanation"></td>
                </tr>
            </tbody>
        </table>
    </div>
</section>
<script>
    $(document).ready(function() {
        let searchTerm = '';

        $('#failure-reason-dlg').dialog({
            autoOpen: false,
            modal: true,
            closeOnEscape: false,
            resizable: false,
            draggable: false,
            width: 600
        });

        $('a.failure-icon').click(function() {
            let dlg = $('#failure-reason-dlg'),
                transferId = $(this).attr('data-xfer-id');

            $.ajax({
                url: '/whitelabel/settings/payment-settings/transfer-failure',
                type: 'POST',
                data: {
                    transfer_id: transferId
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            }).done(function(data) {
                let xfer_failure = JSON.parse(data);

                $('#failure-reason-dlg .xfer-failure-code').text(xfer_failure.code);
                $('#failure-reason-dlg .xfer-failure-desc').text(xfer_failure.description);
                $('#failure-reason-dlg .xfer-failure-explanation').text(xfer_failure.explanation);

                if (!dlg.dialog('isOpen')) {
                    dlg.dialog('open');
                }
            }).fail(function(jqXHR, textStatus) {
                console.log('Request failed: ' + textStatus);
            });
        });

        $('.search-icon').click(function() {
            if ((searchTerm = $('#search-term').val()) != '')
                location = location.protocol + '//' + location.hostname + '/whitelabel/settings/payment-settings/transactions?search=' + encodeURI(searchTerm.replace(/^\s+|\s+$/g, '').replace(/\s+/g, ' '));
            else
                location = location.protocol + '//' + location.hostname + '/whitelabel/settings/payment-settings/transactions';
        });
    });
</script>

@endsection