const allSideMenu = document.querySelectorAll('#sidebar .side-menu.top li a');

'X-CSRF-TOKEN'; document.querySelector('meta[name="csrf-token"]').getAttribute('content'),

allSideMenu.forEach(item=> {
	const li = item.parentElement;

	item.addEventListener('click', function () {
		allSideMenu.forEach(i=> {
			i.parentElement.classList.remove('active');
		})
		li.classList.add('active');
	})
});




// TOGGLE SIDEBAR
const menuBar = document.querySelector('#content nav .bx.bx-menu');
const sidebar = document.getElementById('sidebar');

menuBar.addEventListener('click', function () {
	sidebar.classList.toggle('hide');
})







document.addEventListener('DOMContentLoaded', function() {
    const dropdownToggle = document.querySelector('.dropdown-toggle');
    const dropdown = dropdownToggle.nextElementSibling;

    dropdownToggle.addEventListener('click', function(e) {
        e.preventDefault(); // Prevent default anchor behavior
        dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
    });

    // Optional: Close the dropdown if clicking outside of it
    document.addEventListener('click', function(event) {
        if (!dropdownToggle.contains(event.target) && !dropdown.contains(event.target)) {
            dropdown.style.display = 'none'; 
        }
    });
});

document.querySelectorAll('.side-menu li').forEach(item => {
    item.addEventListener('mouseenter', () => {
        document.querySelectorAll('.side-menu li').forEach(li => li.classList.remove('active'));
        item.classList.add('active');
    });

    item.addEventListener('mouseleave', () => {
        document.querySelectorAll('.side-menu li').forEach(li => li.classList.remove('active'));
        // Check if the current page matches any route, and set the active class accordingly
        document.querySelectorAll('.side-menu li').forEach(li => {
            if (li.dataset.route === 'dashboard' && window.location.pathname === '/dashboard') {
                li.classList.add('active');
            }
            // Add conditions for other routes as needed
        });
    });
});

$(document).ready(function() {
    // Check if document-summary elements exist before adding the event
    if ($('.document-summary').length > 0) {
        $('.document-summary').on('click', function() {
            var documentId = $(this).data('id'); // Get the document ID from the row

            // AJAX request to fetch document details
            $.ajax({
                url: '/documents/details/' + documentId,
                type: 'GET',
                success: function(response) {
                    // Hide the document table
                    $('#document-table-container').hide();
                    // Replace the content in the document details container
                    $('#document-details-container').html(response).show();
                },
                error: function(xhr) {
                    console.error(xhr.responseText); // Log any errors for debugging
                    alert('Could not load document details. Please try again later.');
                }
            });
        });
    }

    // Back button click event
    $(document).on('click', '.back-button', function() {
        // Hide the document details
        $('#document-details-container').hide();
        // Show the document table again
        $('#document-table-container').show();
    });
});

$(document).ready(function() {
    const sidebar = $('#sidebar');
    const documentMenuItems = sidebar.find('.document-menu-item');
    const subMenuItems = sidebar.find('.sub-menu-item');

    // Check localStorage for sidebar state
    if (localStorage.getItem('sidebarMinimized') === 'true') {
        sidebar.addClass('minimized');
        documentMenuItems.css('margin-left', '5px'); // Align to the left when minimized
        subMenuItems.css('margin-left', '6px'); // Slightly more indented when minimized
    } else {
        sidebar.removeClass('minimized');
        documentMenuItems.css('margin-left', '30px'); // Standard margin when expanded
        subMenuItems.css('margin-left', '60px'); // More indentation for sub-items when expanded
    }

    $('.bx-menu').click(function() {
        // Toggle minimized class
        sidebar.toggleClass('minimized');

        if (sidebar.hasClass('minimized')) {
            // Sidebar is minimized
            localStorage.setItem('sidebarMinimized', 'true');
            documentMenuItems.css('margin-left', '6px');
            subMenuItems.css('margin-left', '6px'); // Sub-item slight indent
        } else {
            // Sidebar is expanded
            localStorage.setItem('sidebarMinimized', 'false');
            documentMenuItems.css('margin-left', '30px');
            subMenuItems.css('margin-left', '60px'); // Extra indent for sub-items
        }
    });
});


// SEARCH BAR

$(document).ready(function() {
    const $searchInput = $('#search-input');
    const $clearButton = $('#clearButton');
    const $documentsTable = $('#documentsTable tbody');

    // Show the clear button when there's input
    $searchInput.on('input', function() {
        if ($searchInput.val()) {
            $clearButton.show();
        } else {
            $clearButton.hide();
            $documentsTable.find('tr').show();
        }
    });

    // Filter table rows based on search input (subject + locator number)
    $searchInput.on('input', function() {
        const query = $searchInput.val().toLowerCase();
        $documentsTable.find('tr').filter(function() {
            const subject = $(this).find('td:nth-child(3)').text().toLowerCase();
            const locatorNumber = $(this).find('td:nth-child(1)').text().toLowerCase();
            $(this).toggle(subject.includes(query) || locatorNumber.includes(query));
        });
    });

    // Clear the search input and reset the table
    $clearButton.on('click', function() {
        $searchInput.val('');
        $clearButton.hide();
        $documentsTable.find('tr').show();
    });
});


// PAGINATION

    let currentPage = 1;
    const documentsPerPage = 5;

    function displayDocuments() {
        const rows = document.querySelectorAll('#documentsTable tbody tr');
        const totalDocuments = rows.length;
        const totalPages = Math.ceil(totalDocuments / documentsPerPage);

        // Hide all rows
        rows.forEach(row => {
            row.style.display = 'none';
        });

        // Calculate the start and end index of the rows to display
        const start = (currentPage - 1) * documentsPerPage;
        const end = start + documentsPerPage;

        // Show only the rows for the current page
        for (let i = start; i < end && i < totalDocuments; i++) {
            rows[i].style.display = '';
        }

        // Update page info
        document.getElementById('page-info').textContent = `Page ${currentPage} of ${totalPages}`;

        // Disable buttons if on first/last page
        document.querySelector('.prev').disabled = currentPage === 1;
        document.querySelector('.next').disabled = currentPage === totalPages;
    }

    function changePage(direction) {
        const totalDocuments = document.querySelectorAll('#documentsTable tbody tr').length;
        const totalPages = Math.ceil(totalDocuments / documentsPerPage);

        // Update current page
        currentPage += direction;
        if (currentPage < 1) currentPage = 1;
        if (currentPage > totalPages) currentPage = totalPages;

        displayDocuments();
    }

    document.addEventListener('DOMContentLoaded', () => {
        displayDocuments(); // Initial display of documents
    });


    // COMMENT SECTION

    
    function toggleComment(commentId) {
        // Get the elements for this specific comment
        const truncated = document.getElementById(`truncated-${commentId}`);
        const full = document.getElementById(`full-${commentId}`);
        const btnText = document.getElementById(`btn-${commentId}`);
    
        // Toggle visibility of truncated and full text
        if (full.classList.contains('hidden')) {
            full.classList.remove('hidden'); // Show full comment
            truncated.classList.add('hidden'); // Hide truncated comment
            btnText.textContent = "See less"; // Update button text
        } else {
            full.classList.add('hidden'); // Hide full comment
            truncated.classList.remove('hidden'); // Show truncated comment
            btnText.textContent = "See more"; // Updwate button text
        }


    }
    function confirmReject() {
        let modal = document.getElementById("customModal");
        modal.style.display = "block";  

        document.getElementById("confirmBtn").addEventListener("click", function() {
            let form = document.getElementById("approvalForm");
            let input = document.createElement("input");
            input.type = "hidden";
            input.name = "action";
            input.value = "reject";
            form.appendChild(input);
            form.submit();
        });

        document.getElementById("cancelBtn").addEventListener("click", function() {
            modal.style.display = "none"; // Close modal
        });
    }


    // CHATTING SYSTEM
    
    function toggleChatModal(){
        let modal = document.getElementById('chatModal');
        modal.style.display = modal.style.display === 'block' ? 'none' : 'block';
        document.querySelector('.message-item').style.display = 'block';
        document.querySelector('.chatTitle').style.display = 'block';
        
    }

  

    document.getElementById('searchUser').addEventListener('input', function(){
        let searchValue = this.value.toLowerCase();
        let users = document.querySelectorAll('.user-item');
        
        users.forEach(function (user){
            let userName = user.querySelector('.user-names').textContent.toLowerCase();
            if (userName.includes(searchValue)){
                user.style.display = 'block';
            } else {
                user.style.display = 'none';
            }
        })
    })


    window.onload = function(){
        document.querySelector('.message-item').style.display = 'block';
        
        document.querySelector('.chatTitle').style.display = 'block';
        document.getElementById('openChatWith').style.display = 'none';
    }

    const textarea = document.getElementById("chatInput");

    textarea.addEventListener("input", () => {
        textarea.style.height = "auto"; // Reset height
        textarea.style.height = Math.min(textarea.scrollHeight, 120) + "px"; // 120px = max 4 lines
    });

    // Listen for a new message in the chat channel
window.Echo.private('chat.' + receiverId)
.listen('Messagesent', (event) => {
    console.log(event.message); // Handle the message in your chat UI
    // Update the chat UI with the new message (e.g., append it to the messages list)
});
