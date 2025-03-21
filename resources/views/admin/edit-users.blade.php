

<div class="container mt-5">
    <h2>Edit User Details</h2>

    <form action="{{ route('users.update', $selectedUser->id) }}" method="POST">
        @csrf
        @method('PATCH')
      
        <!-- Employee ID -->
        <div class="form-group">
            <label for="employee_id">Employee ID:</label>
            <input type="text" id="employee_id" name="employee_id" class="form-control" value="{{ old('employee_id', $selectedUser->employee_id) }}" required>
        </div>

        <!-- Name -->
        <div class="form-group">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" class="form-control" value="{{ old('name', $selectedUser->name) }}" required>
        </div>

        <!-- Email -->
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" class="form-control" value="{{ old('email', $selectedUser->email) }}" required>
        </div>

        <!-- User Type -->
        <div class="form-group">
            <label for="usertype">User Type:</label>
            <select id="usertype" name="usertype" class="form-control">
                <option value="admin" {{ $selectedUser->usertype == 'admin' ? 'selected' : '' }}>Admin</option>
                <option value="boss" {{ $selectedUser->usertype == 'boss' ? 'selected' : '' }}>Boss</option>
                <option value="user" {{ $selectedUser->usertype == 'user' ? 'selected' : '' }}>User</option>
                <option value="section" {{ $selectedUser->usertype == 'section' ? 'selected' : '' }}>Section</option>
            </select>
        </div>

        <!-- Role -->
        <div class="form-group">
            <label for="role">Role:</label>
            <select id="role" name="role" class="form-control" required>
                <option value="">Select Role</option>
                <!-- Roles will be populated based on user type selection -->
            </select>
        </div>
        
        <!-- Change Password Section -->
        <div class="form-group">
            <label for="password">Change Password (optional):</label>
            <input type="password" id="password" name="password" class="form-control" >
        </div>

        <div class="form-group">
            <label for="password_confirmation">Confirm New Password:</label>
            <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" >
        </div>

        <!-- Buttons -->
        <div class="form-group" style="display: flex; gap: 10px;">
            <button type="button" class="btn btn-danger" onclick="confirmDelete()">Delete</button>
            <button type="submit" class="btn btn-primary">Update</button>
        </div>
    </form>

    <!-- Hidden Delete Form -->
    <form id="delete-form" action="{{ route('users.destroy', $selectedUser->id) }}" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>

    


    <script>
       // Function to confirm user deletion
    function confirmDelete() {
        if (confirm('Are you sure you want to delete this user?')) {
            document.getElementById('delete-form').submit(); // Submit the delete form
        }
    }

    // Populate the role dropdown based on the selected user type
    document.getElementById('usertype').addEventListener('change', function() {
        var userLevel = this.value;
        var roleDropdown = document.getElementById('role');

        // Clear existing options
        roleDropdown.innerHTML = '<option value="">Select Role</option>';

        if (userLevel === 'admin') {
            roleDropdown.innerHTML += `<option value="admin1">Admin1</option>`;
        } else if (userLevel === 'boss') {
            roleDropdown.innerHTML += `<option value="boss1">Boss1</option>`;
            roleDropdown.innerHTML += `<option value="boss2">Boss2</option>`;
        } else if (userLevel === 'user'){
            roleDropdown.innerHTML += `<option value="receiving">Receiving</option>`;
        } else if (userLevel === 'section') {
            // Fetch sections from the server for dynamic role assignment
            fetch(`/sections/${userLevel}`) // AJAX call to fetch roles based on user type
                .then(response => response.json())
                .then(data => {
                    data.sections.forEach(section => {
                        roleDropdown.innerHTML += `<option value="${section.name}">${section.name}</option>`;
                    });
                })
                .catch(error => {
                    console.error('Error fetching sections:', error);
                });
        }
    });

    // Call the change event to set the initial options based on the current user type
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('usertype').dispatchEvent(new Event('change'));
    });
    </script>
</div>
