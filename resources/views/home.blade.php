@extends('layouts.app')

@section('title', 'Home Finders Coastal - Discover the Exceptional')

@section('content')
<main>
    <section class="hero">
        <div class="hero-content">
            <h1>Discover the Exceptional</h1>
            <p>Exceptional homes. Extraordinary views. Exclusive living</p>
            <a href="{{ route('properties.index') }}" class="hero-btn">View Properties</a>
            
        </div>
    </section>

   <section class="categories">
<div class="container category-grid">
<div class="category-item">
  <div class="category-item-inner">
    <h3>Waterfront Estates</h3>
    <p>Exclusive seaside luxury with unmatched serenity.</p>
  </div>
</div>
<div class="category-item">
  <div class="category-item-inner">
    <h3>Urban Penthouses</h3>
    <p>Experience the skyline from the top — modern & elite.</p>
  </div>
</div>
<div class="category-item">
  <div class="category-item-inner">
    <h3>Exclusive Villas</h3>
    <p>Private, elegant homes for the most discerning buyers.</p>
  </div>
</div>
</div>
</section>

    
</main>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Define the API endpoint to fetch all property data
    const propertiesApiUrl = '/api/properties';
    // Define API endpoints for fetching filter options
    const filterOptionsApiUrl = '/api/properties/filter-options';

    // Select the dropdown elements
    const typeDropdown = document.getElementById('type');
    const specialTypeDropdown = document.getElementById('special_type');
    /**
     * Populates a <select> element with options.
     * @param {string} elementId - The ID of the dropdown element.
     * @param {Array<Object|string>} options - An array of items to use as options.
     * @param {string|null} valueField - The property name for the option value.
     * @param {string|null} textField - The property name for the option text.
     */
    function populateDropdown(elementId, options, valueField = null, textField = null) {
        const dropdown = document.getElementById(elementId);
        if (!dropdown) return;

        if (options.length === 0) {
            const option = new Option('No options available', '');
            option.disabled = true;
            dropdown.add(option);
            return;
        }

        options.forEach(item => {
            const value = valueField ? item[valueField] : item;
            let text = textField ? item[textField] : item;
            // Capitalize the first letter for display
            text = text.charAt(0).toUpperCase() + text.slice(1);
            const option = new Option(text, value);
            dropdown.add(option);
        });
    }

    /**
     * Fetches property data and populates the filter dropdowns.
     * Fetches all filter options from the backend and populates the dropdowns.
     */
    async function populateFilterOptions() {
        try {
            const response = await fetch(propertiesApiUrl);
            const response = await fetch(filterOptionsApiUrl);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            const properties = await response.json();
            const options = await response.json();

            // Extract unique values for each filter type
            const types = getUniqueValues(properties, 'type');
            const specialTypes = getUniqueValues(properties, 'special_type');
            // Populate dropdowns with data from the API
            populateDropdown('agent_id', options.agents || [], 'id', 'name');
            populateDropdown('status', options.statuses || []);
            populateDropdown('type', options.types || []);
            populateDropdown('special_type', options.special_types || []);
            populateDropdown('city', options.cities || []);
            populateDropdown('suburb', options.suburbs || []);

            // Populate the dropdowns with the unique values
            populateDropdown(typeDropdown, types);
            populateDropdown(specialTypeDropdown, specialTypes);

        } catch (error) {
            console.error("Could not fetch property data for filters:", error);
            // Optionally, display an error message to the user
            console.error("Could not fetch filter options:", error);
            // You could display a general error message to the user here
        }
    }

    /**
     * Extracts unique, non-empty values for a given key from an array of objects.
     * @param {Array<Object>} items - The array of property objects.
     * @param {string} key - The key to extract unique values from (e.g., 'type').
     * @returns {Array<string>} - A sorted array of unique values.
     */
    function getUniqueValues(items, key) {
        const uniqueValues = new Set(items.map(item => item[key]).filter(Boolean));
        return Array.from(uniqueValues).sort();
    }

    /**
     * Populates a <select> element with options.
     * @param {HTMLSelectElement} dropdown - The dropdown element to populate.
     * @param {Array<string>} options - An array of strings to use as options.
     */
    function populateDropdown(dropdown, options) {
        options.forEach(optionValue => {
            const option = new Option(optionValue.charAt(0).toUpperCase() + optionValue.slice(1), optionValue);
            dropdown.add(option);
        });
    }

    // Run the population logic when the page is loaded
    // Fetch and populate filters when the DOM is ready
    populateFilterOptions();
});
</script>
@endpush
@endsection