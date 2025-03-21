@extends('layouts.app')

@section('content')


@include('components.breadcrumb')


<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h2 class="text-center mb-4">Create User</h2>
            <form method="POST" action="{{ route('users.store') }}" enctype="multipart/form-data" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
                @csrf

                <!-- Employee ID -->
                <div class="form-group">
                    <label for="employee_id">Employee ID:</label>
                    <input type="text" 
                           id="employee_id" 
                           class="form-control block mt-1 w-full" 
                           name="employee_id" 
                           placeholder="Employee ID" 
                           value="{{ old('employee_id') }}" 
                           required 
                           autofocus 
                           style="text-transform: uppercase;" 
                           oninput="this.value = this.value.toUpperCase();">
                    @if ($errors->get('employee_id'))
                        <div class="text-danger mt-2">
                            {{ implode(', ', $errors->get('employee_id')) }}
                        </div>
                    @endif
                </div>  

                <!-- Name -->
                <div class="form-group">
                    <label for="name">Full Name:</label>
                    <input type="text" 
                           id="name" 
                           class="form-control block mt-1 w-full" 
                           name="name" 
                           placeholder="Full Name" 
                           value="{{ old('name') }}" 
                           required 
                           autocomplete="name">
                    @if ($errors->get('name'))
                        <div class="text-danger mt-2">
                            {{ implode(', ', $errors->get('name')) }}
                        </div>
                    @endif
                </div>

                <!-- Email Address -->
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" 
                           id="email" 
                           class="form-control block mt-1 w-full" 
                           name="email" 
                           placeholder="Email" 
                           value="{{ old('email') }}" 
                           required 
                           autocomplete="username">
                    @if ($errors->get('email'))
                        <div class="text-danger mt-2">
                            {{ implode(', ', $errors->get('email')) }}
                        </div>
                    @endif
                </div>

                <!-- User Level -->
                <div class="form-group">
                    <label for="usertype">User Level:</label>
                    <select id="usertype" name="usertype" class="form-control block mt-1 w-full" required>
                        <option value="">Select a User Level</option>
                        <option value="admin" {{ old('usertype') == 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="boss" {{ old('usertype') == 'boss' ? 'selected' : '' }}>Boss</option>
                        <option value="user" {{ old('usertype') == 'user' ? 'selected' : '' }}>User</option>
                        <option value="section" {{ old('usertype') == 'section' ? 'selected' : '' }}>Section</option>
                    </select>
                    @if ($errors->get('usertype'))
                        <div class="text-danger mt-2">
                            {{ implode(', ', $errors->get('usertype')) }}
                        </div>
                    @endif
                </div>

                <!-- Role  -->
                <div class="form-group mt-4">
                    <label for="role">Role:</label>
                    <select id="role" name="role" class="form-control block mt-1 w-full" required>
                        <option value="">Select a Role</option>
                        <!-- Options will be populated dynamically -->
                    </select>
                </div>

                <!-- Password -->
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" 
                           id="password" 
                           class="form-control block mt-1 w-full" 
                           name="password" 
                           required 
                           autocomplete="new-password">
                    @if ($errors->get('password'))
                        <div class="text-danger mt-2">
                            {{ implode(', ', $errors->get('password')) }}
                        </div>
                    @endif
                </div>

                <!-- Confirm Password -->
                <div class="form-group">
                    <label for="password_confirmation">Confirm Password:</label>
                    <input type="password" 
                           id="password_confirmation" 
                           class="form-control block mt-1 w-full" 
                           name="password_confirmation" 
                           required 
                           autocomplete="new-password">
                    @if ($errors->get('password_confirmation'))
                        <div class="text-danger mt-2">
                            {{ implode(', ', $errors->get('password_confirmation')) }}
                        </div>
                    @endif
                </div>

                <!-- Submit Button -->
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">
                        Create User
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


<script>
  document.getElementById('usertype').addEventListener('change', function() {
        var userLevel = this.value;
        var roleDropdown = document.getElementById('role');

        // Clear existing options
        roleDropdown.innerHTML = '<option value="">Select Role</option>';

        // For "admin" and "boss" user levels, populate fixed options
        if (userLevel === 'admin') {
            roleDropdown.innerHTML += `<option value="admin1">Admin1</option>`;
        } else if (userLevel === 'boss') {
            roleDropdown.innerHTML += `<option value="CL">City Librarian</option>`;
            roleDropdown.innerHTML += `<option value="ACL">Assistant City Librarian</option>`;
        }else if (userLevel === 'user') {
            roleDropdown.innerHTML += `<option value="receiving">Receiving</option>`;
            roleDropdown.innerHTML += `<option value="verifier">Verifier</option>`;
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
</script>

@endsection
