
<style>
    /* Textbox styling */
.table-data input[type="text"],
.table-data input[type="email"],
.table-data input[type="password"],
.table-data textarea {
    width: 100%;
    padding: 10px;
    border: 1px solid #ccc; /* Light gray border */
    border-radius: 5px;
    font-size: 16px;
    background-color: #f9f9f9; /* Light background */
    margin-bottom: 1rem; /* Space between input fields */
    transition: border-color 0.3s ease, background-color 0.3s ease;
}

.table-data input[type="text"]:focus,
.table-data input[type="email"]:focus,
.table-data input[type="password"]:focus,
.table-data textarea:focus {
    border-color: #3490dc; /* Blue border on focus */
    background-color: #fff; /* White background on focus */
    outline: none;
}

/* Button styling */
.table-data button {
    background-color: #3490dc; /* Blue background */
    color: #fff; /* White text */
    padding: 10px 20px;
    margin-bottom: 25px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 16px;
    transition: background-color 0.3s ease;
}

.table-data button:hover {
    background-color: #2779bd; /* Darker blue on hover */
}

/* Disable button */
.table-data button:disabled {
    background-color: #b8c2cc; /* Gray background for disabled buttons */
    cursor: not-allowed;
}

/* Small screen adjustments */
@media (max-width: 640px) {
    .table-data input[type="text"],
    .table-data input[type="email"],
    .table-data input[type="password"],
    .table-data textarea {
        font-size: 14px;
    }

    .table-data button {
        font-size: 14px;
        padding: 8px 16px;
    }
}

</style>    
 <div class="table-data">
	
        <div class="py-12">
                 <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                 <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                    <div class="max-w-xl">
                        @include('profile.partials.update-profile-information-form')
                        </div>
                    </div>

                    <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                        <div class="max-w-xl">
                            @include('profile.partials.update-password-form')
                        </div>
                    </div>

                    <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                        <div class="max-w-xl">
                            @include('profile.partials.delete-user-form')
                        </div>
                    </div>
                </div>
        </div>
    </div>
