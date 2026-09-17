@extends('admin.layout.app')

@section('title', 'Create Quotation')

@section('content')

<div class="container-fluid">

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
        <div>
            <h4 class="mb-1">Create Quotation</h4>
            <p class="text-muted mb-0">Create a new quotation for your client.</p>
        </div>

        <a href="{{ route('admin.quotations.index') }}" class="btn btn-light border">
            <i class="bi bi-arrow-left me-1"></i>
            Back
        </a>
    </div>

    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show">
        <div class="fw-semibold mb-2">
            <i class="bi bi-exclamation-triangle me-1"></i>
            Please fix the following errors:
        </div>

        <ul class="mb-0">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>

        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <form action="{{ route('admin.quotations.store') }}" method="POST" id="quotationForm">
        @csrf

        <div class="card mb-4">

            <div class="card-header">
                <h5 class="mb-0">Quotation Information</h5>
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
                        {{ $client->company_name }}
                        @if($client->contact_person)
                        - {{ $client->contact_person }}
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
                    Quotation Date <span class="text-danger">*</span>
                </label>

                <input
                type="date"
                name="quotation_date"
                id="quotationDate"
                class="form-control @error('quotation_date') is-invalid @enderror"
                value="{{ old('quotation_date', now()->format('Y-m-d')) }}"
                required
                >

                @error('quotation_date')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror

            </div>

            <div class="col-md-3">

                <label class="form-label">
                    Valid Until
                </label>

                <input
                type="date"
                name="valid_until"
                id="validUntil"
                class="form-control @error('valid_until') is-invalid @enderror"
                value="{{ old('valid_until') }}"
                >

                @error('valid_until')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror

            </div>

            <div class="col-md-8">

                <label class="form-label">
                    Subject <span class="text-danger">*</span>
                </label>

                <input
                type="text"
                name="subject"
                class="form-control @error('subject') is-invalid @enderror"
                value="{{ old('subject') }}"
                maxlength="255"
                placeholder="Enter quotation subject"
                required
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
            <h5 class="mb-0">Quotation Items</h5>
            <small class="text-muted">
                Add products or services included in this quotation.
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

            <thead>
                <tr>
                    <th style="min-width:220px;">Item Name</th>
                    <th style="min-width:260px;">Description</th>
                    <th style="width:140px;">Quantity</th>
                    <th style="width:170px;">Rate</th>
                    <th style="width:180px;">Amount</th>
                    <th style="width:70px;"></th>
                </tr>
            </thead>

            <tbody id="itemsBody"></tbody>

        </table>

    </div>

    <div class="text-danger small mt-2" id="itemError"></div>

</div>

</div>

<div class="row g-4 mb-4">

    <div class="col-lg-7">

        <div class="card h-100">

            <div class="card-header">
                <h5 class="mb-0">Notes & Terms</h5>
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
                    placeholder="Additional notes..."
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
                    placeholder="Quotation terms and conditions..."
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

        <div class="card">

            <div class="card-header">
                <h5 class="mb-0">Quotation Summary</h5>
            </div>

            <div class="card-body">

                <div class="d-flex justify-content-between mb-3">
                    <span>Subtotal</span>
                    <strong id="subtotalDisplay">₹0.00</strong>
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
                name="discount"
                id="discount"
                class="form-control @error('discount') is-invalid @enderror"
                value="{{ old('discount', '0') }}"
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

        @error('discount')
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
    id="taxDisplay"
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

<div class="d-flex flex-wrap justify-content-end gap-2">

    <a
    href="{{ route('admin.quotations.index') }}"
    class="btn btn-light border"
    >
    Cancel
</a>

<button
type="submit"
class="btn btn-primary"
id="saveQuotationButton"
>
<i class="bi bi-check-lg me-1"></i>
Save Quotation
</button>

</div>

</form>

</div>

@endsection

@push('scripts')

<script>
    document.addEventListener('DOMContentLoaded', function () {

        const itemsBody = document.getElementById('itemsBody');
        const addItemButton = document.getElementById('addItem');
        const discountType = document.getElementById('discountType');
        const discount = document.getElementById('discount');
        const tax = document.getElementById('tax');
        const discountSymbol = document.getElementById('discountSymbol');
        const subtotalDisplay = document.getElementById('subtotalDisplay');
        const discountAmountDisplay = document.getElementById('discountAmountDisplay');
        const taxDisplay = document.getElementById('taxDisplay');
        const totalDisplay = document.getElementById('totalDisplay');
        const quotationForm = document.getElementById('quotationForm');
        const itemError = document.getElementById('itemError');
        const saveQuotationButton = document.getElementById('saveQuotationButton');
        const quotationDate = document.getElementById('quotationDate');
        const validUntil = document.getElementById('validUntil');

        let itemIndex = 0;
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

        function addItem(data = {}) {

            const row = document.createElement('tr');

            row.innerHTML = `
            <td>
                <input
                    type="text"
                    name="items[${itemIndex}][item_name]"
                    class="form-control item-name"
                    maxlength="255"
                    value="${escapeHtml(data.item_name || '')}"
                    placeholder="Product / Service"
                    required
                >
            </td>

            <td>
                <textarea
                    name="items[${itemIndex}][description]"
                    class="form-control"
                    rows="1"
                    maxlength="1000"
                    placeholder="Description"
                >${escapeHtml(data.description || '')}</textarea>
            </td>

            <td>
                <input
                    type="text"
                    name="items[${itemIndex}][quantity]"
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
                    name="items[${itemIndex}][rate]"
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

            itemsBody.appendChild(row);

            itemIndex++;

            row.querySelectorAll('.number-input').forEach(function (input) {

                input.addEventListener('input', function () {
                    sanitizeNumberInput(this);
                    calculate();
                });

                input.addEventListener('blur', function () {
                    sanitizeNumberInput(this);
                    calculate();
                });

            });

            calculate();
        }

        function calculate() {

            let subtotal = 0;

            itemsBody.querySelectorAll('tr').forEach(function (row) {

                const quantityInput = row.querySelector('.quantity');
                const rateInput = row.querySelector('.rate');
                const amountInput = row.querySelector('.amount');

                const quantity = parseFloat(quantityInput?.value) || 0;
                const rate = parseFloat(rateInput?.value) || 0;

                const amount = roundMoney(quantity * rate);

                if (amountInput) {
                    amountInput.value = money(amount);
                }

                subtotal += amount;
            });

            subtotal = roundMoney(subtotal);

            let discountValue = parseFloat(discount.value) || 0;

            discountValue = Math.max(discountValue, 0);

            let discountAmount = 0;

            if (discountType.value === 'percentage') {

                discountValue = Math.min(discountValue, 100);

                discountAmount = subtotal * (discountValue / 100);

            } else {

                discountAmount = discountValue;

            }

            discountAmount = Math.min(
                Math.max(discountAmount, 0),
                subtotal
                );

            discountAmount = roundMoney(discountAmount);

            const taxableAmount = roundMoney(
                Math.max(subtotal - discountAmount, 0)
                );

            let taxPercentage = parseFloat(tax.value) || 0;

            taxPercentage = Math.min(
                Math.max(taxPercentage, 0),
                100
                );

            const taxAmount = roundMoney(
                taxableAmount * (taxPercentage / 100)
                );

            const total = roundMoney(
                Math.max(taxableAmount + taxAmount, 0)
                );

            subtotalDisplay.textContent = money(subtotal);

            discountAmountDisplay.textContent =
            '-' + money(discountAmount);

            taxDisplay.textContent =
            '+' + money(taxAmount);

            totalDisplay.textContent =
            money(total);

            discountSymbol.textContent =
            discountType.value === 'percentage' ? '%' : '₹';

            if (discountType.value === 'percentage') {

                if (discountValue > 100) {
                    discount.value = '100';
                }

                discount.setAttribute('maxlength', '6');

            } else {

                discount.setAttribute('maxlength', '15');

            }

            if (taxPercentage > 100) {
                tax.value = '100';
            }

            if (taxPercentage < 0) {
                tax.value = '0';
            }
        }

        addItemButton.addEventListener('click', function () {

            itemError.textContent = '';

            addItem();

        });

        itemsBody.addEventListener('click', function (event) {

            const button = event.target.closest('.remove-item');

            if (!button) {
                return;
            }

            const rows = itemsBody.querySelectorAll('tr');

            if (rows.length <= 1) {

                itemError.textContent =
                'At least one quotation item is required.';

                return;
            }

            button.closest('tr').remove();

            itemError.textContent = '';

            calculate();

        });

        discount.addEventListener('input', function () {

            sanitizeNumberInput(this);

            if (discountType.value === 'percentage') {

                const value = parseFloat(this.value) || 0;

                if (value > 100) {
                    this.value = '100';
                }
            }

            calculate();

        });

        tax.addEventListener('input', function () {

            sanitizeNumberInput(this);

            const value = parseFloat(this.value) || 0;

            if (value > 100) {
                this.value = '100';
            }

            calculate();

        });

        discountType.addEventListener('change', function () {

            discount.classList.remove('is-invalid');

            const value = parseFloat(discount.value) || 0;

            if (this.value === 'percentage' && value > 100) {
                discount.value = '100';
            }

            calculate();

        });

        quotationDate.addEventListener('change', function () {

            validUntil.min = this.value;

            if (
                validUntil.value &&
                this.value &&
                validUntil.value < this.value
                ) {
                validUntil.value = '';
        }

    });

        quotationForm.addEventListener('submit', function (event) {

            if (submitting) {
                event.preventDefault();
                return;
            }

            const rows = itemsBody.querySelectorAll('tr');

            if (rows.length < 1) {

                event.preventDefault();

                itemError.textContent =
                'At least one quotation item is required.';

                return;
            }

            let valid = true;

            rows.forEach(function (row) {

                const itemName =
                row.querySelector('.item-name')?.value.trim();

                const quantity =
                parseFloat(row.querySelector('.quantity')?.value);

                const rate =
                parseFloat(row.querySelector('.rate')?.value);

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

            const discountValue =
            parseFloat(discount.value) || 0;

            if (
                discountValue < 0 ||
                discountValue > 999999999999.99
                ) {
                valid = false;
        }

        if (
            discountType.value === 'percentage' &&
            discountValue > 100
            ) {
            valid = false;
    }

    const taxValue =
    parseFloat(tax.value) || 0;

    if (
        taxValue < 0 ||
        taxValue > 100
        ) {
        valid = false;
}

if (
    validUntil.value &&
    quotationDate.value &&
    validUntil.value < quotationDate.value
    ) {
    valid = false;
}

if (!valid) {

    event.preventDefault();

    itemError.textContent =
    'Please enter valid quotation values.';

    return;
}

submitting = true;

saveQuotationButton.disabled = true;

saveQuotationButton.innerHTML =
'<span class="spinner-border spinner-border-sm me-1"></span> Saving...';

});

        validUntil.min = quotationDate.value;

        @if(old('items'))

        @foreach(old('items') as $item)

        addItem({
            item_name: @json($item['item_name'] ?? ''),
            description: @json($item['description'] ?? ''),
            quantity: @json($item['quantity'] ?? '1'),
            rate: @json($item['rate'] ?? '0')
        });

        @endforeach

        @else

        addItem({
            quantity: '1',
            rate: '0'
        });

        @endif

        calculate();

    });
</script>

@endpush
