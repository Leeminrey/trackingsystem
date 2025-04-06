<div id="date-range-picker" class="flex items-center">
    <div class="relative">
        <input id="datepicker-range-start" name="start" type="text" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg p-2" placeholder="Select Start Date" />
    </div>
    <span class="mx-4 text-gray-500">to</span>
    <div class="relative">
    <input id="datepicker-range-end" name="end" type="text" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg p-2" placeholder="Select End Date" />
    </div>
    <button id="filterButton" class="ml-2 bg-blue-500 text-white p-2 rounded">FILTER</button>
</div>


<script>
    
$(document).ready(function() {
    // Initialize the datepicker for start and end date
    $('#datepicker-range-start').daterangepicker({
        singleDatePicker: true,
        locale: {
            format: 'YYYY-MM-DD'
        }
    });

    $('#datepicker-range-end').daterangepicker({
        singleDatePicker: true,
        locale: {
            format: 'YYYY-MM-DD'
        }
    });

    // Event listener to handle filtering
    $('#filterButton').on('click', function() {
        const startDate = $('#datepicker-range-start').val(); // Get start date
        const endDate = $('#datepicker-range-end').val(); // Get end date

        // Make an AJAX request to filter documents
        $.ajax({
            url: '{{ route("filter.documents") }}', // URL to your Laravel route
            method: 'GET',
            data: {
                start_date: startDate,
                end_date: endDate
            },
            success: function(response) {
                // Clear the current list of documents
                $('#documentsTableBody').empty();

                // Loop through the response and add the documents to the table
                response.forEach(function(document) {
                    $('#documentsTableBody').append(
                        `<tr class="document-summary 
                            ${document.status === 'approved' ? 'approved-row' : 
                            document.status === 'rejected' ? 'rejected-row' : 
                            document.status === 'pending' ? 'pending-row' : 
                            document.status === 'pending in CL' ? 'pending-CL-row' : ''}"
                            data-id="${document.id}" style="cursor: pointer;">
                            <td>${document.locator_no}</td>
                            <td>${document.user.name}</td>
                            <td>${document.subject}</td>
                            <td>${moment(document.date_received).format('MMM. D, YYYY')}</td>
                        </tr>`
                    );
                });
            },
            error: function(error) {
                console.log("Error:", error);
            }
        });
    });
});


</script>