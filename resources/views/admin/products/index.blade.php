@extends('dashboardLayouts.main')
@section('title', 'Products & Services')

@section('breadcrumbTitle', 'Product / Service Listing')

@section('breadcrumbs')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item active">Products & Services</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header justify-content-between d-flex align-items-center">
                <h4 class="card-title shine">Products Table</h4>
                @if(Auth::user()->hasAdminPermission('products', 'create'))
                <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#createProductModal">
                    Create <i class="mdi mdi-arrow-right align-middle"></i>
                </button>
                @endif
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="mdi mdi-check-all me-2"></i> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="mdi mdi-alert-circle me-2"></i> {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="table-responsive">
                    <table id="custom-table" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Description</th>
                                <th>Price ($)</th>
                                <th>Tax</th>
                                <th>Created At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($products as $product)
                            <tr>
                                <td>{{ $product->id }}</td>
                                <td><strong>{{ $product->name }}</strong></td>
                                <td>{{ $product->description ?: 'N/A' }}</td>
                                <td><span class="fw-bold text-dark">$ {{ number_format($product->price, 2) }}</span></td>
                                <td>
                                    @if($product->tax)
                                        <span class="badge bg-info text-dark fs-6">{{ $product->tax->name }} ({{ number_format($product->tax->rate, 2) }}%)</span>
                                    @else
                                        <span class="badge bg-secondary fs-6">No Tax</span>
                                    @endif
                                </td>
                                <td>{{ $product->created_at ? $product->created_at->format('Y-m-d') : 'N/A' }}</td>
                                <td>
                                    <div class="btn-group" role="group" aria-label="Product Actions">
                                        @if(Auth::user()->hasAdminPermission('products', 'update'))
                                        <button class="editBtn me-2" data-bs-toggle="modal" data-bs-target="#editProductModal{{ $product->id }}" title="Edit Product">
                                            <svg height="1em" viewBox="0 0 512 512">
                                                <path d="M410.3 231l11.3-11.3-33.9-33.9-62.1-62.1L291.7 89.8l-11.3 11.3-22.6 22.6L58.6 322.9c-10.4 10.4-18 23.3-22.2 37.4L1 480.7c-2.5 8.4-.2 17.5 6.1 23.7s15.3 8.5 23.7 6.1l120.3-35.4c14.1-4.2 27-11.8 37.4-22.2L387.7 253.7 410.3 231zM160 399.4l-9.1 22.7c-4 3.1-8.5 5.4-13.3 6.9L59.4 452l23-78.1c1.4-4.9 3.8-9.4 6.9-13.3l22.7-9.1v32c0 8.8 7.2 16 16 16h32zM362.7 18.7L348.3 33.2 325.7 55.8 314.3 67.1l33.9 33.9 62.1 62.1 33.9 33.9 11.3-11.3 22.6-22.6 14.5-14.5c25-25 25-65.5 0-90.5L453.3 18.7c-25-25-65.5-25-90.5 0zm-47.4 168l-144 144c-6.2 6.2-16.4 6.2-22.6 0s-6.2-16.4 0-22.6l144-144c6.2-6.2 16.4-6.2 22.6 0s6.2 16.4 0 22.6z"></path>
                                            </svg>
                                        </button>
                                        @endif
                                        @if(Auth::user()->hasAdminPermission('products', 'delete'))
                                        <a href="{{ route('products.delete', $product->id) }}" class="bin-button ml-1" data-bs-toggle="tooltip" title="Delete Product" onclick="return confirm('Are you sure you want to delete this product?')">
                                            <svg class="bin-top" viewBox="0 0 39 7" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <line y1="5" x2="39" y2="5" stroke="white" stroke-width="4"></line>
                                                <line x1="12" y1="1.5" x2="26.0357" y2="1.5" stroke="white" stroke-width="3"></line>
                                            </svg>
                                            <svg class="bin-bottom" viewBox="0 0 33 39" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <mask id="path-1-inside-1_8_19" fill="white">
                                                    <path d="M0 0H33V35C33 37.2091 31.2091 39 29 39H4C1.79086 39 0 37.2091 0 35V0Z"></path>
                                                </mask>
                                                <path d="M0 0H33H0ZM37 35C37 39.4183 33.4183 43 29 43H4C-0.418278 43 -4 39.4183 -4 35H4H29H37ZM4 43C-0.418278 43 -4 39.4183 -4 35V0H4V35V43ZM37 0V35C37 39.4183 33.4183 43 29 43V35V0H37Z" fill="white" mask="url(#path-1-inside-1_8_19)"></path>
                                                <path d="M12 6L12 29" stroke="white" stroke-width="4"></path>
                                                <path d="M21 6V29" stroke="white" stroke-width="4"></path>
                                            </svg>
                                        </a>
                                        @endif
                                    </div>

                                    <!-- Edit Product Modal -->
                                    <div class="modal fade" id="editProductModal{{ $product->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form action="{{ route('products.update', $product->id) }}" method="POST">
                                                    @csrf
                                                    <div class="modal-header">
                                                        <h5 class="modal-title fw-bold">Edit Product / Service</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="mb-3">
                                                            <label class="form-label fw-semibold">Name *</label>
                                                            <input type="text" name="name" class="form-control" value="{{ $product->name }}" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label fw-semibold">Description *</label>
                                                            <textarea name="description" class="form-control" rows="3" required>{{ $product->description }}</textarea>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label fw-semibold">Price ($) *</label>
                                                            <input type="number" step="0.01" min="0" name="price" class="form-control" value="{{ $product->price }}" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label fw-semibold">Tax</label>
                                                            <select name="tax_id" class="form-select">
                                                                <option value="">No Tax</option>
                                                                @foreach($taxes as $tax)
                                                                    <option value="{{ $tax->id }}" {{ $product->tax_id == $tax->id ? 'selected' : '' }}>{{ $tax->name }} ({{ number_format($tax->rate, 2) }}%)</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-primary rounded-pill">Update Product</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Create Product Modal -->
<div class="modal fade" id="createProductModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('products.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">New Product or Service</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Name *</label>
                        <input type="text" name="name" class="form-control" placeholder="Name*" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Description *</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Description*" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Price ($)</label>
                        <input type="number" step="0.01" min="0" name="price" class="form-control" value="0" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tax</label>
                        <select name="tax_id" class="form-select">
                            <option value="">Tax</option>
                            @foreach($taxes as $tax)
                                <option value="{{ $tax->id }}">{{ $tax->name }} ({{ number_format($tax->rate, 2) }}%)</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="submit" class="btn btn-primary rounded-pill px-4" style="background-color: #2563eb; border-color: #2563eb;">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
