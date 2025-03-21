@extends('layouts.app')

@section('content')
<div class="container-section">
    <div class="row">
        <!-- Upper Box for Create Section -->
        <div class="col-md-12 mb-4">
            <div class="card">
                <div class="card-header">
                    <h2>Create Section</h2>
                </div>
                <div class="card-body">
                    <!-- Success & Error Alerts -->
                    @if(session('success'))
                        <div class="alert alert-success custom-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger custom-error">
                            <ul>
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Section Creation Form -->
                    <form action="{{ route('sections.store') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="role_name">Section Name:</label>
                            <input type="text" class="form-control" id="role_name" name="role_name" required>
                        </div>
                        <input type="hidden" name="category" value="Section">
                        <button type="submit" class="btn btn-primary" style="padding-top: 13px; padding-bottom: 13px;">Create Section</button>                   
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Lower Box for List of Sections -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h2>List of Sections</h2>
                </div>
                <div class="card-body">

                    <!-- Two-column layout for sections -->
                    <div class="row2">

                        @foreach ($sections as $section)
                        <div class="section-item1">
                            <input
                                type="checkbox"
                                class="section-checkbox"
                                data-id="{{ $section->id }}"
                                style="display: none;"
                            />
                            <input
                                type="text"
                                class="section-name1"
                                data-id="{{ $section->id }}"
                                value="{{ $section->name }}"
                                onblur="updateSection({{ $section->id }}, this.value)" 
                                onkeydown="if (event.key === 'Enter') { updateSection({{ $section->id }}, this.value); }"
                            />
                        </div>
                        @endforeach
                </div>


            </div>

                <!-- Actions: Select, Delete -->
                <div class="section-actions mb-3" style="margin-left: 10px;">
                        <button id="select-btn" class="btn btn-warning">Select</button>
                        <button id="delete-btn" class="btn btn-danger" style="display: none;">Delete Selected</button>
                        <button id="select-all-btn" class="btn btn-warning" style="display: none;">Select All</button>
                        <button id="unselect-all-btn" class="btn btn-warning" style="display: none;">Unselect All</button>
                </div>
        </div>
    </div>
</div>

<!-- JavaScript for interactivity -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const selectBtn = document.getElementById('select-btn');
    const deleteBtn = document.getElementById('delete-btn');
    const selectAllBtn = document.getElementById('select-all-btn');
    const unselectAllBtn = document.getElementById('unselect-all-btn');
    const sectionCheckboxes = document.querySelectorAll('.section-checkbox');

    let selecting = false;  // Track whether we are in select mode

    selectBtn.addEventListener('click', function () {
        selecting = !selecting;  // Toggle selecting mode
        toggleSelectMode(selecting);
    });

    deleteBtn.addEventListener('click', function () {
    const selectedSections = Array.from(sectionCheckboxes).filter(checkbox => checkbox.checked);
    const idsToDelete = selectedSections.map(checkbox => checkbox.dataset.id);

        if (idsToDelete.length > 0) {
            fetch('/admin/sections/delete', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ ids: idsToDelete })
            })
            .then(response => {
                if (response.ok) {
                    // Refresh the page to reflect changes and show success message
                    window.location.reload();
                } else {
                    console.error('Failed to delete sections.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }
    });




    selectAllBtn.addEventListener('click', function () {
        sectionCheckboxes.forEach(checkbox => checkbox.checked = true); // Select all checkboxes
    });

    unselectAllBtn.addEventListener('click', function () {
        sectionCheckboxes.forEach(checkbox => checkbox.checked = false); // Unselect all checkboxes
    });

    function toggleSelectMode(active) {
        sectionCheckboxes.forEach(checkbox => {
            checkbox.style.display = active ? 'inline' : 'none'; // Show or hide checkboxes
            if (active) {
                checkbox.checked = false; // Reset checkbox state when entering select mode
            }
        });
        selectBtn.textContent = active ? 'Cancel' : 'Select'; // Change to 'Cancel'
        selectBtn.style.backgroundColor = active ? 'gray' : ''; // Change button color to gray when active
        deleteBtn.style.display = active ? 'inline-block' : 'none'; // Show delete button in select mode
        selectAllBtn.style.display = active ? 'inline-block' : 'none'; // Show select all button in select mode
        unselectAllBtn.style.display = active ? 'inline-block' : 'none'; // Show unselect all button in select mode
    }

    // Function to update section
    window.updateSection = function (id, newName) {
        fetch(`/admin/sections/update/${id}`, { // Adjust the URL to your update route
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ name: newName }) // Send the updated value
        })
        .then(response => {
            if (!response.ok) {
                console.error('Failed to update section.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    };
});
</script>
@endsection
