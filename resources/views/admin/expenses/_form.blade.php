@csrf
@isset($expense)
    @method('PUT')
@endisset

<div class="card shadow-sm" style="max-width:640px">
    <div class="card-body row g-3">
        <div class="col-md-6">
            <label class="form-label">Category <span class="text-danger">*</span></label>
            <select name="expense_category_id" class="form-select" required>
                <option value="">Select Category</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" @selected(old('expense_category_id', $expense->expense_category_id ?? '') == $category->id)>{{ $category->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-6">
            <label class="form-label">Expense Date <span class="text-danger">*</span></label>
            <input type="date" name="expense_date" class="form-control"
                   value="{{ old('expense_date', isset($expense) ? $expense->expense_date->format('Y-m-d') : now()->toDateString()) }}" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">Amount <span class="text-danger">*</span></label>
            <input type="number" step="0.01" min="0.01" name="amount" class="form-control @error('amount') is-invalid @enderror"
                   value="{{ old('amount', $expense->amount ?? '') }}" required>
            @error('amount') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="col-md-6">
            <label class="form-label">Paid To</label>
            <input type="text" name="paid_to" class="form-control" value="{{ old('paid_to', $expense->paid_to ?? '') }}">
        </div>
        <div class="col-md-6">
            <label class="form-label">Payment Method</label>
            <select name="payment_method_id" class="form-select">
                <option value="">— Not specified —</option>
                @foreach($methods as $method)
                    <option value="{{ $method->id }}" @selected(old('payment_method_id', $expense->payment_method_id ?? '') == $method->id)>{{ $method->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-6">
            <label class="form-label">Reference Number</label>
            <input type="text" name="reference_number" class="form-control" value="{{ old('reference_number', $expense->reference_number ?? '') }}">
        </div>
        <div class="col-12">
            <label class="form-label">Remarks</label>
            <textarea name="remarks" class="form-control" rows="2">{{ old('remarks', $expense->remarks ?? '') }}</textarea>
        </div>
        <div class="col-12">
            <label class="form-label">Attachment (receipt/invoice)</label>
            <input type="file" name="attachment" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
            @isset($expense)
                @if($expense->attachment_path)
                    <div class="form-text"><a href="{{ Storage::url($expense->attachment_path) }}" target="_blank">View current attachment</a></div>
                @endif
            @endisset
        </div>
    </div>
</div>

<div class="d-flex justify-content-end gap-2 mt-3" style="max-width:640px">
    <a href="{{ route('admin.expenses.index') }}" class="btn btn-outline-secondary">Cancel</a>
    <button type="submit" class="btn btn-success">
        <i class="bi bi-check-lg me-1"></i> {{ isset($expense) ? 'Update Expense' : 'Save Expense' }}
    </button>
</div>
