
<nav>
    <i class='bx bx-menu'></i>
    <form>
        <div class="form-input">
            <span class="qcpl">Quezon City Public Library | Document Tracking System</span>
        </div>
    </form>
    <div>
        @if(auth()->check())
            <span class="user-name" style="font-size: 14px; font-family: poppins; color: #828388; margin-right: 15px;">Hello, {{ auth()->user()->name }}</span>
        @endif
    </div>
@if(in_array(auth()->user()->usertype, ['user', 'section', 'boss']))
    <a href="#" class="notification" id="notificationBell">
        <i class='bx bxs-bell' style="margin-right: 15px;"></i>
        <span class="num" style="margin-right:15px;">0</span>
    </a>
@endif

<li >
                <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                    @csrf
                    <a href="#" class="logout" onclick="event.preventDefault(); this.closest('form').submit();">
                        <i class='bx bx-log-out' style="font-size:25px"></i>
                       
                    </a>
                </form>
			</li>
</nav>

			

    <!-- Notification Modal -->
    <div id="notificationModal" class="notification-modal">
        <div class="notification-modal-content">
            <span class="notification-close" id="closeModal">&times;</span>
        
                <h2>Notifications</h2>
                <div class="tabs">
                    <div class="tab active" id="allTab">
                        All
                    </div>
                    <div class="tab" id="readTab">
                        Read
                    </div>
                    <div class="tab" id="unreadTab">
                        Unread
                    </div>
                </div>
            <ul id="notificationList">
                <!-- Notifications will be dynamically added here -->
            </ul>

        </div>
    </div>



<!-- JavaScript to handle modal functionality -->
<script>

    // FILE AND SECTION MODAL

    let selectedSectionIds = [];
let documentId = null;
let sectionNames = {};

function openFileModal(fileUrl, originalFileName) {
    const fileExtension = originalFileName.split('.').pop().toLowerCase();
    const fileViewer = document.getElementById('fileViewer');
    const modal = document.getElementById('fileModal');
    const sidebar = document.querySelector('.sidebar');

    if (['pdf', 'png', 'jpeg', 'jpg'].includes(fileExtension)) {
        fileViewer.src = fileUrl;
        modal.style.display = 'block';
    } else if (fileExtension === 'docx') {
        const link = document.createElement('a');
        link.href = fileUrl;
        link.download = originalFileName;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        return;
    } else {
        alert('This file format is not supported for viewing.');
        return;
    }

    sidebar.classList.add('dimmed');
}

function closeFileModal() {
    const modal = document.getElementById('fileModal');
    const sidebar = document.querySelector('.sidebar');

    modal.style.display = 'none';
    sidebar.classList.remove('dimmed');
}

function openSectionModal(id) {
    documentId = id;
    fetchSections();
    document.getElementById('sectionModal').style.display = 'block';
}

function saveSelectedSections() {
    fetch(`/documents/${documentId}/save-sections`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        },
        body: JSON.stringify({ section_ids: selectedSectionIds }),
    })
    .then(response => response.json())
    .then(data => {
        console.log(data.message);
        closeSectionModal();
    })
    .catch(error => {
        console.error('Error saving sections:', error);
    });
}

function closeSectionModal(cancel = false) {
    const checkboxes = document.querySelectorAll('#sectionList input[type="checkbox"]');
    if (cancel) {
        checkboxes.forEach(checkbox => checkbox.checked = false);
    } else {
        selectedSectionIds = Array.from(checkboxes).filter(checkbox => checkbox.checked).map(checkbox => parseInt(checkbox.value));
        updateSelectedSectionsList();
    }
    document.getElementById('sectionModal').style.display = 'none';
}

function fetchSections() {
    fetch(`/get-selected-sections/${documentId}`)
        .then(response => response.json())
        .then(data => {
            const sectionListContainer = document.getElementById('sectionList');
            sectionListContainer.innerHTML = '';
            sectionNames = {}; // Reset section names before fetching new data


            selectedSectionIds = data.selectedSectionIds || [];
            
       

            data.sections.forEach(section => {
                // Map section ID to section name
                sectionNames[section.id] = section.name; 

                const checkbox = document.createElement('input');
                checkbox.type = 'checkbox';
                checkbox.value = section.id;
                checkbox.id = `section_${section.id}`;
                checkbox.checked = selectedSectionIds.includes(section.id);

                const label = document.createElement('label');
                label.htmlFor = `section_${section.id}`;
                label.innerText = section.name;

                const wrapper = document.createElement('div');
                wrapper.classList.add('section-item');
                wrapper.appendChild(checkbox);
                wrapper.appendChild(label);

                sectionListContainer.appendChild(wrapper);
            });

            sectionListContainer.style.height = '500px';
            sectionListContainer.style.overflowY = 'scroll';
            updateSelectAllButton();
            updateSelectedSectionsList(); // Update list to show selected sections
        })
        .catch(error => {
            console.error('Error fetching sections:', error);
        });
}

function confirmSectionSelection() {
    selectedSectionIds = Array.from(document.querySelectorAll('#sectionList input[type="checkbox"]'))
        .filter(checkbox => checkbox.checked)
        .map(checkbox => parseInt(checkbox.value));

    closeSectionModal(false);
    saveSelectedSections();
}

function toggleSelectAll() {
    const checkboxes = document.querySelectorAll('#sectionList input[type="checkbox"]');
    const allChecked = Array.from(checkboxes).every(checkbox => checkbox.checked);
    checkboxes.forEach(checkbox => checkbox.checked = !allChecked);
    updateSelectAllButton();
}

function updateSelectAllButton() {
    const checkboxes = document.querySelectorAll('#sectionList input[type="checkbox"]');
    const selectAllButton = document.querySelector('.btn.btn-warning');
    const allChecked = Array.from(checkboxes).every(checkbox => checkbox.checked);
    selectAllButton.innerText = allChecked ? 'Unselect All' : 'Select All';
}


function updateSelectedSectionsList() {
    const selectedSectionsList = document.getElementById('selectedSectionsList');
    selectedSectionsList.innerHTML = ''; // Clear the list

    // Use the sectionNames object to get the names
    selectedSectionIds.forEach((sectionId, index) => {
        const listItem = document.createElement('li');
        listItem.innerText = `${index + 1}. ${sectionNames[sectionId] || 'Unknown Section'}`; // Use the mapped name
        
        selectedSectionsList.appendChild(listItem);
    });

    selectedSectionsList.style.listStyleType = 'none'; // Remove default bullet points
}

   // NOTIFICATION

document.addEventListener('DOMContentLoaded', function() {
    const notificationBell = document.getElementById('notificationBell');
    const notificationModal = document.getElementById('notificationModal');
    const notificationList = document.getElementById('notificationList');
    const closeModal = document.getElementById('closeModal');
    const tabs = document.querySelectorAll('.tab');

    let currentTab = 'all'; // Default tab is 'all'

    // Toggle modal when notification bell is clicked
    notificationBell.addEventListener('click', function(e) {
        e.preventDefault();
        notificationModal.style.display = notificationModal.style.display === 'block' ? 'none' : 'block';
        if (notificationModal.style.display === 'block') {
            fetchNotifications(); // Fetch notifications from the server when modal is opened
        }
    });

    // Close modal when 'x' is clicked
    closeModal.addEventListener('click', function() {
        notificationModal.style.display = 'none';
    });

    // Close modal when clicking outside of the modal content
    window.addEventListener('click', function(event) {
        if (event.target === notificationModal) {
            notificationModal.style.display = 'none';
        }
    });

    tabs.forEach(tab => {
        tab.addEventListener('click', function() {
            // Set the current tab based on which tab was clicked
            const currentTab = tab.id.replace('Tab', '').toLowerCase(); // 'all', 'read', or 'unread'

            // Update active class for the tabs
            tabs.forEach(t => t.classList.remove('active'));
            tab.classList.add('active');

            // Fetch notifications based on the current tab
            fetchNotifications(currentTab);  // Pass currentTab ('all', 'read', 'unread')
        });
    });


    // Fetch notifications from the server
   function fetchNotifications(status = 'all') {
    fetch('/notifications') // Update with your endpoint
        .then(response => response.json())
        .then(data => {
            notificationList.innerHTML = ''; // Clear previous notifications

            // Update notification count
            const notificationCount = document.querySelector('.num');
            notificationCount.textContent = data.unreadCount; // Update count

            // Filter notifications based on the status
            let filteredNotifications = data.notifications;

            // Filter based on 'read' or 'unread' if the tab is selected
            if (status === 'read') {
                filteredNotifications = filteredNotifications.filter(notification => notification.is_read === 1);
            } else if (status === 'unread') {
                filteredNotifications = filteredNotifications.filter(notification => notification.is_read === 0);
            }

            // Handle filtered notifications
            filteredNotifications.forEach(notification => {
                const listItem = document.createElement('li');
                listItem.classList.add(notification.is_read ? 'read' : 'unread'); // Highlight unread notifications

               // Calculate the time difference
                const createdAt = new Date(notification.created_at);
                const now = new Date();
                const timeDifference = now - createdAt; // Time difference in milliseconds

                let timeDisplay;

                if (timeDifference < 60000) { 
                    // Less than a minute
                    timeDisplay = `Just Sent`;
                } else if (timeDifference < 3600000) { 
                    // Less than an hour
                    const minutesAgo = Math.floor(timeDifference / 60000);
                    timeDisplay = `${minutesAgo} minute${minutesAgo > 1 ? 's' : ''} ago`;
                } else if (timeDifference < 86400000) { 
                    // Less than a day
                    const hoursAgo = Math.floor(timeDifference / 3600000);
                    timeDisplay = `${hoursAgo} hour${hoursAgo > 1 ? 's' : ''} ago`;
                } else if (timeDifference < 2592000000) { 
                    // Less than a month (~30 days)
                    const daysAgo = Math.floor(timeDifference / 86400000);
                    timeDisplay = `${daysAgo} day${daysAgo > 1 ? 's' : ''} ago`;
                } else { 
                    // More than a month
                    const monthsAgo = Math.floor(timeDifference / 2592000000);
                    timeDisplay = `${monthsAgo} month${monthsAgo > 1 ? 's' : ''} ago`;
                }


                // Create the notification content with time and uploader name
                listItem.innerHTML = `
                    <a href="#">${notification.message}</a>
                    <p class="minutes" style="padding-top: 5px; font-size: 12px;">${timeDisplay}</p>
                    <p class="uploader-name" style="margin-top: -20px; margin-bottom: -5px; font-size: 13px;"><strong>Uploader:</strong> ${notification.uploader_name}</p>
                `;

                // Click event to fetch document details, mark as read, and close modal
                listItem.addEventListener('click', function(e) {
                    e.preventDefault();
                    const documentId = notification.document_id; // Get the document ID from the notification

                    markNotificationAsRead(notification.id); // Mark notification as read
                    fetchDocumentDetails(documentId); // Fetch document details
                    notificationModal.style.display = 'none'; // Close the modal
                });

                notificationList.appendChild(listItem);
            });
        })
        .catch(error => {
            console.error('Error fetching notifications:', error);
            // Optionally handle error UI feedback here
        });
}




    // Function to fetch document details via AJAX
    function fetchDocumentDetails(documentId) {
        fetch(`/documents/details/${documentId}`) // Adjust the endpoint as needed
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.text(); // Assuming the server returns HTML
            })
            .then(data => {
                // Hide the document table
                $('#document-table-container').hide();
                
                // Inject the HTML into the document details container
                const documentDetailsContainer = document.getElementById('document-details-container');
                documentDetailsContainer.innerHTML = data; // Set the response HTML
                documentDetailsContainer.style.display = 'block'; // Show the container if hidden
            })
            .catch(error => {
                console.error('Error fetching document details:', error);
                // Optionally, show an error message to the user
            });
    }

    // Function to mark notification as read and remove highlight
    function markNotificationAsRead(notificationId) {
        fetch(`/notifications/mark-as-read/${notificationId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') // Include CSRF token if using Laravel
            },
            body: JSON.stringify({ is_read: 1 }) // Send 1 to mark as read
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            // Remove 'unread' class and add 'read' class to remove highlight
            const listItem = document.querySelector(`li[data-id='${notificationId}']`);
            if (listItem) {
                listItem.classList.remove('unread');
                listItem.classList.add('read');
            }
        })
        .catch(error => {
            console.error('Error marking notification as read:', error);
        });
    }

    // Call fetchNotifications initially to get the unread count on page load
    fetchNotifications(); // This will ensure the count is updated on refresh
});


</script>
