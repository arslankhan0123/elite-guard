<div class="row g-3">
    <div class="col-md-6"><label class="form-label">Customer Name *</label><input class="form-control" name="name" required maxlength="255" value="{{ old('name', $customer?->name) }}"></div>
    <div class="col-md-6"><label class="form-label">Company / Organization</label><input class="form-control" name="company_name" maxlength="255" value="{{ old('company_name', $customer?->company_name) }}"></div>
    <div class="col-md-6"><label class="form-label">Email</label><input class="form-control" type="email" name="email" maxlength="255" value="{{ old('email', $customer?->email) }}"></div>
    <div class="col-md-6"><label class="form-label">Phone</label><input class="form-control" name="phone" maxlength="50" value="{{ old('phone', $customer?->phone) }}"></div>
    <div class="col-md-6"><label class="form-label">Tax / Business ID</label><input class="form-control" name="tax_id" maxlength="100" value="{{ old('tax_id', $customer?->tax_id) }}"></div>
    <div class="col-md-6"><label class="form-label">Address</label><input class="form-control" name="address" maxlength="255" value="{{ old('address', $customer?->address) }}"></div>
    <div class="col-md-4"><label class="form-label">City</label><input class="form-control" name="city" maxlength="100" value="{{ old('city', $customer?->city) }}"></div>
    <div class="col-md-4"><label class="form-label">State / Province</label><input class="form-control" name="province" maxlength="100" value="{{ old('province', $customer?->province) }}"></div>
    <div class="col-md-4"><label class="form-label">Postal Code</label><input class="form-control" name="postal_code" maxlength="30" value="{{ old('postal_code', $customer?->postal_code) }}"></div>
    <div class="col-md-6"><label class="form-label">Country</label><input class="form-control" name="country" maxlength="100" value="{{ old('country', $customer?->country) }}"></div>
    <div class="col-12"><label class="form-label">Notes</label><textarea class="form-control" name="notes" rows="3">{{ old('notes', $customer?->notes) }}</textarea></div>
</div>
