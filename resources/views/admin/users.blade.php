@extends('layouts.app')

@section('content')

<div id="users-table-container">
    <div class="table-data">
        <div class="order">
            <div class="head">
                <h3>User List</h3>
                <div class="search-container">
                    <input type="text" id="search-input" placeholder="Search by Name or Role" />
                    <button id="clearButton" style="display: none;">X</button>
                    <i class='bx bx-search' id="search-icon"></i>
                </div>
            </div>  

            <table id="usersTable">
                <thead>
                    <tr>
                        <th>Employee ID</th>
                        <th>Name</th>
                        <th>Role</th>
                        <th>User Level</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $selectedUser)
                        <tr class="user-summary" data-id="{{ $selectedUser->id }}" style="cursor: pointer;">
                            <td>{{ $selectedUser->employee_id }}</td>
                            <td>{{ $selectedUser->name }}</td>
                            <td>{{ $selectedUser->role }}</td>
                            <td>{{ $selectedUser->usertype }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Pagination Controls -->
            <div class="pagination">
                <button class="prev" onclick="changePage(-1)">❮ Prev</button>
                <span id="page-info">Page 1 of {{ ceil($users->count() / 5) }}</span>
                <button class="next" onclick="changePage(1)">Next ❯</button>
            </div>
        </div>
    </div>
</div>

<div id="user-details-container" style="display: none;">
    <!-- User details will be injected here -->
</div>

<script>
    let currentPage = 1;
    const usersPerPage = 5;

    function displayUsers() {
        const rows = document.querySelectorAll('#usersTable tbody tr');
        const totalUsers = rows.length;
        const totalPages = Math.ceil(totalUsers / usersPerPage);

        // Hide all rows
        rows.forEach(row => {
            row.style.display = 'none';
        });

        // Calculate the start and end index of the rows to display
        const start = (currentPage - 1) * usersPerPage;
        const end = start + usersPerPage;

        // Show only the rows for the current page
        for (let i = start; i < end && i < totalUsers; i++) {
            rows[i].style.display = '';
        }

        // Update page info
        document.getElementById('page-info').textContent = `Page ${currentPage} of ${totalPages}`;
        
        // Disable buttons if on first/last page
        document.querySelector('.prev').disabled = currentPage === 1;
        document.querySelector('.next').disabled = currentPage === totalPages;
    }

    function changePage(direction) {
        const totalUsers = document.querySelectorAll('#usersTable tbody tr').length;
        const totalPages = Math.ceil(totalUsers / usersPerPage);
        
        // Update current page
        currentPage += direction;
        if (currentPage < 1) currentPage = 1;
        if (currentPage > totalPages) currentPage = totalPages;

        displayUsers();
    }

    document.addEventListener('DOMContentLoaded', () => {
        displayUsers(); // Initial display of users

        $('.user-summary').on('click', function() {
            var userId = $(this).data('id'); // Get user ID from data-id attribute
            console.log("Fetching details for user ID:", userId); // Check the console for correct ID

            // AJAX request to fetch user details for editing
            $.ajax({
                url: '/admin/users/' + userId + '/edit', // Ensure this route matches your controller method
                type: 'GET',
                success: function(response) {
                    console.log("Received response:", response); // Check the response in the console
                    $('#users-table-container').hide(); // Hide the users table
                    $('#user-details-container').html(response).show(); // Show user details
                },
                error: function(xhr) {
                    console.error(xhr.responseText);
                    alert('Could not load user details. Please try again later.');
                }
            });
        });
    });
</script>


@endsection
