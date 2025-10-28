{{-- resources/views/admin/properties/_form-visibility.blade.php --}}
<div class="form-group">
    <div class="form-check">
        <input type="hidden" name="is_visible" value="0"> {{-- Sends 0 if checkbox is unchecked --}}
        <input class="form-check-input" type="checkbox" id="is_visible" name="is_visible" value="1" @checked(old('is_visible', $property->is_visible ?? true))>
        <label class="form-check-label" for="is_visible">Visible</label>
    </div>
    <small class="form-text text-muted">If unchecked, only approved users can view the full property details.</small>
</div>

<div class="form-group">
    <div class="form-check">
        <input type="hidden" name="is_exclusive" value="0"> {{-- Sends 0 if checkbox is unchecked --}}
        <input class="form-check-input" type="checkbox" id="is_exclusive" name="is_exclusive" value="1" @checked(old('is_exclusive', $property->is_exclusive ?? false))>
        <label class="form-check-label" for="is_exclusive">Exclusive</label>
    </div>
    <small class="form-text text-muted">If checked, this property will only be visible to logged-in users.</small>
</div>