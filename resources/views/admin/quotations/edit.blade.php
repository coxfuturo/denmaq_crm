@extends('admin.layout.app')

@section('title', 'Edit Quotation')

@section('content')

@php
$user = auth()->user();
$isSuperAdmin = $user && $user->hasRole('Super Admin');
@endphp

<div class="container-fluid">

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
        <div>
            <h4 class="mb-1">Edit Quotation</h4>
            <p class="text-muted mb-0">{{ $quotation->quotation_number }}</p>
        </div>

        <a href="{{ route('admin.quotations.show', $quotation->id) }}" class="btn btn-light border">
            <i class="bi bi-arrow-left me-1"></i>
            Back
        </a>
    </div>

    @if($errors->any())
    <div class="alert alert-danger">
        <strong>Please fix the following errors:</strong>
        <ul class="mb-0 mt-2">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form
    action="{{ route('admin.quotations.update', $quotation->id) }}"
    method="POST"
    id="quotationForm"
    novalidate
    >
    @csrf
    @method('PUT')

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
                    {{ (string) old('client_id', $quotation->client_id) === (string) $client->id ? 'selected' : '' }}
                    >
                    {{ $client->company_name }}
                    @if($client->contact_person)
                    - {{ $client->contact_person }}
                    @endif
                </option>
                @endforeach
            </select>

            @error('client_id')
            <div class="invalid-feedback">{{ $message }}</div>
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
            value="{{ old('quotation_date', $quotation->quotation_date?->format('Y-m-d')) }}"
            required
            >

            @error('quotation_date')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-md-3">
            <label class="form-label">Valid Until</label>

            <input
            type="date"
            name="valid_until"
            id="validUntil"
            class="form-control @error('valid_until') is-invalid @enderror"
            value="{{ old('valid_until', $quotation->valid_until?->format('Y-m-d')) }}"
            >

            @error('valid_until')
            <div class="invalid-feedback">{{ $message }}</div>
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
            value="{{ old('subject', $quotation->subject) }}"
            maxlength="255"
            required
            >

            @error('subject')
            <div class="invalid-feedback">{{ $message }}</div>
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
            {{ old('status', $quotation->status) === $status ? 'selected' : '' }}
            >
            {{ $status }}
        </option>
        @endforeach
    </select>

    @error('status')
    <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

</div>
</div>
</div>

<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Quotation Items</h5>

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
        <table class="table table-bordered align-middle mb-0">
            <thead>
                <tr>
                    <th width="22%">Item Name</th>
                    <th width="30%">Description</th>
                    <th width="13%">Quantity</th>
                    <th width="15%">Rate</th>
                    <th width="15%">Amount</th>
                    <th width="5%"></th>
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

                <div class="mb-3">
                    <label class="form-label">Notes</label>

                    <textarea
                    name="notes"
                    rows="5"
                    maxlength="5000"
                    class="form-control @error('notes') is-invalid @enderror"
                    >{{ old('notes', $quotation->notes) }}</textarea>

                    @error('notes')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div>
                    <label class="form-label">Terms & Conditions</label>

                    <textarea
                    name="terms"
                    rows="5"
                    maxlength="5000"
                    class="form-control @error('terms') is-invalid @enderror"
                    >{{ old('terms', $quotation->terms) }}</textarea>

                    @error('terms')
                    <div class="invalid-feedback">{{ $message }}</div>
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
                        <label class="form-label">Discount Type</label>

                        <select
                        name="discount_type"
                        id="discountType"
                        class="form-select @error('discount_type') is-invalid @enderror"
                        >
                        <option
                        value="fixed"
                        {{ old('discount_type', $quotation->discount_type) === 'fixed' ? 'selected' : '' }}
                        >
                        Fixed
                    </option>

                    <option
                    value="percentage"
                    {{ old('discount_type', $quotation->discount_type) === 'percentage' ? 'selected' : '' }}
                    >
                    Percentage
                </option>
            </select>

            @error('discount_type')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-7">
            <label class="form-label" id="discountLabel">
                Discount
            </label>

            <div class="input-group">
                <span class="input-group-text" id="discountPrefix">₹</span>

                <input
                type="text"
                name="discount_value"
                id="discountValue"
                class="form-control @error('discount_value') is-invalid @enderror"
                inputmode="decimal"
                autocomplete="off"
                value="{{ old('discount_value', $quotation->discount_value ?? 0) }}"
                >
            </div>

            @error('discount_value')
            <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>

    </div>

    <div class="d-flex justify-content-between mb-3">
        <span>Discount Amount</span>
        <strong id="discountAmountDisplay">₹0.00</strong>
    </div>

    <div class="mb-3">
        <label class="form-label">Tax</label>

        <div class="input-group">
            <span class="input-group-text">₹</span>

            <input
            type="text"
            name="tax"
            id="tax"
            class="form-control @error('tax') is-invalid @enderror"
            inputmode="decimal"
            autocomplete="off"
            value="{{ old('tax', $quotation->tax ?? 0) }}"
            >
        </div>

        @error('tax')
        <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>

    <hr>

    <div class="d-flex justify-content-between fs-5">
        <span>Grand Total</span>
        <strong id="totalDisplay">₹0.00</strong>
    </div>

</div>
</div>

</div>

</div>

<div class="d-flex justify-content-end gap-2">

    <a
    href="{{ route('admin.quotations.show', $quotation->id) }}"
    class="btn btn-light border"
    >
    Cancel
</a>

<button
type="submit"
class="btn btn-primary"
id="updateButton"
>
<i class="bi bi-check-lg me-1"></i>
Update Quotation
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
        const discountValue = document.getElementById('discountValue');
        const discountPrefix = document.getElementById('discountPrefix');
        const discountLabel = document.getElementById('discountLabel');
        const tax = document.getElementById('tax');
        const subtotalDisplay = document.getElementById('subtotalDisplay');
        const discountAmountDisplay = document.getElementById('discountAmountDisplay');
        const totalDisplay = document.getElementById('totalDisplay');
        const quotationForm = document.getElementById('quotationForm');
        const itemError = document.getElementById('itemError');
        const quotationDate = document.getElementById('quotationDate');
        const validUntil = document.getElementById('validUntil');
        const updateButton = document.getElementById('updateButton');

        let itemIndex = 0;

        function money(value) {
            const number = Number(value) || 0;

            return '₹' + number.toLocaleString('en-IN', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }

        function cleanDecimal(value) {
            let cleaned = String(value ?? '').replace(/[^\d.]/g, '');
            const firstDot = cleaned.indexOf('.');

            if (firstDot !== -1) {
                cleaned =
                cleaned.substring(0, firstDot + 1) +
                cleaned.substring(firstDot + 1).replace(/\./g, '');
            }

            if (cleaned.includes('.')) {
                const parts = cleaned.split('.');
                cleaned = parts[0] + '.' + parts[1].substring(0, 2);
            }

            return cleaned;
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
                    value="${escapeHtml(data.item_name)}"
                    required
                >
            </td>

            <td>
                <textarea
                    name="items[${itemIndex}][description]"
                    class="form-control"
                    rows="1"
                    maxlength="1000"
                >${escapeHtml(data.description)}</textarea>
            </td>

            <td>
                <input
                    type="text"
                    name="items[${itemIndex}][quantity]"
                    class="form-control quantity"
                    inputmode="decimal"
                    autocomplete="off"
                    maxlength="15"
                    value="${escapeHtml(data.quantity || '1')}"
                    required
                >
            </td>

            <td>
                <input
                    type="text"
                    name="items[${itemIndex}][rate]"
                    class="form-control rate"
                    inputmode="decimal"
                    autocomplete="off"
                    maxlength="15"
                    value="${escapeHtml(data.rate || '0')}"
                    required
                >
            </td>

            <td>
                <input
                    type="text"
                    class="form-control amount"
                    value="₹0.00"
                    readonly
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

            const quantityInput = row.querySelector('.quantity');
            const rateInput = row.querySelector('.rate');

            quantityInput.value = cleanDecimal(quantityInput.value);
            rateInput.value = cleanDecimal(rateInput.value);

            calculate();
        }

        function calculate() {
            let subtotal = 0;

            document.querySelectorAll('#itemsBody tr').forEach(function (row) {
                const quantityInput = row.querySelector('.quantity');
                const rateInput = row.querySelector('.rate');
                const amountInput = row.querySelector('.amount');

                const quantity = parseFloat(quantityInput?.value) || 0;
                const rate = parseFloat(rateInput?.value) || 0;

                const amount = Math.round((quantity * rate + Number.EPSILON) * 100) / 100;

                if (amountInput) {
                    amountInput.value = money(amount);
                }

                subtotal += amount;
            });

            subtotal = Math.round((subtotal + Number.EPSILON) * 100) / 100;

            let discount = parseFloat(discountValue.value) || 0;

            if (discount < 0) {
                discount = 0;
            }

            let discountAmount = 0;

            if (discountType.value === 'percentage') {
                if (discount > 100) {
                    discount = 100;
                }

                discountAmount = subtotal * (discount / 100);
            } else {
                discountAmount = discount;
            }

            discountAmount = Math.min(discountAmount, subtotal);
            discountAmount = Math.round((discountAmount + Number.EPSILON) * 100) / 100;

            let taxAmount = parseFloat(tax.value) || 0;

            if (taxAmount < 0) {
                taxAmount = 0;
            }

            taxAmount = Math.round((taxAmount + Number.EPSILON) * 100) / 100;

            const total = Math.max(
                0,
                Math.round(
                    (subtotal - discountAmount + taxAmount + Number.EPSILON) * 100
                    ) / 100
                );

            subtotalDisplay.textContent = money(subtotal);
            discountAmountDisplay.textContent = money(discountAmount);
            totalDisplay.textContent = money(total);
        }

        function updateDiscountUI() {
            if (discountType.value === 'percentage') {
                discountPrefix.textContent = '%';
                discountLabel.textContent = 'Discount (%)';
                discountValue.setAttribute('maxlength', '6');
            } else {
                discountPrefix.textContent = '₹';
                discountLabel.textContent = 'Discount';
                discountValue.setAttribute('maxlength', '15');
            }

            calculate();
        }

        addItemButton.addEventListener('click', function () {
            addItem({
                item_name: '',
                description: '',
                quantity: '1',
                rate: '0'
            });
        });

        itemsBody.addEventListener('input', function (event) {
            if (
                event.target.classList.contains('quantity') ||
                event.target.classList.contains('rate')
                ) {
                event.target.value = cleanDecimal(event.target.value);
            calculate();
        }
    });

        itemsBody.addEventListener('click', function (event) {
            const button = event.target.closest('.remove-item');

            if (!button) {
                return;
            }

            const rows = itemsBody.querySelectorAll('tr');

            if (rows.length <= 1) {
                itemError.textContent = 'At least one quotation item is required.';
                return;
            }

            button.closest('tr').remove();
            itemError.textContent = '';

            calculate();
        });

        discountType.addEventListener('change', function () {
            if (discountType.value === 'percentage') {
                const value = parseFloat(discountValue.value) || 0;

                if (value > 100) {
                    discountValue.value = '100';
                }
            }

            updateDiscountUI();
        });

        discountValue.addEventListener('input', function () {
            discountValue.value = cleanDecimal(discountValue.value);

            if (discountType.value === 'percentage') {
                const value = parseFloat(discountValue.value) || 0;

                if (value > 100) {
                    discountValue.value = '100';
                }
            }

            calculate();
        });

        tax.addEventListener('input', function () {
            tax.value = cleanDecimal(tax.value);
            calculate();
        });

        quotationDate.addEventListener('change', function () {
            validUntil.min = quotationDate.value;

            if (
                validUntil.value &&
                quotationDate.value &&
                validUntil.value < quotationDate.value
                ) {
                validUntil.value = quotationDate.value;
        }
    });

        quotationForm.addEventListener('submit', function (event) {
            const rows = itemsBody.querySelectorAll('tr');

            itemError.textContent = '';

            if (rows.length < 1) {
                event.preventDefault();
                itemError.textContent = 'At least one quotation item is required.';
                return;
            }

            let valid = true;

            rows.forEach(function (row) {
                const itemName = row.querySelector('.item-name');
                const quantity = row.querySelector('.quantity');
                const rate = row.querySelector('.rate');

                if (!itemName.value.trim()) {
                    valid = false;
                    itemName.classList.add('is-invalid');
                } else {
                    itemName.classList.remove('is-invalid');
                }

                const quantityValue = parseFloat(quantity.value);
                const rateValue = parseFloat(rate.value);

                if (
                    !quantity.value ||
                    isNaN(quantityValue) ||
                    quantityValue < 0.01 ||
                    quantityValue > 100000000
                    ) {
                    valid = false;
                quantity.classList.add('is-invalid');
            } else {
                quantity.classList.remove('is-invalid');
            }

            if (
                rate.value === '' ||
                isNaN(rateValue) ||
                rateValue < 0 ||
                rateValue > 999999999999.99
                ) {
                valid = false;
            rate.classList.add('is-invalid');
        } else {
            rate.classList.remove('is-invalid');
        }
    });

            const discount = parseFloat(discountValue.value) || 0;
            const taxAmount = parseFloat(tax.value) || 0;

            if (discount < 0) {
                valid = false;
                discountValue.classList.add('is-invalid');
            }

            if (
                discountType.value === 'percentage' &&
                discount > 100
                ) {
                valid = false;
            discountValue.classList.add('is-invalid');
        }

        if (taxAmount < 0) {
            valid = false;
            tax.classList.add('is-invalid');
        }

        if (!valid) {
            event.preventDefault();

            itemError.textContent = 'Please check all quotation item and amount fields.';
            return;
        }

        updateButton.disabled = true;
        updateButton.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Updating...';
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
        @foreach($quotation->items as $item)
        addItem({
            item_name: @json($item->item_name),
            description: @json($item->description ?? ''),
            quantity: @json($item->quantity),
            rate: @json($item->rate)
        });
        @endforeach
        @endif

        if (itemsBody.querySelectorAll('tr').length === 0) {
            addItem({
                item_name: '',
                description: '',
                quantity: '1',
                rate: '0'
            });
        }

        updateDiscountUI();
        calculate();
    });
</script>

@endpush