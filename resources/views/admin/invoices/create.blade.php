@extends('admin.layout.app')

@section('title', 'Create Invoice')

@section('content')

<div class="container-fluid">

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
        <div>
            <h4 class="mb-1">Create Invoice</h4>
            <p class="text-muted mb-0">Create a new invoice for your client.</p>
        </div>

        <a href="{{ route('admin.invoices.index') }}" class="btn btn-light border">
            <i class="bi bi-arrow-left me-1"></i>
            Back
        </a>
    </div>

    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">

        <div class="fw-semibold mb-2">
            <i class="bi bi-exclamation-triangle me-1"></i>
            Please fix the following errors:
        </div>

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
    action="{{ route('admin.invoices.store') }}"
    id="invoiceForm"
    >

    @csrf

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
                    {{ (string) old('client_id') === (string) $client->id ? 'selected' : '' }}
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
            id="invoiceDate"
            class="form-control @error('invoice_date') is-invalid @enderror"
            value="{{ old('invoice_date', now()->format('Y-m-d')) }}"
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
            id="dueDate"
            class="form-control @error('due_date') is-invalid @enderror"
            value="{{ old('due_date') }}"
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
            maxlength="255"
            class="form-control @error('subject') is-invalid @enderror"
            value="{{ old('subject') }}"
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
            {{ old('status', 'Draft') === $status ? 'selected' : '' }}
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

    <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">

        <div>
            <h5 class="mb-0">Invoice Items</h5>
            <small class="text-muted">
                Add products or services included in this invoice.
            </small>
        </div>

        <button
        type="button"
        class="btn btn-sm btn-primary"
        id="addItem"
        >
        <i class="bi bi-plus-lg me-1"></i>
        Add Item
    </button>

</div>

<div class="card-body">

    <div class="table-responsive">

        <table class="table table-bordered align-middle mb-0" id="itemsTable">

            <thead class="table-light">

                <tr>
                    <th style="min-width:220px;">Item</th>
                    <th style="min-width:260px;">Description</th>
                    <th style="width:140px;">Quantity</th>
                    <th style="width:170px;">Rate</th>
                    <th style="width:180px;">Amount</th>
                    <th style="width:70px;">Action</th>
                </tr>

            </thead>

            <tbody id="itemsBody">

                @php
                $oldItems = old('items', [
                [
                'item_name' => '',
                'description' => '',
                'quantity' => 1,
                'rate' => 0
                ]
                ]);
                @endphp

                @foreach($oldItems as $index => $item)

                <tr class="item-row">

                    <td>

                        <input
                        type="text"
                        name="items[{{ $index }}][item_name]"
                        class="form-control item-name"
                        value="{{ $item['item_name'] ?? '' }}"
                        maxlength="255"
                        placeholder="Item name"
                        required
                        >

                    </td>

                    <td>

                        <textarea
                        name="items[{{ $index }}][description]"
                        class="form-control"
                        rows="1"
                        maxlength="1000"
                        placeholder="Description"
                        >{{ $item['description'] ?? '' }}</textarea>

                    </td>

                    <td>

                        <input
                        type="text"
                        name="items[{{ $index }}][quantity]"
                        class="form-control quantity number-input"
                        value="{{ $item['quantity'] ?? 1 }}"
                        inputmode="decimal"
                        autocomplete="off"
                        maxlength="15"
                        required
                        >

                    </td>

                    <td>

                        <input
                        type="text"
                        name="items[{{ $index }}][rate]"
                        class="form-control rate number-input"
                        value="{{ $item['rate'] ?? 0 }}"
                        inputmode="decimal"
                        autocomplete="off"
                        maxlength="15"
                        required
                        >

                    </td>

                    <td>

                        <input
                        type="text"
                        class="form-control amount"
                        value="₹0.00"
                        readonly
                        tabindex="-1"
                        >

                    </td>

                    <td class="text-center">

                        <button
                        type="button"
                        class="btn btn-sm btn-outline-danger remove-item"
                        title="Remove Item"
                        >
                        <i class="bi bi-trash"></i>
                    </button>

                </td>

            </tr>

            @endforeach

        </tbody>

    </table>

</div>

<div
id="itemError"
class="text-danger small mt-2"
></div>

</div>

</div>

<div class="row g-4 mb-4">

    <div class="col-lg-7">

        <div class="card h-100">

            <div class="card-header">
                <h5 class="mb-0">Additional Information</h5>
            </div>

            <div class="card-body">

                <div class="mb-4">

                    <label class="form-label">
                        Notes
                    </label>

                    <textarea
                    name="notes"
                    rows="5"
                    maxlength="5000"
                    class="form-control @error('notes') is-invalid @enderror"
                    placeholder="Invoice notes"
                    >{{ old('notes') }}</textarea>

                    @error('notes')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                    @enderror

                </div>

                <div>

                    <label class="form-label">
                        Terms & Conditions
                    </label>

                    <textarea
                    name="terms"
                    rows="5"
                    maxlength="5000"
                    class="form-control @error('terms') is-invalid @enderror"
                    placeholder="Terms and conditions"
                    >{{ old('terms') }}</textarea>

                    @error('terms')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                    @enderror

                </div>

            </div>

        </div>

    </div>

    <div class="col-lg-5">

        <div class="card h-100">

            <div class="card-header">
                <h5 class="mb-0">Invoice Summary</h5>
            </div>

            <div class="card-body">

                <div class="d-flex justify-content-between mb-3">

                    <span>
                        Subtotal
                    </span>

                    <strong id="subtotalDisplay">
                        ₹0.00
                    </strong>

                </div>

                <div class="row align-items-end mb-3">

                    <div class="col-5">

                        <label class="form-label">
                            Discount Type
                        </label>

                        <select
                        name="discount_type"
                        id="discountType"
                        class="form-select @error('discount_type') is-invalid @enderror"
                        >

                        <option
                        value="fixed"
                        {{ old('discount_type', 'fixed') === 'fixed' ? 'selected' : '' }}
                        >
                        Fixed
                    </option>

                    <option
                    value="percentage"
                    {{ old('discount_type') === 'percentage' ? 'selected' : '' }}
                    >
                    Percentage
                </option>

            </select>

            @error('discount_type')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
            @enderror

        </div>

        <div class="col-7">

            <label class="form-label">
                Discount
            </label>

            <div class="input-group">

                <input
                type="text"
                name="discount_value"
                id="discountValue"
                class="form-control @error('discount_value') is-invalid @enderror"
                value="{{ old('discount_value', '0') }}"
                inputmode="decimal"
                autocomplete="off"
                maxlength="15"
                placeholder="0.00"
                >

                <span
                class="input-group-text"
                id="discountSymbol"
                >
                ₹
            </span>

        </div>

        @error('discount_value')
        <div class="text-danger small mt-1">
            {{ $message }}
        </div>
        @enderror

    </div>

</div>

<div class="d-flex justify-content-between mb-3 text-muted">

    <span>
        Discount Amount
    </span>

    <strong
    id="discountAmountDisplay"
    class="text-danger"
    >
    -₹0.00
</strong>

</div>

<div class="mb-3">

    <label class="form-label">
        Tax
    </label>

    <div class="input-group">

        <input
        type="text"
        name="tax"
        id="tax"
        class="form-control @error('tax') is-invalid @enderror"
        value="{{ old('tax', '0') }}"
        inputmode="decimal"
        autocomplete="off"
        maxlength="6"
        placeholder="0.00"
        >

        <span class="input-group-text">
            %
        </span>

    </div>

    @error('tax')
    <div class="text-danger small mt-1">
        {{ $message }}
    </div>
    @enderror

</div>

<div class="d-flex justify-content-between mb-3 text-muted">

    <span>
        Tax Amount
    </span>

    <strong
    id="taxAmountDisplay"
    class="text-primary"
    >
    +₹0.00
</strong>

</div>

<hr>

<div class="d-flex justify-content-between align-items-center fs-5">

    <span>
        Grand Total
    </span>

    <strong
    id="totalDisplay"
    class="text-success"
    >
    ₹0.00
</strong>

</div>

</div>

</div>

</div>

</div>

<div class="d-flex flex-wrap justify-content-end gap-2 mb-4">

    <a
    href="{{ route('admin.invoices.index') }}"
    class="btn btn-light border"
    >
    Cancel
</a>

<button
type="submit"
class="btn btn-primary"
id="saveInvoiceButton"
>
<i class="bi bi-check-lg me-1"></i>
Save Invoice
</button>

</div>

</form>

</div>

@endsection

@push('scripts')

<script>
    document.addEventListener('DOMContentLoaded', function () {

        const invoiceForm = document.getElementById('invoiceForm');
        const itemsBody = document.getElementById('itemsBody');
        const addItemButton = document.getElementById('addItem');
        const discountType = document.getElementById('discountType');
        const discountValue = document.getElementById('discountValue');
        const discountSymbol = document.getElementById('discountSymbol');
        const taxInput = document.getElementById('tax');
        const subtotalDisplay = document.getElementById('subtotalDisplay');
        const discountAmountDisplay = document.getElementById('discountAmountDisplay');
        const taxAmountDisplay = document.getElementById('taxAmountDisplay');
        const totalDisplay = document.getElementById('totalDisplay');
        const itemError = document.getElementById('itemError');
        const saveInvoiceButton = document.getElementById('saveInvoiceButton');
        const invoiceDate = document.getElementById('invoiceDate');
        const dueDate = document.getElementById('dueDate');

        let itemIndex = itemsBody.querySelectorAll('.item-row').length;
        let submitting = false;

        function money(value) {
            const number = Number(value) || 0;

            return '₹' + number.toLocaleString('en-IN', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }

        function roundMoney(value) {
            return Math.round((value + Number.EPSILON) * 100) / 100;
        }

        function sanitizeNumberInput(input) {

            let value = String(input.value || '');

            value = value.replace(/[^0-9.]/g, '');

            const firstDot = value.indexOf('.');

            if (firstDot !== -1) {

                value =
                value.substring(0, firstDot + 1) +
                value.substring(firstDot + 1).replace(/\./g, '');

            }

            if (value.includes('.')) {

                const parts = value.split('.');

                value =
                parts[0] +
                '.' +
                parts[1].substring(0, 2);

            }

            input.value = value;
        }

        function escapeHtml(value) {

            return String(value ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');

        }

        function createItemRow(index, data = {}) {

            const row = document.createElement('tr');

            row.className = 'item-row';

            row.innerHTML = `
            <td>
                <input
                    type="text"
                    name="items[${index}][item_name]"
                    class="form-control item-name"
                    maxlength="255"
                    value="${escapeHtml(data.item_name || '')}"
                    placeholder="Item name"
                    required
                >
            </td>

            <td>
                <textarea
                    name="items[${index}][description]"
                    class="form-control"
                    rows="1"
                    maxlength="1000"
                    placeholder="Description"
                >${escapeHtml(data.description || '')}</textarea>
            </td>

            <td>
                <input
                    type="text"
                    name="items[${index}][quantity]"
                    class="form-control quantity number-input"
                    inputmode="decimal"
                    autocomplete="off"
                    maxlength="15"
                    value="${escapeHtml(data.quantity ?? '1')}"
                    required
                >
            </td>

            <td>
                <input
                    type="text"
                    name="items[${index}][rate]"
                    class="form-control rate number-input"
                    inputmode="decimal"
                    autocomplete="off"
                    maxlength="15"
                    value="${escapeHtml(data.rate ?? '0')}"
                    required
                >
            </td>

            <td>
                <input
                    type="text"
                    class="form-control amount"
                    value="₹0.00"
                    readonly
                    tabindex="-1"
                >
            </td>

            <td class="text-center">
                <button
                    type="button"
                    class="btn btn-sm btn-outline-danger remove-item"
                    title="Remove Item"
                >
                    <i class="bi bi-trash"></i>
                </button>
            </td>
            `;

            return row;
        }

        function calculateRow(row) {

            const quantityInput = row.querySelector('.quantity');
            const rateInput = row.querySelector('.rate');
            const amountInput = row.querySelector('.amount');

            const quantity = parseFloat(quantityInput?.value) || 0;
            const rate = parseFloat(rateInput?.value) || 0;

            const amount = roundMoney(
                Math.max(quantity, 0) * Math.max(rate, 0)
                );

            if (amountInput) {
                amountInput.value = money(amount);
            }

            return amount;
        }

        function calculateInvoice() {

            let subtotal = 0;

            itemsBody.querySelectorAll('.item-row').forEach(function (row) {
                subtotal += calculateRow(row);
            });

            subtotal = roundMoney(subtotal);

            let discountInputValue =
            parseFloat(discountValue.value) || 0;

            discountInputValue =
            Math.max(discountInputValue, 0);

            let discountAmount = 0;

            if (discountType.value === 'percentage') {

                if (discountInputValue > 100) {
                    discountInputValue = 100;
                    discountValue.value = '100';
                }

                discountAmount =
                subtotal * (discountInputValue / 100);

            } else {

                discountAmount = discountInputValue;

            }

            discountAmount = Math.min(
                Math.max(discountAmount, 0),
                subtotal
                );

            discountAmount = roundMoney(discountAmount);

            const taxableAmount = roundMoney(
                Math.max(subtotal - discountAmount, 0)
                );

            let taxPercentage =
            parseFloat(taxInput.value) || 0;

            taxPercentage =
            Math.max(taxPercentage, 0);

            if (taxPercentage > 100) {
                taxPercentage = 100;
                taxInput.value = '100';
            }

            const taxAmount = roundMoney(
                taxableAmount * (taxPercentage / 100)
                );

            const total = roundMoney(
                Math.max(taxableAmount + taxAmount, 0)
                );

            subtotalDisplay.textContent =
            money(subtotal);

            discountAmountDisplay.textContent =
            '-' + money(discountAmount);

            taxAmountDisplay.textContent =
            '+' + money(taxAmount);

            totalDisplay.textContent =
            money(total);

            discountSymbol.textContent =
            discountType.value === 'percentage'
            ? '%'
            : '₹';
        }

        addItemButton.addEventListener('click', function () {

            itemError.textContent = '';

            const row = createItemRow(itemIndex, {
                quantity: '1',
                rate: '0'
            });

            itemsBody.appendChild(row);

            itemIndex++;

            calculateInvoice();

            row.querySelector('.item-name')?.focus();
        });

        itemsBody.addEventListener('input', function (event) {

            if (
                event.target.classList.contains('quantity') ||
                event.target.classList.contains('rate')
                ) {
                sanitizeNumberInput(event.target);
            calculateInvoice();
        }

    });

        itemsBody.addEventListener('blur', function (event) {

            if (
                event.target.classList.contains('quantity') ||
                event.target.classList.contains('rate')
                ) {
                sanitizeNumberInput(event.target);
            calculateInvoice();
        }

    }, true);

        itemsBody.addEventListener('click', function (event) {

            const button =
            event.target.closest('.remove-item');

            if (!button) {
                return;
            }

            const rows =
            itemsBody.querySelectorAll('.item-row');

            if (rows.length <= 1) {

                itemError.textContent =
                'At least one invoice item is required.';

                return;
            }

            button.closest('.item-row').remove();

            itemError.textContent = '';

            calculateInvoice();

        });

        discountValue.addEventListener('input', function () {

            sanitizeNumberInput(this);

            if (discountType.value === 'percentage') {

                const value =
                parseFloat(this.value) || 0;

                if (value > 100) {
                    this.value = '100';
                }

            }

            calculateInvoice();

        });

        discountValue.addEventListener('blur', function () {

            sanitizeNumberInput(this);

            calculateInvoice();

        });

        discountType.addEventListener('change', function () {

            const value =
            parseFloat(discountValue.value) || 0;

            if (
                this.value === 'percentage' &&
                value > 100
                ) {
                discountValue.value = '100';
        }

        calculateInvoice();

    });

        taxInput.addEventListener('input', function () {

            sanitizeNumberInput(this);

            const value =
            parseFloat(this.value) || 0;

            if (value > 100) {
                this.value = '100';
            }

            calculateInvoice();

        });

        taxInput.addEventListener('blur', function () {

            sanitizeNumberInput(this);

            calculateInvoice();

        });

        invoiceDate.addEventListener('change', function () {

            dueDate.min = this.value;

            if (
                dueDate.value &&
                this.value &&
                dueDate.value < this.value
                ) {
                dueDate.value = '';
        }

    });

        invoiceForm.addEventListener('submit', function (event) {

            if (submitting) {
                event.preventDefault();
                return;
            }

            const rows =
            itemsBody.querySelectorAll('.item-row');

            if (rows.length < 1) {

                event.preventDefault();

                itemError.textContent =
                'At least one invoice item is required.';

                return;
            }

            let valid = true;

            rows.forEach(function (row) {

                const itemName =
                row.querySelector('.item-name')?.value.trim();

                const quantity =
                parseFloat(
                    row.querySelector('.quantity')?.value
                    );

                const rate =
                parseFloat(
                    row.querySelector('.rate')?.value
                    );

                if (!itemName) {
                    valid = false;
                }

                if (
                    isNaN(quantity) ||
                    quantity < 0.01 ||
                    quantity > 100000000
                    ) {
                    valid = false;
            }

            if (
                isNaN(rate) ||
                rate < 0 ||
                rate > 999999999999.99
                ) {
                valid = false;
        }

    });

            const discount =
            parseFloat(discountValue.value) || 0;

            if (
                discount < 0 ||
                discount > 999999999999.99
                ) {
                valid = false;
        }

        if (
            discountType.value === 'percentage' &&
            discount > 100
            ) {
            valid = false;
    }

    const tax =
    parseFloat(taxInput.value) || 0;

    if (
        tax < 0 ||
        tax > 100
        ) {
        valid = false;
}

if (
    dueDate.value &&
    invoiceDate.value &&
    dueDate.value < invoiceDate.value
    ) {
    valid = false;
}

if (!valid) {

    event.preventDefault();

    itemError.textContent =
    'Please enter valid invoice values.';

    return;
}

submitting = true;

saveInvoiceButton.disabled = true;

saveInvoiceButton.innerHTML =
'<span class="spinner-border spinner-border-sm me-1"></span> Saving...';

});

        dueDate.min = invoiceDate.value;

        calculateInvoice();

    });
</script>

@endpush
