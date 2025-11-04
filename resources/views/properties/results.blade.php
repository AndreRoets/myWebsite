@extends('layouts.app')

@section('title', 'Property Search Results')

@section('content')
<div class="container" style="padding-top: 2rem; padding-bottom: 2rem;">
    <h1>Property Search Results</h1>

    <div class="search-results">
        @if ($properties->isEmpty())
            <p>No properties found matching your criteria.</p>
        @else
            <div class="property-listings">
                @foreach ($properties as $property)
                    <div class="property-card">
                        <a href="{{ route('properties.show', $property->slug) }}">
                            <img src="{{ $property->image_url ?? asset('images/property-placeholder.png') }}" alt="{{ $property->title }}">
                            <div class="property-card-body">
                                <h3>{{ $property->title }}</h3>
                                <p class="price">${{ number_format($property->price) }}</p>
                                <p class="location">{{ $property->location }}</p>
                                <div class="features">
                                    <span><i class="fas fa-bed"></i> {{ $property->bedrooms }}</span>
                                    <span><i class="fas fa-bath"></i> {{ $property->bathrooms }}</span>
                                </div>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>

            <div class="pagination-container">
                {{ $properties->appends(request()->query())->links() }}
            </div>
        @endif
    </div>

    @auth
    <div class="save-search-container" style="margin-top: 2rem;">
        <button class="btn btn-primary" id="saveSearchButton">Save This Search</button>
    </div>

    <!-- Save Search Modal -->
    <div id="saveSearchModal" class="save-search-modal" style="display: none;">
        <div class="modal-content">
            <span class="close-button">&times;</span>
            <h2>Save Search</h2>
            <p>Enter a name for your current search criteria to save it to your profile.</p>
            <div class="form-group">
                <label for="searchNameInput">Search Name</label>
                <input type="text" id="searchNameInput" class="form-control" placeholder="e.g., 'My Dream Beach House'">
                <div id="saveSearchError" class="field-error" style="display: none;"></div>
            </div>
            <div class="modal-actions">
                <button id="cancelSaveButton" class="btn btn-secondary">Cancel</button>
                <button id="confirmSaveButton" class="btn btn-primary">Save</button>
            </div>
        </div>
    </div>

    @endauth
</div>

<style>
.property-listings {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 1.5rem;
}
.property-card {
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    overflow: hidden;
    transition: transform 0.2s;
}
.property-card:hover {
    transform: translateY(-5px);
}
.property-card a {
    text-decoration: none;
    color: inherit;
}
.property-card img {
    width: 100%;
    height: 200px;
    object-fit: cover;
}
.property-card-body {
    padding: 1rem;
}
.property-card-body h3 {
    margin-top: 0;
    font-size: 1.25rem;
}
.price {
    font-size: 1.5rem;
    font-weight: 600;
    color: #007bff;
}
.location {
    color: #6c757d;
}
.features {
    display: flex;
    justify-content: space-between;
    margin-top: 1rem;
}
.pagination-container {
    margin-top: 2rem;
}

/* Save Search Modal Styles */
.save-search-modal {
    position: fixed;
    z-index: 1050;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: hidden;
    background-color: rgba(0,0,0,0.5);
    display: flex;
    align-items: center;
    justify-content: center;
}
.save-search-modal .modal-content {
    background-color: #fefefe;
    padding: 2rem;
    border: 1px solid #888;
    width: 90%;
    max-width: 500px;
    border-radius: 8px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.3);
    position: relative;
}
.save-search-modal .close-button {
    color: #aaa;
    position: absolute;
    top: 10px;
    right: 20px;
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
}
.save-search-modal .close-button:hover,
.save-search-modal .close-button:focus {
    color: black;
}
.save-search-modal .form-group {
    margin-bottom: 1rem;
}
.save-search-modal label {
    display: block;
    margin-bottom: .5rem;
}
.save-search-modal .form-control {
    width: 100%;
    padding: .75rem;
    border: 1px solid #ccc;
    border-radius: 4px;
}
.save-search-modal .field-error {
    color: #dc3545;
    font-size: 0.875em;
    margin-top: 0.25rem;
}
.save-search-modal .modal-actions {
    text-align: right;
    margin-top: 1.5rem;
    display: flex;
    justify-content: flex-end;
    gap: 0.5rem;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    @auth
    const saveSearchButton = document.getElementById('saveSearchButton');
    const modal = document.getElementById('saveSearchModal');
    const closeButton = modal.querySelector('.close-button');
    const cancelButton = document.getElementById('cancelSaveButton');
    const confirmSaveButton = document.getElementById('confirmSaveButton');
    const searchNameInput = document.getElementById('searchNameInput');
    const errorDiv = document.getElementById('saveSearchError');

    function showModal() {
        modal.style.display = 'flex';
        searchNameInput.value = '';
        errorDiv.style.display = 'none';
        searchNameInput.focus();
    }

    function hideModal() {
        modal.style.display = 'none';
    }

    if (saveSearchButton) {
        saveSearchButton.addEventListener('click', showModal);
    }

    closeButton.addEventListener('click', hideModal);
    cancelButton.addEventListener('click', hideModal);
    window.addEventListener('click', function(event) {
        if (event.target == modal) {
            hideModal();
        }
    });

    confirmSaveButton.addEventListener('click', function () {
        const searchName = searchNameInput.value.trim();
        if (searchName) {
            errorDiv.style.display = 'none';
            confirmSaveButton.disabled = true;
            confirmSaveButton.textContent = 'Saving...';

            const filters = @json(request()->except('page'));
            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            fetch('{{ route('saved-searches.store') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    name: searchName,
                    filters: filters
                })
            })
            .then(async (res) => {
                const data = await res.json();
                if (!res.ok) {
                    const error = new Error(data.message || `HTTP error! status: ${res.status}`);
                    error.errors = data.errors;
                    throw error;
                }
                return data;
            })
            .then(data => {
                // On success, redirect to the profile page to view the saved search.
                window.location.href = '{{ route('profile.show') }}';
            })
            .catch(error => {
                console.error('Error:', error);
                if (error.errors && error.errors.name) {
                    errorDiv.textContent = error.errors.name[0];
                } else {
                    errorDiv.textContent = error.message || 'An unknown error occurred.';
                }
                errorDiv.style.display = 'block';
            })
            .finally(() => {
                confirmSaveButton.disabled = false;
                confirmSaveButton.textContent = 'Save';
            });
        } else {
            errorDiv.textContent = 'Please enter a name for the search.';
            errorDiv.style.display = 'block';
        }
    });

    // This is the old logic, which I've commented out and replaced above.
    /* if (saveSearchButton) {
        saveSearchButton.addEventListener('click', function () {
            const searchName = prompt('Enter a name for this search:');
            if (searchName) {
                const filters = @json(request()->except('page'));
                const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

                fetch('{{ route('saved-searches.store') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        name: searchName,
                        filters: filters
                    })
                })
                .then(async (res) => {
                    if (!res.ok) {
                        const contentType = res.headers.get('content-type') || '';
                        let errText = `HTTP error! status: ${res.status}`;
                        if (contentType.includes('application/json')) {
                            const errJson = await res.json();
                            errText = errJson.message || errText;
                        } else {
                            // For non-JSON responses, like HTML error pages
                            console.error("Server returned a non-JSON response.");
                        }
                        throw new Error(errText);
                    }
                    return res.json();
                })
                .then(data => {
                    if (data.success) {
                        alert(data.message || 'Search saved successfully!');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error saving search: ' + error.message);
                });
            }
        });
    } */
    @endauth
});
</script>
@endsection
