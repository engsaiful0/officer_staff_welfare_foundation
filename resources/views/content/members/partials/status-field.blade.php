@php
    use App\Enums\MemberStatus;
    $current = old('status', $selected ?? MemberStatus::ACTIVE->value);
@endphp
<div class="col-md-4 mb-3">
    <label for="status" class="form-label">Account status <span class="text-danger">*</span></label>
    <select class="form-select" id="status" name="status" required>
        @foreach(MemberStatus::cases() as $case)
            <option value="{{ $case->value }}" {{ (string) $current === $case->value ? 'selected' : '' }}>
                {{ $case->label() }}
            </option>
        @endforeach
    </select>
    <div class="invalid-feedback"></div>
</div>
