@extends('dashboardLayouts.main')
@section('title', $customer->name)
@section('breadcrumbTitle', 'Customer Details')
@section('breadcrumbs')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item"><a href="{{ route('customers.index') }}">Customers</a></li>
<li class="breadcrumb-item active">{{ $customer->name }}</li>
@endsection
@section('content')
<div class="card"><div class="card-header d-flex justify-content-between align-items-center"><h4 class="mb-0">{{ $customer->name }}</h4><a class="btn btn-outline-secondary" href="{{ route('customers.index') }}">Back to Customers</a></div>
<div class="card-body"><div class="row g-3">
@foreach(['Company' => 'company_name', 'Email' => 'email', 'Phone' => 'phone', 'Tax / Business ID' => 'tax_id', 'Address' => 'address', 'City' => 'city', 'State / Province' => 'province', 'Postal Code' => 'postal_code', 'Country' => 'country', 'Notes' => 'notes'] as $label => $field)
<div class="col-md-6"><div class="text-muted small">{{ $label }}</div><div>{{ $customer->{$field} ?: '—' }}</div></div>
@endforeach
<div class="col-md-6"><div class="text-muted small">Invoices</div><div>{{ $customer->invoices->count() }}</div></div>
</div></div></div>
@endsection
