@extends('dashboardLayouts.main')
@section('title', 'Customers')
@section('breadcrumbTitle', 'Customer Listing')

@section('breadcrumbs')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item"><a href="{{ route('invoices.index') }}">Invoices</a></li>
<li class="breadcrumb-item active">Customers</li>
@endsection

@section('content')
<div class="row"><div class="col-12"><div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="card-title mb-0">Customers</h4>
        @if(Auth::user()->hasAdminPermission('customers', 'create'))
        <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#createCustomerModal">Add Customer</button>
        @endif
    </div>
    <div class="card-body">
        @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
        @if($errors->any())<div class="alert alert-danger">Please check the customer details and try again.</div>@endif
        <div class="table-responsive">
            <table id="custom-table" class="table table-striped table-bordered align-middle">
                <thead><tr><th>ID</th><th>Name</th><th>Company</th><th>Email</th><th>Phone</th><th>Location</th><th>Invoices</th><th>Actions</th></tr></thead>
                <tbody>
                @forelse($customers as $customer)
                    <tr>
                        <td>{{ $customer->id }}</td>
                        <td><strong>{{ $customer->name }}</strong></td>
                        <td>{{ $customer->company_name ?: 'N/A' }}</td>
                        <td>{{ $customer->email ?: 'N/A' }}</td>
                        <td>{{ $customer->phone ?: 'N/A' }}</td>
                        <td>{{ collect([$customer->city, $customer->province, $customer->country])->filter()->join(', ') ?: 'N/A' }}</td>
                        <td>{{ $customer->invoices_count }}</td>
                        <td class="text-nowrap">
                            @if(Auth::user()->hasAdminPermission('customers', 'view'))<a class="btn btn-sm btn-outline-info" href="{{ route('customers.show', $customer->id) }}" title="View"><i class="mdi mdi-eye"></i></a>@endif
                            @if(Auth::user()->hasAdminPermission('customers', 'update'))<button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editCustomer{{ $customer->id }}" title="Edit"><i class="mdi mdi-pencil"></i></button>@endif
                            @if(Auth::user()->hasAdminPermission('customers', 'delete'))<form class="d-inline" method="POST" action="{{ route('customers.delete', $customer->id) }}" onsubmit="return confirm('Delete this customer? Existing invoices will keep working.');">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger" title="Delete"><i class="mdi mdi-delete"></i></button></form>@endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="text-center text-muted py-4">No customers yet. Add a customer to use them on invoices.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div></div></div>

@if(Auth::user()->hasAdminPermission('customers', 'update'))
@foreach($customers as $customer)
<div class="modal fade" id="editCustomer{{ $customer->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg"><div class="modal-content">
        <form method="POST" action="{{ route('customers.update', $customer->id) }}">@csrf @method('PUT')
            <div class="modal-header"><h5 class="modal-title">Edit Customer</h5><button class="btn-close" type="button" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">@include('admin.customers._fields', ['customer' => $customer])</div>
            <div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button><button class="btn btn-primary">Save Changes</button></div>
        </form>
    </div></div>
</div>
@endforeach
@endif

@if(Auth::user()->hasAdminPermission('customers', 'create'))
<div class="modal fade" id="createCustomerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg"><div class="modal-content">
        <form method="POST" action="{{ route('customers.store') }}">@csrf
            <div class="modal-header"><h5 class="modal-title">Add Customer</h5><button class="btn-close" type="button" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">@include('admin.customers._fields', ['customer' => null])</div>
            <div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button><button class="btn btn-primary">Save Customer</button></div>
        </form>
    </div></div>
</div>
@endif
@endsection
