@extends('admin.layout.app')

@section('title', 'Edit Invoice')

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Edit Invoice</h4>
            <p class="text-muted mb-0">
                {{ $invoice->invoice_number }}
            </p>
        </div>

        <div class="d-flex gap-2">
            <a
            href="{{ route('admin.invoices.show', $invoice->id) }}"
            class="btn btn-outline-primary"
            >
            <i class="bi bi-eye"></i> View
        </a>

        <a
        href="{{ route('admin.invoices.index') }}"
        class="btn btn-light"
        >
        <i class="bi bi-arrow-left"></i> Back
    </a>
</div>
</div>

@if($errors->any())
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <ul class="mb-0">
        @foreach($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>

    <button
    type="button"
    class="btn-close"
    data-bs-dismiss="alert"
    ></button>
</div>
@endif

<form
method="POST"
action="{{ route('admin.invoices.update', $invoice->id) }}"
id="invoiceForm"
>
@csrf
@method('PUT')

<div class="card mb-4">

    <div class="card-header">
        <h5 class="mb-0">Invoice Information</h5>
    </div>

    <div class="card-body">

        <div class="row g-3">

            <div class="col-md-6">
                <label class="form-label">
                    Client <span class="text-danger">*</span>
                </label>

                <select
                name="client_id"
                class="form-select @error('client_id') is-invalid @enderror"
                required
                >
                <option value="">Select Client</option>

                @foreach($clients as $client)
                <option
                value="{{ $client->id }}"
                {{ (string) old('client_id', $invoice->client_id) === (string) $client->id ? 'selected' : '' }}
                >
                {{ $client->name }}
                @if($client->company_name)
                - {{ $client->company_name }}
                @endif
            </option>
            @endforeach
        </select>

        @error('client_id')
        <div class="invalid-feedback">
            {{ $message }}
        </div>
        @enderror
    </div>

    <div class="col-md-3">
        <label class="form-label">
            Invoice Date <span class="text-danger">*</span>
        </label>

        <input
        type="date"
        name="invoice_date"
        class="form-control @error('invoice_date') is-invalid @enderror"
        value="{{ old('invoice_date', optional($invoice->invoice_date)->format('Y-m-d')) }}"
        required
        >

        @error('invoice_date')
        <div class="invalid-feedback">
            {{ $message }}
        </div>
        @enderror
    </div>

    <div class="col-md-3">
        <label class="form-label">
            Due Date
        </label>

        <input
        type="date"
        name="due_date"
        class="form-control @error('due_date') is-invalid @enderror"
        value="{{ old('due_date', optional($invoice->due_date)->format('Y-m-d')) }}"
        >

        @error('due_date')
        <div class="invalid-feedback">
            {{ $message }}
        </div>
        @enderror
    </div>

    <div class="col-md-8">
        <label class="form-label">
            Subject
        </label>

        <input
        type="text"
        name="subject"
        class="form-control @error('subject') is-invalid @enderror"
        value="{{ old('subject', $invoice->subject) }}"
        placeholder="Invoice subject"
        >

        @error('subject')
        <div class="invalid-feedback">
            {{ $message }}
        </div>
        @enderror
    </div>

    <div class="col-md-4">
        <label class="form-label">
            Status <span class="text-danger">*</span>
        </label>

        <select
        name="status"
        class="form-select @error('status') is-invalid @enderror"
        required
        >
        @foreach($statuses as $status)
        <option
        value="{{ $status }}"
        {{ old('status', $invoice->status) === $status ? 'selected' : '' }}
        >
        {{ $status }}
    </option>
    @endforeach
</select>

@error('status')
<div class="invalid-feedback">
    {{ $message }}
</div>
@enderror
</div>

</div>

</div>
</div>

<div class="card mb-4">

    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Invoice Items</h5>

        <button
        type="button"
        class="btn btn-sm btn-primary"
        id="addItem"
        >
        <i class="bi bi-plus-lg"></i> Add Item
    </button>
</div>

<div class="card-body">

    <div class="table-responsive">

        <table class="table table-bordered align-middle">

            <thead class="table-light">
                <tr>
                    <th style="width:20%">Item</th>
                    <th style="width:25%">Description</th>
                    <th style="width:12%">Quantity</th>
                    <th style="width:15%">Rate</th>
                    <th style="width:15%">Amount</th>
                    <th style="width:8%">Action</th>
                </tr>
            </thead>

            <tbody id="itemsBody">

                @php
                $oldItems = old('items');

                if ($oldItems === null) {
                    $oldItems = $invoice->items->map(function ($item) {
                        return [
                        'item_name' => $item->item_name,
                        'description' => $item->description,
                        'quantity' => $item->quantity,
                        'rate' => $item->rate,
                        ];
                    })->toArray();
                }
                @endphp

                @foreach($oldItems as $index => $item)

                <tr class="item-row">

                    <td>
                        <input
                        type="text"
                        name="items[{{ $index }}][item_name]"
                        class="form-control item-name"
                        value="{{ $item['item_name'] ?? '' }}"
                        placeholder="Item name"
                        required
                        >
                    </td>

                    <td>
                        <input
                        type="text"
                        name="items[{{ $index }}][description]"
                        class="form-control"
                        value="{{ $item['description'] ?? '' }}"
                        placeholder="Description"
                        >
                    </td>

                    <td>
                        <input
                        type="number"
                        name="items[{{ $index }}][quantity]"
                        class="form-control quantity"
                        value="{{ $item['quantity'] ?? 1 }}"
                        min="0.01"
                        step="0.01"
                        required
                        >
                    </td>

                    <td>
                        <input
                        type="number"
                        name="items[{{ $index }}][rate]"
                        class="form-control rate"
                        value="{{ $item['rate'] ?? 0 }}"
                        min="0"
                        step="0.01"
                        required
                        >
                    </td>

                    <td>
                        <input
                        type="text"
                        class="form-control amount"
                        value="0.00"
                        readonly
                        >
                    </td>

                    <td class="text-center">
                        <button
                        type="button"
                        class="btn btn-sm btn-outline-danger remove-item"
                        >
                        <i class="bi bi-trash"></i>
                    </button>
                </td>

            </tr>

            @endforeach

        </tbody>

    </table>

</div>

</div>
</div>

<div class="row">

    <div class="col-lg-7">

        <div class="card mb-4">

            <div class="card-header">
                <h5 class="mb-0">Additional Information</h5>
            </div>

            <div class="card-body">

                <div class="mb-3">
                    <label class="form-label">
                        Notes
                    </label>

                    <textarea
                    name="notes"
                    rows="5"
                    class="form-control"
                    placeholder="Invoice notes"
                    >{{ old('notes', $invoice->notes) }}</textarea>
                </div>

                <div>
                    <label class="form-label">
                        Terms & Conditions
                    </label>

                    <textarea
                    name="terms"
                    rows="5"
                    class="form-control"
                    placeholder="Terms and conditions"
                    >{{ old('terms', $invoice->terms) }}</textarea>
                </div>

            </div>

        </div>

    </div>

    <div class="col-lg-5">

        <div class="card mb-4">

            <div class="card-header">
                <h5 class="mb-0">Invoice Summary</h5>
            </div>

            <div class="card-body">

                <div class="row align-items-center mb-3">

                    <div class="col-6">
                        <label class="form-label mb-0">
                            Subtotal
                        </label>
                    </div>

                    <div class="col-6">
                        <input
                        type="text"
                        id="subtotal"
                        class="form-control text-end"
                        value="0.00"
                        readonly
                        >
                    </div>

                </div>

                <div class="row align-items-center mb-3">

                    <div class="col-5">
                        <label class="form-label mb-0">
                            Discount
                        </label>
                    </div>

                    <div class="col-3">
                        <select
                        name="discount_type"
                        id="discountType"
                        class="form-select"
                        >
                        <option
                        value="fixed"
                        {{ old('discount_type', $invoice->discount_type) === 'fixed' ? 'selected' : '' }}
                        >
                        Fixed
                    </option>

                    <option
                    value="percentage"
                    {{ old('discount_type', $invoice->discount_type) === 'percentage' ? 'selected' : '' }}
                    >
                    %
                </option>
            </select>
        </div>

        <div class="col-4">
            <input
            type="number"
            name="discount_value"
            id="discountValue"
            class="form-control text-end"
            value="{{ old('discount_value', $invoice->discount_value) }}"
            min="0"
            step="0.01"
            >
        </div>

    </div>

    <div class="row align-items-center mb-3">

        <div class="col-6">
            <label class="form-label mb-0">
                Discount Amount
            </label>
        </div>

        <div class="col-6">
            <input
            type="text"
            id="discountAmount"
            class="form-control text-end"
            value="0.00"
            readonly
            >
        </div>

    </div>

    <div class="row align-items-center mb-3">

        <div class="col-6">
            <label class="form-label mb-0">
                Tax
            </label>
        </div>

        <div class="col-6">
            <input
            type="number"
            name="tax"
            id="tax"
            class="form-control text-end"
            value="{{ old('tax', $invoice->tax) }}"
            min="0"
            step="0.01"
            >
        </div>

    </div>

    <hr>

    <div class="row align-items-center">

        <div class="col-6">
            <h5 class="mb-0">Total</h5>
        </div>

        <div class="col-6">
            <input
            type="text"
            id="total"
            class="form-control text-end fw-bold"
            value="0.00"
            readonly
            >
        </div>

    </div>

</div>

</div>

<div class="d-flex justify-content-end gap-2 mb-4">

    <a
    href="{{ route('admin.invoices.index') }}"
    class="btn btn-light"
    >
    Cancel
</a>

<button
type="submit"
class="btn btn-primary"
>
<i class="bi bi-check-lg"></i>
Update Invoice
</button>

</div>

</div>

</div>

</form>

</div>

@endsection

@push('scripts')

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const itemsBody = document.getElementById('itemsBody');
        const addItemButton = document.getElementById('addItem');
        const subtotalInput = document.getElementById('subtotal');
        const discountType = document.getElementById('discountType');
        const discountValue = document.getElementById('discountValue');
        const discountAmount = document.getElementById('discountAmount');
        const taxInput = document.getElementById('tax');
        const totalInput = document.getElementById('total');

        let itemIndex = itemsBody.querySelectorAll('.item-row').length;

        function numberValue(value) {
            const number = parseFloat(value);

            if (isNaN(number) || number < 0) {
                return 0;
            }

            return number;
        }

        function calculateRow(row) {
            const quantity = numberValue(
                row.querySelector('.quantity').value
                );

            const rate = numberValue(
                row.querySelector('.rate').value
                );

            const amount = quantity * rate;

            row.querySelector('.amount').value = amount.toFixed(2);

            return amount;
        }

        function calculateInvoice() {
            let subtotal = 0;

            itemsBody.querySelectorAll('.item-row').forEach(function (row) {
                subtotal += calculateRow(row);
            });

            subtotal = Math.round((subtotal + Number.EPSILON) * 100) / 100;

            let discount = numberValue(discountValue.value);

            if (discountType.value === 'percentage') {
                if (discount > 100) {
                    discount = 100;
                    discountValue.value = 100;
                }

                discount = subtotal * discount / 100;
            }

            discount = Math.min(discount, subtotal);

            const tax = numberValue(taxInput.value);

            const total = Math.max(
                0,
                subtotal - discount + tax
                );

            subtotalInput.value = subtotal.toFixed(2);
            discountAmount.value = discount.toFixed(2);
            totalInput.value = total.toFixed(2);
        }

        function createItemRow(index) {
            const row = document.createElement('tr');

            row.className = 'item-row';

            row.innerHTML = `
            <td>
                <input
                    type="text"
                    name="items[${index}][item_name]"
                    class="form-control item-name"
                    placeholder="Item name"
                    required
                >
            </td>

            <td>
                <input
                    type="text"
                    name="items[${index}][description]"
                    class="form-control"
                    placeholder="Description"
                >
            </td>

            <td>
                <input
                    type="number"
                    name="items[${index}][quantity]"
                    class="form-control quantity"
                    value="1"
                    min="0.01"
                    step="0.01"
                    required
                >
            </td>

            <td>
                <input
                    type="number"
                    name="items[${index}][rate]"
                    class="form-control rate"
                    value="0"
                    min="0"
                    step="0.01"
                    required
                >
            </td>

            <td>
                <input
                    type="text"
                    class="form-control amount"
                    value="0.00"
                    readonly
                >
            </td>

            <td class="text-center">
                <button
                    type="button"
                    class="btn btn-sm btn-outline-danger remove-item"
                >
                    <i class="bi bi-trash"></i>
                </button>
            </td>
            `;

            return row;
        }

        addItemButton.addEventListener('click', function () {
            const row = createItemRow(itemIndex);

            itemsBody.appendChild(row);

            itemIndex++;

            calculateInvoice();
        });

        itemsBody.addEventListener('input', function (event) {
            if (
                event.target.classList.contains('quantity') ||
                event.target.classList.contains('rate')
                ) {
                calculateInvoice();
        }
    });

        itemsBody.addEventListener('click', function (event) {
            const button = event.target.closest('.remove-item');

            if (!button) {
                return;
            }

            const rows = itemsBody.querySelectorAll('.item-row');

            if (rows.length <= 1) {
                return;
            }

            button.closest('.item-row').remove();

            calculateInvoice();
        });

        discountType.addEventListener('change', calculateInvoice);
        discountValue.addEventListener('input', calculateInvoice);
        taxInput.addEventListener('input', calculateInvoice);

        calculateInvoice();
    });
</script>

@endpush