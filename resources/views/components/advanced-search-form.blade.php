<div class="advanced-search-form">
    <form id="propertySearchForm">
        <div class="form-group">
            <label for="property_type">Property Type:</label>
            <select id="property_type" name="property_type" class="form-control">
                <option value="">Any</option>
                <option value="house">House</option>
                <option value="apartment">Apartment</option>
                <option value="condo">Condo</option>
                <option value="land">Land</option>
            </select>
        </div>

        <div class="form-group">
            <label for="price_min">Min Price:</label>
            <input type="number" id="price_min" name="price_min" class="form-control" placeholder="Min Price">
        </div>

        <div class="form-group">
            <label for="price_max">Max Price:</label>
            <input type="number" id="price_max" name="price_max" class="form-control" placeholder="Max Price">
        </div>

        <div class="form-group">
            <label for="bedrooms">Bedrooms:</label>
            <input type="number" id="bedrooms" name="bedrooms" class="form-control" min="0" placeholder="Any">
        </div>

        <div class="form-group">
            <label for="bathrooms">Bathrooms:</label>
            <input type="number" id="bathrooms" name="bathrooms" class="form-control" min="0" placeholder="Any">
        </div>

        <div class="form-group">
            <label for="location">Location:</label>
            <input type="text" id="location" name="location" class="form-control" placeholder="e.g., New York, London">
        </div>

        <div class="form-group">
            <button type="button" id="liveSearchButton" class="btn btn-secondary">Live Search</button>
            <button type="submit" id="viewResultsPageButton" class="btn btn-primary">View Results Page</button>
        </div>
    </form>

    <div id="searchResults" style="margin-top: 20px;">
        <h3>Live Search Results</h3>
        <div id="propertyList">
            <p>Adjust filters to see live results...</p>
        </div>
    </div>
</div>

<style>
.advanced-search-form .form-group {
    margin-bottom: 1rem;
}
.advanced-search-form label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 600;
}
.advanced-search-form .form-control {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid #ccc;
    border-radius: 4px;
    box-sizing: border-box;
}
.advanced-search-form .btn-primary {
    width: auto;
    padding: 0.75rem 1.5rem;
}
.advanced-search-form .btn-secondary {
    background-color: #6c757d;
    color: #fff;
    margin-right: 10px;
}
.advanced-search-form .btn-secondary:hover {
    background-color: #5a6268;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const propertySearchForm = document.getElementById('propertySearchForm');
    const propertyList = document.getElementById('propertyList');
    const liveSearchButton = document.getElementById('liveSearchButton');
    const viewResultsPageButton = document.getElementById('viewResultsPageButton');

    function fetchProperties() {
        const formData = new FormData(propertySearchForm);
        const params = new URLSearchParams(formData).toString();

        fetch(`/properties?${params}`)
            .then(response => response.json())
            .then(data => {
                propertyList.innerHTML = '';
                if (data.length > 0) {
                    data.forEach(property => {
                        const propertyCard = `
                            <div class="property-card" style="border: 1px solid #eee; padding: 1rem; margin-bottom: 1rem; border-radius: 5px;">
                                <h4>${property.title}</h4>
                                <p>Price: $${property.price}</p>
                                <p>Bedrooms: ${property.bedrooms}</p>
                                <p>Bathrooms: ${property.bathrooms}</p>
                                <p>Location: ${property.location}</p>
                            </div>
                        `;
                        propertyList.innerHTML += propertyCard;
                    });
                } else {
                    propertyList.innerHTML = '<p>No properties found matching your criteria.</p>';
                }
            })
            .catch(error => {
                console.error('Error fetching properties:', error);
                propertyList.innerHTML = '<p>Error loading properties.</p>';
            });
    }

    // Trigger live search on button click
    liveSearchButton.addEventListener('click', function (e) {
        e.preventDefault(); // Prevent form submission
        fetchProperties();
    });

    // Submit form to properties page on 'View Results Page' button click
    viewResultsPageButton.addEventListener('click', function (e) {
        // The form will naturally submit to its action attribute
        // We need to set the action and method for the form
        propertySearchForm.action = '{{ route('properties.index') }}';
        propertySearchForm.method = 'GET';
        propertySearchForm.submit();
    });

    // Optional: Fetch properties on filter change (e.g., input, select change)
    propertySearchForm.querySelectorAll('input, select').forEach(element => {
        element.addEventListener('change', fetchProperties);
        element.addEventListener('keyup', fetchProperties);
    });

    // Initial load of properties (optional)
    // fetchProperties();
});
</script>
