@extends('layouts.investor')
@section('content')

<section class="full-page bg-color fullpage cf transactions">
    <div class="full-pg-content-container top-padding cf">
        <div class="wrapper cf">

            <!-- Add A New Member -->
            <div class="community-tab-content-wrapper active cf">

                <h1 class="color h1-border">Transactions</h1>

                <table class="transactions-table">
                    <thead class="thead">
                        <tr>
                            <th class="col-1">Project Name</th>
                            <th class="col-2">Amount</th>
                            <th class="col-3">Status</th>
                            <th class="col-4">Transfer Date</th>
                        </tr>
                    </thead>
                    <tbody class="tbody">
                        @foreach ($transactions as $transaction)
                        <tr>
                            <td class="col-1">{{ $transaction->projectName }}</td>
                            <td class="col-2">${{ $transaction->amount }}</td>
                            <td class="col-3">{{ $transaction->status or '' }}</td>
                            @if (!empty(Auth::user()->subscriberTimezone))
                            <td class="col-4">{{ Carbon\Carbon::parse($transaction->created_at)->setTimezone(Auth::user()->subscriberTimezone)->format('n/d/Y g:i A') }}</td>
                            @else
                            <td class="col-4">{{ Carbon\Carbon::parse($transaction->created_at)->format('F j, Y') }}</td>
                            @endif
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                {{ $transactions->links() }}
            </div>
        </div>
    </div>
</section>

@endsection