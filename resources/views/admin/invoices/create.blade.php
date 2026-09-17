@extends('admin.layout.app')

@section('title', 'Create Invoice')

@section('content')

<div class="container-fluid">

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
        <div>
            <h4 class="mb-1">Create Invoice</h4>
            <p class="text-muted mb-0">Create an invoice from an accepted quotation.</p>
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

        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}

        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <form method="POST" action="{{ route('admin.invoices.store') }}" id="invoiceForm">
        @csrf

        <input
        type="hidden"
        name="quotation_id"
        id="quotationId"
        value="{{ old('quotation_id', $selectedQuotation?->id) }}"
        >

        <input
        type="hidden"
        name="status"
        id="invoiceStatus"
        value="{{ old('status', 'Draft') }}"
        >

        <input
        type="hidden"
        name="discount_type"
        id="discountType"
        value="{{ old('discount_type', $selectedQuotation?->discount_type ?? 'fixed') }}"
        >

        <input
        type="hidden"
        name="discount_value"
        id="discountValue"
        value="{{ old('discount_value', $selectedQuotation?->discount ?? 0) }}"
        >

        <input
        type="hidden"
        name="tax"
        id="taxValue"
        value="{{ old('tax', $selectedQuotation?->tax ?? 0) }}"
        >

        <div class="card mb-4">

            <div class="card-header">
                <h5 class="mb-0">Quotation Selection</h5>
            </div>

            <div class="card-body">

                <div class="row g-3">

                    <div class="col-lg-8">

                        <label class="form-label">
                            Accepted Quotation
                            <span class="text-danger">*</span>
                        </label>

                        <select
                        id="quotationSelect"
                        class="form-select @error('quotation_id') is-invalid @enderror"
                        required
                        >
                        <option value="">Select Accepted Quotation</option>

                        @foreach($quotations as $quotation)
                        <option
                        value="{{ $quotation->id }}"
                        {{ (string) old('quotation_id', $selectedQuotation?->id) === (string) $quotation->id ? 'selected' : '' }}
                        >
                        {{ $quotation->quotation_number }}
                        -
                        {{ $quotation->client?->contact_person }}
                        @if($quotation->client?->company_name)
                        - {{ $quotation->client->company_name }}
                        @endif
                    </option>
                    @endforeach
                </select>

                @error('quotation_id')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror

                @if($quotations->isEmpty())
                <div class="text-muted small mt-2">
                    No accepted quotations are available for invoice creation.
                </div>
                @else
                <div class="form-text">
                    Only accepted quotations without an existing invoice are shown.
                </div>
                @endif

            </div>

            <div class="col-lg-4">

                <label class="form-label">
                    Quotation Date
                </label>

                <input
                type="text"
                id="quotationDateDisplay"
                class="form-control"
                value="{{ $selectedQuotation?->quotation_date?->format('d M Y') }}"
                readonly
                >

            </div>

        </div>

    </div>

</div>

<div id="invoiceDetails" class="{{ $selectedQuotation ? '' : 'd-none' }}">

    <div class="card mb-4">

        <div class="card-header">
            <h5 class="mb-0">Invoice Information</h5>
        </div>

        <div class="card-body">

            <div class="row g-3">

                <div class="col-md-6">

                    <label class="form-label">
                        Client
                    </label>

                    <input
                    type="text"
                    id="clientDisplay"
                    class="form-control"
                    value="{{ $selectedQuotation?->client?->contact_person }}{{ $selectedQuotation?->client?->company_name ? ' - '.$selectedQuotation->client->company_name : '' }}"
                    readonly
                    >

                </div>

                <div class="col-md-3">

                    <label class="form-label">
                        Invoice Date
                        <span class="text-danger">*</span>
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
                    id="subjectDisplay"
                    class="form-control"
                    value="{{ $selectedQuotation?->subject }}"
                    readonly
                    >

                </div>

                <div class="col-md-4">

                    <label class="form-label">
                        Invoice Status
                    </label>

                    <input
                    type="text"
                    class="form-control"
                    value="Draft"
                    readonly
                    >

                </div>

            </div>

        </div>

    </div>

    <div class="card mb-4">

        <div class="card-header">

            <div>
                <h5 class="mb-0">Invoice Items</h5>

                <small class="text-muted">
                    Items are copied automatically from the accepted quotation.
                </small>
            </div>

        </div>

        <div class="card-body">

            <div class="table-responsive">

                <table class="table table-bordered align-middle mb-0">

                    <thead class="table-light">

                        <tr>
                            <th style="min-width:220px;">Item</th>
                            <th style="min-width:260px;">Description</th>
                            <th style="width:140px;">Quantity</th>
                            <th style="width:170px;">Rate</th>
                            <th style="width:180px;">Amount</th>
                        </tr>

                    </thead>

                    <tbody id="itemsBody">

                        @if($selectedQuotation)

                        @foreach($selectedQuotation->items as $index => $item)

                        <tr class="item-row">

                            <td>

                                <input
                                type="hidden"
                                name="items[{{ $index }}][item_name]"
                                value="{{ $item->item_name }}"
                                >

                                <input
                                type="text"
                                class="form-control"
                                value="{{ $item->item_name }}"
                                readonly
                                >

                            </td>

                            <td>

                                <textarea
                                name="items[{{ $index }}][description]"
                                class="form-control"
                                rows="1"
                                readonly
                                >{{ $item->description }}</textarea>

                            </td>

                            <td>

                                <input
                                type="hidden"
                                name="items[{{ $index }}][quantity]"
                                value="{{ $item->quantity }}"
                                >

                                <input
                                type="text"
                                class="form-control"
                                value="{{ number_format((float) $item->quantity, 2, '.', '') }}"
                                readonly
                                >

                            </td>

                            <td>

                                <input
                                type="hidden"
                                name="items[{{ $index }}][rate]"
                                value="{{ $item->rate }}"
                                >

                                <input
                                type="text"
                                class="form-control"
                                value="₹{{ number_format((float) $item->rate, 2) }}"
                                readonly
                                >

                            </td>

                            <td>

                                <input
                                type="text"
                                class="form-control"
                                value="₹{{ number_format((float) $item->amount, 2) }}"
                                readonly
                                >

                            </td>

                        </tr>

                        @endforeach

                        @endif

                    </tbody>

                </table>

            </div>

            <div id="itemError" class="text-danger small mt-2"></div>

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
                        id="notes"
                        rows="5"
                        class="form-control"
                        readonly
                        >{{ $selectedQuotation?->notes }}</textarea>

                    </div>

                    <div>

                        <label class="form-label">
                            Terms & Conditions
                        </label>

                        <textarea
                        name="terms"
                        id="terms"
                        rows="5"
                        class="form-control"
                        readonly
                        >{{ $selectedQuotation?->terms }}</textarea>

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
                        <span>Subtotal</span>
                        <strong id="subtotalDisplay">₹0.00</strong>
                    </div>

                    <div class="d-flex justify-content-between mb-3">
                        <span>
                            Discount
                            <span id="discountLabel"></span>
                        </span>

                        <strong
                        id="discountAmountDisplay"
                        class="text-danger"
                        >
                        -₹0.00
                    </strong>
                </div>

                <div class="d-flex justify-content-between mb-3">

                    <span>
                        Tax
                        <span id="taxLabel"></span>
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
Create Invoice
</button>

</div>

</div>

</form>

</div>

@endsection

@push('scripts')

<script>
    document.addEventListener('DOMContentLoaded', function () {

        const invoiceForm = document.getElementById('invoiceForm');
        const quotationSelect = document.getElementById('quotationSelect');
        const quotationId = document.getElementById('quotationId');
        const invoiceStatus = document.getElementById('invoiceStatus');
        const discountType = document.getElementById('discountType');
        const discountValue = document.getElementById('discountValue');
        const taxValue = document.getElementById('taxValue');
        const quotationDateDisplay = document.getElementById('quotationDateDisplay');
        const invoiceDetails = document.getElementById('invoiceDetails');
        const clientDisplay = document.getElementById('clientDisplay');
        const subjectDisplay = document.getElementById('subjectDisplay');
        const itemsBody = document.getElementById('itemsBody');
        const notes = document.getElementById('notes');
        const terms = document.getElementById('terms');
        const subtotalDisplay = document.getElementById('subtotalDisplay');
        const discountAmountDisplay = document.getElementById('discountAmountDisplay');
        const taxAmountDisplay = document.getElementById('taxAmountDisplay');
        const totalDisplay = document.getElementById('totalDisplay');
        const discountLabel = document.getElementById('discountLabel');
        const taxLabel = document.getElementById('taxLabel');
        const invoiceDate = document.getElementById('invoiceDate');
        const dueDate = document.getElementById('dueDate');
        const itemError = document.getElementById('itemError');
        const saveInvoiceButton = document.getElementById('saveInvoiceButton');

        const quotations = @json($quotations->values());

        const quotationMap = {};

        quotations.forEach(function (quotation) {
            quotationMap[String(quotation.id)] = quotation;
        });

        function money(value) {
            const number = Number(value) || 0;

            return '₹' + number.toLocaleString('en-IN', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }

        function roundMoney(value) {
            return Math.round(
                (Number(value) + Number.EPSILON) * 100
                ) / 100;
        }

        function calculateQuotation(quotation) {

            if (!quotation) {

                subtotalDisplay.textContent = money(0);
                discountAmountDisplay.textContent = '-' + money(0);
                taxAmountDisplay.textContent = '+' + money(0);
                totalDisplay.textContent = money(0);
                discountLabel.textContent = '';
                taxLabel.textContent = '';

                return;
            }

            let subtotal = 0;

            if (Array.isArray(quotation.items)) {

                quotation.items.forEach(function (item) {

                    const quantity = Number(item.quantity) || 0;
                    const rate = Number(item.rate) || 0;

                    subtotal += Math.max(quantity, 0) * Math.max(rate, 0);

                });

            }

            subtotal = roundMoney(subtotal);

            let discount = Number(quotation.discount) || 0;

            discount = Math.max(discount, 0);

            let discountAmount = 0;

            if (quotation.discount_type === 'percentage') {

                discount = Math.min(discount, 100);

                discountAmount = roundMoney(
                    subtotal * (discount / 100)
                    );

                discountLabel.textContent =
                '(' + discount.toFixed(2) + '%)';

            } else {

                discountAmount = roundMoney(discount);

                discountLabel.textContent = '(Fixed)';
            }

            discountAmount = Math.min(
                Math.max(discountAmount, 0),
                subtotal
                );

            const taxableAmount = roundMoney(
                Math.max(subtotal - discountAmount, 0)
                );

            let tax = Number(quotation.tax) || 0;

            tax = Math.min(
                Math.max(tax, 0),
                100
                );

            const taxAmount = roundMoney(
                taxableAmount * (tax / 100)
                );

            const total = roundMoney(
                Math.max(taxableAmount + taxAmount, 0)
                );

            subtotalDisplay.textContent = money(subtotal);

            discountAmountDisplay.textContent =
            '-' + money(discountAmount);

            taxAmountDisplay.textContent =
            '+' + money(taxAmount);

            totalDisplay.textContent =
            money(total);

            taxLabel.textContent =
            '(' + tax.toFixed(2) + '%)';

            discountType.value =
            quotation.discount_type || 'fixed';

            discountValue.value =
            quotation.discount ?? 0;

            taxValue.value =
            quotation.tax ?? 0;

            invoiceStatus.value = 'Draft';
        }

        function renderQuotation(quotation) {

            if (!quotation) {

                quotationId.value = '';
                invoiceStatus.value = 'Draft';
                discountType.value = 'fixed';
                discountValue.value = '0';
                taxValue.value = '0';

                quotationDateDisplay.value = '';
                clientDisplay.value = '';
                subjectDisplay.value = '';
                notes.value = '';
                terms.value = '';

                itemsBody.innerHTML = '';

                invoiceDetails.classList.add('d-none');

                calculateQuotation(null);

                return;
            }

            quotationId.value = quotation.id;

            invoiceStatus.value = 'Draft';

            discountType.value =
            quotation.discount_type || 'fixed';

            discountValue.value =
            quotation.discount ?? 0;

            taxValue.value =
            quotation.tax ?? 0;

            quotationDateDisplay.value =
            quotation.quotation_date || '';

            const clientName =
            quotation.client?.contact_person || '';

            const companyName =
            quotation.client?.company_name || '';

            clientDisplay.value =
            companyName
            ? clientName + ' - ' + companyName
            : clientName;

            subjectDisplay.value =
            quotation.subject || '';

            notes.value =
            quotation.notes || '';

            terms.value =
            quotation.terms || '';

            itemsBody.innerHTML = '';

            if (Array.isArray(quotation.items)) {

                quotation.items.forEach(function (item, index) {

                    const row = document.createElement('tr');

                    row.className = 'item-row';

                    row.innerHTML = `
                    <td>
                        <input
                            type="hidden"
                            name="items[${index}][item_name]"
                            value="${escapeHtml(item.item_name || '')}"
                        >

                        <input
                            type="text"
                            class="form-control"
                            value="${escapeHtml(item.item_name || '')}"
                            readonly
                        >
                    </td>

                    <td>
                        <textarea
                            name="items[${index}][description]"
                            class="form-control"
                            rows="1"
                            readonly
                        >${escapeHtml(item.description || '')}</textarea>
                    </td>

                    <td>
                        <input
                            type="hidden"
                            name="items[${index}][quantity]"
                            value="${item.quantity ?? 0}"
                        >

                        <input
                            type="text"
                            class="form-control"
                            value="${Number(item.quantity || 0).toFixed(2)}"
                            readonly
                        >
                    </td>

                    <td>
                        <input
                            type="hidden"
                            name="items[${index}][rate]"
                            value="${item.rate ?? 0}"
                        >

                        <input
                            type="text"
                            class="form-control"
                            value="${money(item.rate)}"
                            readonly
                        >
                    </td>

                    <td>
                        <input
                            type="text"
                            class="form-control"
                            value="${money(item.amount)}"
                            readonly
                        >
                    </td>
                    `;

                    itemsBody.appendChild(row);
                });
            }

            invoiceDetails.classList.remove('d-none');

            calculateQuotation(quotation);
        }

        function escapeHtml(value) {

            return String(value ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
        }

        quotationSelect.addEventListener('change', function () {

            const selectedId = String(this.value || '');

            const quotation =
            selectedId
            ? quotationMap[selectedId]
            : null;

            renderQuotation(quotation);
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

            itemError.textContent = '';

            if (!quotationId.value) {

                event.preventDefault();

                itemError.textContent =
                'Please select an accepted quotation.';

                quotationSelect.focus();

                return;
            }

            const quotation =
            quotationMap[String(quotationId.value)];

            if (!quotation) {

                event.preventDefault();

                itemError.textContent =
                'Selected quotation could not be found.';

                return;
            }

            if (
                !quotation.items ||
                !quotation.items.length
                ) {

                event.preventDefault();

            itemError.textContent =
            'The selected quotation has no items.';

            return;
        }

        if (!invoiceDate.value) {

            event.preventDefault();

            itemError.textContent =
            'Please enter an invoice date.';

            invoiceDate.focus();

            return;
        }

        if (
            dueDate.value &&
            dueDate.value < invoiceDate.value
            ) {

            event.preventDefault();

        itemError.textContent =
        'Due date cannot be earlier than invoice date.';

        dueDate.focus();

        return;
    }

    invoiceStatus.value = 'Draft';

    if (!discountType.value) {
        discountType.value = 'fixed';
    }

    if (discountValue.value === '') {
        discountValue.value = '0';
    }

    if (taxValue.value === '') {
        taxValue.value = '0';
    }

    saveInvoiceButton.disabled = true;

    saveInvoiceButton.innerHTML =
    '<span class="spinner-border spinner-border-sm me-1"></span> Creating...';
});

        dueDate.min = invoiceDate.value;

        const initialQuotationId =
        String(quotationSelect.value || '');

        if (
            initialQuotationId &&
            quotationMap[initialQuotationId]
            ) {
            renderQuotation(
                quotationMap[initialQuotationId]
                );
    }

});
</script>

@endpush
