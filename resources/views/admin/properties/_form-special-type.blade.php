{{-- resources/views/admin/properties/_form-special-type.blade.php --}}
@php
    $specialTypes = [
        'Luxury Residence',
        'Executive Apartment',
        'Modern Home',
        'Coastal Villa',
        'Beachfront Apartment',
        'Ocean View Penthouse',
        'Sea-Facing Unit',
        'Contemporary Home',
        'Designer Apartment',
        'Exclusive Residence',
    ];
@endphp
<div class="form-group">
    <label for="special_type">Special Type</label>
    <select id="special_type" name="special_type">
        <option value="">None</option>
        @foreach($specialTypes as $type)
            <option value="{{ $type }}" @selected(old('special_type', $property->special_type ?? '') == $type)>{{ $type }}</option>
        @endforeach
    </select>
</div>