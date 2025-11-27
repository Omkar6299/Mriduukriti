@extends('admin_panel.layouts.app')
@section('title', 'Payment Transactions')
@section('content')
    <style>
        .form-control {
            border: 1px solid #00000021 !important;
            border-radius: 8px;
        }

        .page-link.active,
        .active>.page-link {
            z-index: 3;
            color: var(--bs-pagination-active-color) !important;
            background-color: var(--bs-pagination-active-bg);
            border-color: var(--bs-pagination-active-border-color);
        }
    </style>
    <div class="container">
        <div class="row">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3 class="mb-0 h4 font-weight-bolder">@yield('title')</h3>
            </div>

            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-body p-3">
                        <div class="table-responsive">
                            <table class="table  align-middle mb-0" id="dataTables">
                                <thead class="bg-gradient-primary ">
                                    <tr>
                                        <th class="text-white">#</th>
                                        <th class="text-white">Order</th>
                                        <th class="text-white">Merchant Txn ID</th>
                                        <th class="text-white">Payment Mode</th>
                                        <th class="text-white">Amount</th>
                                        <th class="text-white">Status</th>
                                        <th class="text-white">Transaction Date</th>
                                        <th class="text-white">Completed At</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($transactions as $transaction)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>
                                                <div class="fw-semibold">
                                                    {{ optional($transaction->order)->order_number ?? 'N/A' }}
                                                </div>
                                                <small class="text-muted">
                                                    {{ optional(optional($transaction->order)->user)->name ?? 'Guest User' }}
                                                </small>
                                            </td>
                                            <td>
                                                <span class="fw-semibold">{{ $transaction->merchant_transaction_id }}</span>
                                                @if ($transaction->ntt_data_transaction_id)
                                                    <div class="small text-muted">NTT: {{ $transaction->ntt_data_transaction_id }}</div>
                                                @endif
                                                @if ($transaction->bank_transaction_id)
                                                    <div class="small text-muted">Bank: {{ $transaction->bank_transaction_id }}</div>
                                                @endif
                                            </td>
                                            <td>{{ $transaction->payment_mode ?? 'N/A' }}</td>
                                            <td>₹ {{ number_format($transaction->amount, 2) }}</td>
                                            <td>
                                                @php
                                                    $statusColors = [
                                                        'success' => 'bg-success',
                                                        'pending' => 'bg-warning text-dark',
                                                        'initiated' => 'bg-info text-dark',
                                                        'failed' => 'bg-danger',
                                                        'cancelled' => 'bg-secondary',
                                                    ];
                                                    $statusClass = $statusColors[$transaction->status] ?? 'bg-dark';
                                                @endphp
                                                <span class="badge {{ $statusClass }}">{{ ucfirst($transaction->status) }}</span>
                                            </td>
                                            <td>{{ optional($transaction->transaction_date)->format('d M Y, h:i A') ?? '-' }}</td>
                                            <td>{{ optional($transaction->transaction_completed_at)->format('d M Y, h:i A') ?? '-' }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center text-muted py-5">
                                                No payment transactions found.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

