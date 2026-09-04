@extends('dashboardLayouts.main')
@section('title', 'Create Invoice')

@section('breadcrumbTitle', 'Create Invoice')

@section('breadcrumbs')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item"><a href="{{ route('invoices.index') }}">Invoices</a></li>
<li class="breadcrumb-item active">Create</li>
@endsection

@section('content')
<style>
    .invoice-card {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        padding: 24px;
        margin-bottom: 24px;
    }

    .billed-by-info {
        font-size: 0.85rem;
        color: #4b5563;
        line-height: 1.5;
        text-align: right;
    }

    .billed-by-info strong {
        font-weight: 700;
        color: #1f2937;
    }

    .form-control-custom {
        border: 1px solid #d1d5db;
        border-radius: 8px;
        padding: 10px 14px;
        font-size: 0.95rem;
        transition: border-color 0.15s ease-in-out;
    }
    .form-control-custom:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        outline: none;
    }

    .field-label {
        font-size: 0.85rem;
        font-weight: 600;
        color: #374151;
        margin-bottom: 6px;
        display: block;
    }

    .table-products-services th {
        background-color: #f9fafb;
        color: #374151;
        font-weight: 600;
        font-size: 0.85rem;
        border-bottom: 1px solid #e5e7eb;
        padding: 12px 16px;
    }
    .table-products-services td {
        padding: 12px 16px;
        vertical-align: middle;
        border-bottom: 1px solid #f3f4f6;
    }

    .btn-add-new-item {
        background-color: #eff6ff;
        color: #2563eb;
        border: 1px dashed #93c5fd;
        border-radius: 20px;
        padding: 6px 18px;
        font-size: 0.875rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .btn-add-new-item:hover {
        background-color: #dbeafe;
        border-color: #2563eb;
    }

    .summary-totals-box {
        text-align: right;
    }
    .summary-row {
        display: flex;
        justify-content: flex-end;
        align-items: center;
        margin-bottom: 8px;
        font-size: 0.95rem;
    }
    .summary-row .label {
        width: 120px;
        color: #6b7280;
        font-weight: 500;
        text-align: right;
        margin-right: 24px;
    }
    .summary-row .value {
        width: 100px;
        font-weight: 600;
        color: #111827;
        text-align: right;
    }
    .summary-row.total-row .label,
    .summary-row.total-row .value {
        font-size: 1.15rem;
        font-weight: 700;
        color: #111827;
    }

    .bottom-actions-bar {
        display: flex;
        justify-content: flex-end;
        align-items: center;
        gap: 12px;
        margin-top: 30px;
    }
</style>

<form action="{{ route('invoices.store') }}" method="POST" id="invoiceForm">
    @csrf

    <!-- Invoice Header Box -->
    <div class="invoice-card">
        <h5 class="fw-bold text-dark mb-0" id="invoiceHeaderNumber">Invoice #{{ $nextInvoiceNumber }}</h5>
    </div>

    <!-- Business Information Box -->
    <div class="invoice-card">
        <h6 class="fw-bold text-dark mb-4">Business Information</h6>
        <div class="row align-items-center">
            <div class="col-md-6">
                <img src="{{ asset('logo.png') }}" alt="Elite Guard Logo" height="60" style="object-fit: contain;">
            </div>
            <div class="col-md-6">
                <div class="billed-by-info">
                    <strong>Billed By:</strong><br>
                    <strong>Elite Guard Inc.</strong><br>
                    2104-3961 52 Ave NE, Calgary, AB T3J 0K7, Canada<br>
                    +14039090602
                </div>
            </div>
        </div>
    </div>

    <!-- Invoice title and summary Box -->
    <div class="invoice-card">
        <h6 class="fw-bold text-dark mb-4">Invoice title and summary</h6>
        
        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <label class="field-label">Invoice Title *</label>
                <input type="text" name="title" class="form-control form-control-custom" value="Invoice" required>
            </div>
            <div class="col-md-6">
                <label class="field-label">Summary</label>
                <input type="text" name="summary" class="form-control form-control-custom" placeholder="Summary">
            </div>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <label class="field-label">Invoice Date *</label>
                <input type="date" name="invoice_date" class="form-control form-control-custom" value="{{ date('Y-m-d') }}" required>
            </div>
            <div class="col-md-6">
                <label class="field-label">Payment Due *</label>
                <input type="date" name="due_date" class="form-control form-control-custom" value="{{ date('Y-m-d') }}" required>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-md-6">
                <label class="field-label">Invoice Number *</label>
                <input type="text" name="invoice_number" id="invoiceNumberInput" class="form-control form-control-custom bg-light" value="{{ $nextInvoiceNumber }}" readonly required>
            </div>
            <div class="col-md-6">
                <label class="field-label">PO/SO Number</label>
                <input type="text" name="po_so_number" class="form-control form-control-custom" placeholder="PO/SO Number">
            </div>
        </div>
    </div>

    <!-- Billed To Box -->
    <div class="invoice-card">
        <h6 class="fw-bold text-dark mb-4">Billed To</h6>
        <div class="row g-3">
            <div class="col-md-6">
                <label class="field-label">Client *</label>
                <select name="company_id" id="company_id" class="form-select form-control-custom" required>
                    <option value="">Client *</option>
                    @foreach($companies as $company)
                        <option value="{{ $company->id }}">{{ $company->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label class="field-label">Site</label>
                <select name="site_id" id="site_id" class="form-select form-control-custom">
                    <option value="">Site</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Products & Services Box -->
    <div class="invoice-card">
        <h6 class="fw-bold text-dark mb-4">Products & Services</h6>
        
        <div class="table-responsive mb-3">
            <table class="table table-products-services" id="itemsTable">
                <thead>
                    <tr>
                        <th width="40%">Product/Service</th>
                        <th width="15%">Quantity</th>
                        <th width="15%">Rate</th>
                        <th width="15%">Tax</th>
                        <th width="15%">Amount</th>
                        <th width="40"></th>
                    </tr>
                </thead>
                <tbody id="itemsContainer">
                    <!-- Rows will be dynamically added here -->
                </tbody>
            </table>
        </div>

        <div class="text-center py-2">
            <button type="button" class="btn-add-new-item" id="addNewItemBtn">
                <i class="fa fa-plus-circle"></i> Add New
            </button>
        </div>
    </div>

    <!-- Note & Summary Totals Box -->
    <div class="invoice-card">
        <div class="row g-4">
            <div class="col-md-6">
                <h6 class="fw-bold text-dark mb-3">Note</h6>
                <textarea name="notes" class="form-control form-control-custom" rows="4" placeholder="Notes"></textarea>
            </div>
            <div class="col-md-6">
                <div class="summary-totals-box pt-4">
                    <div class="summary-row">
                        <span class="label">Subtotal</span>
                        <span class="value" id="subtotalDisplay">$ 0.00</span>
                    </div>
                    <div class="summary-row">
                        <span class="label">Tax</span>
                        <span class="value" id="taxDisplay">$ 0.00</span>
                    </div>
                    <div class="summary-row total-row border-top pt-2">
                        <span class="label">Total</span>
                        <span class="value" id="totalDisplay">$ 0.00</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bottom Actions Bar -->
    <div class="bottom-actions-bar mb-5">
        <div class="form-check form-switch me-3 d-inline-flex align-items-center">
            <input class="form-check-input me-2" type="checkbox" name="send_email" value="1" id="sendEmail" checked style="width: 2.2em; height: 1.2em; cursor: pointer;">
            <label class="form-check-label fw-semibold text-dark" for="sendEmail" style="cursor: pointer;">
                <i class="fa fa-envelope text-primary me-1"></i> Send email to client
            </label>
        </div>
        <button type="button" class="btn btn-light rounded-pill px-4" id="previewBtn">Preview</button>
        <button type="submit" name="action_type" value="draft" class="btn btn-outline-secondary rounded-pill px-4">Save As Draft</button>
        <button type="submit" name="action_type" value="save" class="btn btn-primary rounded-pill px-4" style="background-color: #2563eb; border-color: #2563eb;">Save and Continue</button>
    </div>
</form>

<!-- New Product or Service Modal (Screenshot 1) -->
<div class="modal fade" id="newProductModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content rounded-3">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold">New Product or Service</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form id="newProductForm">
                    @csrf
                    <div class="mb-3">
                        <input type="text" name="name" id="modalProductName" class="form-control form-control-custom" placeholder="Name*" required>
                    </div>
                    <div class="mb-3">
                        <textarea name="description" id="modalProductDescription" class="form-control form-control-custom" rows="3" placeholder="Description*" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label font-size-12 text-muted mb-1">Price ($)</label>
                        <input type="number" step="0.01" min="0" name="price" id="modalProductPrice" class="form-control form-control-custom" value="0" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label font-size-12 text-muted mb-1">Tax</label>
                        <select name="tax_id" id="modalProductTax" class="form-select form-control-custom">
                            <option value="">Tax</option>
                            <option value="add_new_tax">Add New Tax</option>
                            @foreach($taxes as $tax)
                                <option value="{{ $tax->id }}" data-rate="{{ $tax->rate }}">{{ $tax->name }} ({{ number_format($tax->rate, 2) }}%)</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="text-end">
                        <button type="submit" class="btn btn-primary rounded-pill px-4" style="background-color: #2563eb; border-color: #2563eb;">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Add New Tax Modal (Screenshot 2) -->
<div class="modal fade" id="newTaxModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content rounded-3">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold">Add New Tax</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form id="newTaxForm">
                    @csrf
                    <div class="mb-3">
                        <label class="field-label">Tax Name *</label>
                        <input type="text" name="name" id="modalTaxName" class="form-control form-control-custom" placeholder="e.g. GST" required>
                    </div>
                    <div class="mb-3">
                        <label class="field-label">Tax Rate (%) *</label>
                        <input type="number" step="0.01" min="0" max="100" name="rate" id="modalTaxRate" class="form-control form-control-custom" placeholder="e.g. 5" required>
                    </div>
                    <div class="text-end">
                        <button type="submit" class="btn btn-primary rounded-pill px-4" style="background-color: #2563eb; border-color: #2563eb;">Create Tax</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Preview Modal -->
<div class="modal fade" id="invoicePreviewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content rounded-3">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Invoice Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" id="modalPreviewBody">
                <!-- Live preview injected by JS -->
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    let itemIndex = 0;
    let productsList = @json($products);
    let taxesList = @json($taxes);
    let $activeRowSelect = null;

    // Update Header Invoice Number live
    $('#invoiceNumberInput').on('input', function() {
        const val = $(this).val() || '{{ $nextInvoiceNumber }}';
        $('#invoiceHeaderNumber').text('Invoice #' + val);
    });

    // Dynamic Client -> Site Dropdown
    $('#company_id').on('change', function() {
        const companyId = $(this).val();
        const $siteDropdown = $('#site_id');
        $siteDropdown.html('<option value="">Site</option>');

        if (companyId) {
            $.ajax({
                url: '{{ url("invoices/sites-by-company") }}/' + companyId,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.sites && response.sites.length > 0) {
                        $.each(response.sites, function(index, site) {
                            $siteDropdown.append('<option value="' + site.id + '">' + site.name + '</option>');
                        });
                    }
                }
            });
        }
    });

    function renderProductOptions(selectedName = '') {
        let optionsHtml = '<option value="">Select Product or Service</option>';
        productsList.forEach(function(prod) {
            const selected = (prod.name === selectedName) ? 'selected' : '';
            const taxRate = prod.tax ? prod.tax.rate : 0;
            optionsHtml += `<option value="${prod.name}" data-price="${prod.price}" data-tax-rate="${taxRate}" ${selected}>${prod.name}</option>`;
        });
        optionsHtml += `<option value="create_new">Create New</option>`;
        return optionsHtml;
    }

    function addRow(selectedProduct = '', qty = 1, rate = 0, tax = 0) {
        const optionsHtml = renderProductOptions(selectedProduct);
        const rowId = `item_row_${itemIndex}`;
        const rowHtml = `
            <tr id="${rowId}" class="item-row">
                <td>
                    <select name="items[${itemIndex}][product_service]" class="form-select form-control-custom product-service-select" required>
                        ${optionsHtml}
                    </select>
                </td>
                <td>
                    <input type="number" step="0.01" min="0" name="items[${itemIndex}][quantity]" class="form-control form-control-custom qty-input" value="${qty}">
                </td>
                <td>
                    <input type="number" step="0.01" min="0" name="items[${itemIndex}][rate]" class="form-control form-control-custom rate-input" value="${rate}">
                </td>
                <td>
                    <input type="number" step="0.01" min="0" name="items[${itemIndex}][tax]" class="form-control form-control-custom tax-input" value="${tax}">
                </td>
                <td>
                    <input type="text" readonly name="items[${itemIndex}][amount]" class="form-control form-control-custom amount-input fw-semibold" value="0.00">
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm text-danger remove-row-btn"><i class="fa fa-times font-size-16"></i></button>
                </td>
            </tr>
        `;

        $('#itemsContainer').append(rowHtml);
        itemIndex++;
        recalculateTotals();
    }

    // Handle selection change in Product/Service dropdown
    $(document).on('change', '.product-service-select', function() {
        const val = $(this).val();
        const $row = $(this).closest('tr');

        if (val === 'create_new') {
            $activeRowSelect = $(this);
            $('#newProductForm')[0].reset();
            const modal = new bootstrap.Modal(document.getElementById('newProductModal'));
            modal.show();
            $(this).val('');
            return;
        }

        const selectedOption = $(this).find('option:selected');
        const price = parseFloat(selectedOption.data('price')) || 0;
        const taxRate = parseFloat(selectedOption.data('tax-rate')) || 0;
        const qty = parseFloat($row.find('.qty-input').val()) || 1;

        $row.find('.rate-input').val(price.toFixed(2));
        const calculatedTax = (qty * price) * (taxRate / 100);
        $row.find('.tax-input').val(calculatedTax.toFixed(2));

        const rowAmount = (qty * price) + calculatedTax;
        $row.find('.amount-input').val(rowAmount.toFixed(2));

        recalculateTotals();
    });

    // Handle "Add New Tax" selection inside Product Modal
    $('#modalProductTax').on('change', function() {
        if ($(this).val() === 'add_new_tax') {
            $('#newTaxForm')[0].reset();
            const newTaxModal = new bootstrap.Modal(document.getElementById('newTaxModal'));
            newTaxModal.show();
            $(this).val('');
        }
    });

    // Save Tax via AJAX
    $('#newTaxForm').on('submit', function(e) {
        e.preventDefault();
        $.ajax({
            url: '{{ route("taxes.storeAjax") }}',
            type: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                if (response.success && response.tax) {
                    const tax = response.tax;
                    taxesList.push(tax);
                    const newOption = `<option value="${tax.id}" data-rate="${tax.rate}" selected>${tax.name} (${parseFloat(tax.rate).toFixed(2)}%)</option>`;
                    $('#modalProductTax option[value="add_new_tax"]').after(newOption);
                    
                    const taxModalEl = document.getElementById('newTaxModal');
                    const modalObj = bootstrap.Modal.getInstance(taxModalEl);
                    if (modalObj) modalObj.hide();
                }
            },
            error: function(err) {
                alert('Error creating tax. Please try again.');
            }
        });
    });

    // Save Product via AJAX
    $('#newProductForm').on('submit', function(e) {
        e.preventDefault();
        $.ajax({
            url: '{{ route("products.storeAjax") }}',
            type: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                if (response.success && response.product) {
                    const prod = response.product;
                    productsList.push(prod);

                    // Update all product dropdowns
                    $('.product-service-select').each(function() {
                        const currentVal = $(this).val();
                        $(this).html(renderProductOptions(currentVal));
                    });

                    // Select product in active row and trigger calculations
                    if ($activeRowSelect) {
                        $activeRowSelect.val(prod.name).trigger('change');
                    }

                    const prodModalEl = document.getElementById('newProductModal');
                    const modalObj = bootstrap.Modal.getInstance(prodModalEl);
                    if (modalObj) modalObj.hide();
                }
            },
            error: function(err) {
                alert('Error creating product. Please ensure the name is unique.');
            }
        });
    });

    // Add initial row automatically on page load
    addRow();

    $('#addNewItemBtn').on('click', function() {
        addRow();
    });

    $(document).on('click', '.remove-row-btn', function() {
        $(this).closest('tr').remove();
        recalculateTotals();
    });

    $(document).on('input change', '.qty-input, .rate-input, .tax-input', function() {
        const $row = $(this).closest('tr');
        const qty = parseFloat($row.find('.qty-input').val()) || 0;
        const rate = parseFloat($row.find('.rate-input').val()) || 0;
        const tax = parseFloat($row.find('.tax-input').val()) || 0;

        const rowAmount = (qty * rate) + tax;
        $row.find('.amount-input').val(rowAmount.toFixed(2));

        recalculateTotals();
    });

    function recalculateTotals() {
        let subtotal = 0;
        let totalTax = 0;

        $('.item-row').each(function() {
            const qty = parseFloat($(this).find('.qty-input').val()) || 0;
            const rate = parseFloat($(this).find('.rate-input').val()) || 0;
            const tax = parseFloat($(this).find('.tax-input').val()) || 0;

            subtotal += (qty * rate);
            totalTax += tax;
        });

        const grandTotal = subtotal + totalTax;

        $('#subtotalDisplay').text('$ ' + subtotal.toFixed(2));
        $('#taxDisplay').text('$ ' + totalTax.toFixed(2));
        $('#totalDisplay').text('$ ' + grandTotal.toFixed(2));
    }

    // Modal Preview Handler
    $('#previewBtn').on('click', function() {
        const invoiceNum = $('#invoiceNumberInput').val();
        const clientName = $('#company_id option:selected').text();
        const siteName = $('#site_id option:selected').text();
        const invoiceDate = $('input[name="invoice_date"]').val();
        const dueDate = $('input[name="due_date"]').val();
        const subtotal = $('#subtotalDisplay').text();
        const tax = $('#taxDisplay').text();
        const total = $('#totalDisplay').text();

        let itemsPreviewHtml = '';
        $('.item-row').each(function() {
            const svc = $(this).find('.product-service-select').val() || 'N/A';
            const qty = $(this).find('.qty-input').val() || 0;
            const rate = $(this).find('.rate-input').val() || 0;
            const amt = $(this).find('.amount-input').val() || 0.00;

            itemsPreviewHtml += `
                <tr>
                    <td>${svc}</td>
                    <td class="text-center">${qty}</td>
                    <td class="text-end">$ ${parseFloat(rate).toFixed(2)}</td>
                    <td class="text-end">$ ${parseFloat(amt).toFixed(2)}</td>
                </tr>
            `;
        });

        const previewHtml = `
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <img src="{{ asset('logo.png') }}" height="50">
                </div>
                <div class="text-end">
                    <h4 class="fw-bold mb-0">INVOICE</h4>
                    <span class="text-muted">#${invoiceNum}</span>
                </div>
            </div>
            <hr>
            <div class="row mb-4">
                <div class="col-6">
                    <strong class="text-dark">Billed To:</strong><br>
                    ${clientName !== 'Client *' ? clientName : 'Client Name'}<br>
                    ${siteName !== 'Site' ? siteName : ''}
                </div>
                <div class="col-6 text-end">
                    <strong>Invoice Date:</strong> ${invoiceDate}<br>
                    <strong>Due Date:</strong> ${dueDate}
                </div>
            </div>
            <table class="table table-bordered mb-4">
                <thead>
                    <tr class="table-light">
                        <th>Product/Service</th>
                        <th class="text-center">Qty</th>
                        <th class="text-end">Rate</th>
                        <th class="text-end">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    ${itemsPreviewHtml}
                </tbody>
            </table>
            <div class="row justify-content-end">
                <div class="col-md-5">
                    <table class="table table-borderless">
                        <tr><td><strong>Subtotal:</strong></td><td class="text-end">${subtotal}</td></tr>
                        <tr><td><strong>Tax:</strong></td><td class="text-end">${tax}</td></tr>
                        <tr class="table-light fs-5"><td><strong>Total:</strong></td><td class="text-end"><strong>${total}</strong></td></tr>
                    </table>
                </div>
            </div>
        `;

        $('#modalPreviewBody').html(previewHtml);
        const modal = new bootstrap.Modal(document.getElementById('invoicePreviewModal'));
        modal.show();
    });
});
</script>
@endsection
