

<div class="document-details">
    <h3>Document Details</h3>
    <div class="document-info">
        <div class="left-column"> 
            <p><strong>Locator No:</strong> {{ $document->locator_no }}</p>
            <p><strong>Subject:</strong> {{ $document->subject }}</p>
            <p><strong>Received from:</strong> {{ $document->received_from }}</p>
            <p><strong>Status:</strong>
            <span class="{{ 
                $document->status === 'approved' ? 'status-approved' : 
                ($document->status === 'rejected' ? 'status-rejected' : 
                ($document->status === 'completed' ? 'status-completed' : 'status-pending')) 
            }}">
                {{ ucfirst($document->status) }}
            </span>
            </p>
            <p><strong>Date Received:</strong> {{ \Carbon\Carbon::parse($document->date_received)->format('M. d, Y') }}</p>
            <p><strong>Date Filed:</strong> {{ \Carbon\Carbon::parse($document->date_filed)->format('M. d, Y') }}</p>
            <p><strong>Uploader:</strong> {{ $document->uploader->name }}</p>
            <p><strong>From:</strong> {{ ucfirst($document->uploaded_from) }}</p>
            <label for="file" class="view-file-label">View File:</label>
            <div class="file-preview" 
                onclick="openFileModal('{{ route('documents.view', $document->hashed_file_name) }}', '{{ $document->original_file_name }}')">
                <span class="file-name">{{ $document->original_file_name }}</span>
            </div>
        </div>
        <div class="right-column">
            <div class="comments-box">
                <h3>Details:</h3>
                <p>{{ $document->details }}</p>
            </div>
          
          
        </div>       
    </div>

    @if($document->status === 'rejected' || $document->status === 'approved' || $document->status === 'pending in CL' || auth()->user()->role === 'ACL' || auth()->user()->role === 'verfier' || $document->status === 'completed')
    <!-- Verifier Comment -->
        @if($document->verifierComments->isNotEmpty())
                <div class="comments-box verifier-comments">
                    <h3>Verifier's Comments:</h3>
                    @foreach($document->verifierComments as $comment)
                        <div class="comment-item">
                            <strong>{{ $comment->user->name }} ({{ $comment->user->role }})</strong>
                            <p class="comment-text">{{ $comment->comment }}</p>
                            <p class="comment-time" style="font-size: 12px;">{{ $comment->created_at->diffForHumans() }}</p>
                        </div>
                    @endforeach
                </div>
            @endif
        @endif

        <!-- Librarian Comments -->
        @if($document->librarianComments->isNotEmpty())
            <div class="comments-box librarian-comments">
                <h3>Librarian's Comments:</h3>
                @foreach($document->librarianComments as $comment)
                    @if($comment->reply_phase == 0)
                        <div class="comment-item">
                            <div class="comment-header">
                                <strong>{{ $comment->user->name }} ({{ $comment->user->role }})</strong>
                                <span class="comment-time" style="font-size: 12px;">{{ $comment->created_at->diffForHumans() }}</span>
                            </div>
                            <p class="comment-text">{{ $comment->comment }}</p>
                        </div>
                    @endif
                @endforeach
            </div>
        @endif

        @if($document->librarianComments->contains('reply_phase', 1) || $document->replyComments->isNotEmpty())
        <div class="comments-box reply-comments">
            <h3>Reply Statement:</h3>
            
            {{-- Librarian Replies --}}
            @foreach($document->librarianComments as $reply)
                @if($reply->reply_phase == 1)
                    <div class="comment-item">
                        <div class="comment-header">
                            <strong>{{ $reply->user->name }} ({{ $reply->user->role }})</strong>
                            <span class="comment-time" style="font-size: 12px;">{{ $reply->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="comment-text">{{ $reply->comment }}</p>
                    </div>
                @endif
            @endforeach

            {{-- Section Replies --}}
            @foreach($document->replyComments as $reply)
                <div class="comment-item">
                    <div class="comment-header">
                        <strong>{{ $reply->user->name }} ({{ $reply->user->role }})</strong>
                        <span class="comment-time" style="font-size: 12px;">{{ $reply->created_at->diffForHumans() }}</span>
                    </div>
                    <p class="comment-text">{{ $reply->comment }}</p>
                </div>
            @endforeach
        </div>
    @endif


<!-- Receiving Comment -->

    @if($document->receivingComments->isNotEmpty())
    <div class="comments-box receiving-comments">
        <h3>Receiving Comments:</h3>
        @foreach($document->receivingComments as $comment)
            <div class="comment-item">
                <strong>{{ $comment->user->name }} ({{ $comment->user->role }})</strong>
                <p class="comment-text">
                    @php
                        $words = explode(' ', $comment->comments);
                    @endphp
                    @if(count($words) > 10)
                        <span id="truncated-{{ $comment->id }}">{{ implode(' ', array_slice($words, 0, 10)) }}...</span>
                        <span id="full-{{ $comment->id }}" class="hidden">{{ $comment->comments }}</span>
                        <button onclick="toggleComment({{ $comment->id }})" id="btn-{{ $comment->id }}" class="see-more-button">See more</button>
                    @else
                        {{ $comment->comments }}
                    @endif
                </p>
                <span class="comment-time">{{ $comment->created_at->diffForHumans() }}</span>
            </div>
        @endforeach
    </div>
@endif

<!-- Section Comment -->

@if($document->sectionComments->isNotEmpty())
    <div class="comments-box section-comments">
        <h3>Section Comments:</h3>
        @foreach($document->sectionComments as $comment)
            <div class="comment-item">
                <strong>{{ $comment->user->name }} ({{ $comment->user->role }})</strong>
                <p class="comment-text">
                    @php
                        $words = explode(' ', $comment->comments);
                    @endphp
                    @if(count($words) > 10)
                        <span id="truncated-{{ $comment->id }}">{{ implode(' ', array_slice($words, 0, 10)) }}...</span>
                        <span id="full-{{ $comment->id }}" class="hidden">{{ $comment->comments }}</span>
                        <button onclick="toggleComment({{ $comment->id }})" id="btn-{{ $comment->id }}" class="see-more-button">See more</button>
                    @else
                        {{ $comment->comments }}
                    @endif
                </p>
                <span class="comment-time">{{ $comment->created_at->diffForHumans() }}</span>
            </div>
        @endforeach
    </div>
@endif


    @if(auth()->user()->usertype === 'section' || auth()->user()->role === 'receiving')
        @if(auth()->user()->id !== $document->user_id)
        <div class="approval-section">
                <h3 style="font-size: 20px;">Comments</h3>
                <form id="commentForm" action="{{ route('comments.store', $document->id) }}" method="POST">
                    @csrf
                    <textarea name="comments" id="comments" rows="4" placeholder="Add your comments here..." required></textarea>
                    
                    <input type="hidden" name="action_type" id="action_type" value="accomplish">

                    <!-- Buttons for Accomplish and Reply -->
                    <button type="submit" class="btn-primary" onclick="setActionType('accomplish')">Accomplish</button>
                    @if(auth()->user()->usertype === 'section')
                    <button type="submit" class="btn-secondary" onclick="setActionType('reply')">Reply</button>
                    @endif
                </form>
            </div>

            <script>
                function setActionType(type) {
                    document.getElementById('action_type').value = type;
                }
            </script>
        @endif
    @endif



    @if(auth()->user()->usertype === 'boss' && $document->uploaded_from === 'incoming')
        <button class="btn btn-primary" onclick="openSectionModal('{{ $document->id }}')">Assigned to</button>

        <!-- Real-time Section Selection Modal -->
        <div id="sectionModal" class="modal">
            <div class="modal-content">
                <h4>Select Sections</h4>
                <div class="section-list" id="sectionList">
                    <!-- Dynamically populated sections will appear here -->
                </div>
                <button class="btn btn-success" onclick="confirmSectionSelection()">OK</button>
                <button class="btn btn-warning" onclick="toggleSelectAll()">Select All</button>
                <button class="btn btn-secondary" onclick="closeSectionModal(true)">Cancel</button>
            </div>
        </div>
    

        <!-- Display selected sections above the "Send to" button -->
        <div id="selectedSectionsContainer">
            <h4>Selected Sections:</h4>
            <ul id="selectedSectionsList" required></ul>
        </div>

    @endif
    
    @if(auth()->user()->usertype === 'boss' || auth()->user()->role === 'verifier')
         <!-- COMMENT SECTION FOR LIBRARIANS -->
        <div class="approval-section">
            
                <h3 style="font-size: 20px;">Comments</h3>
                <form action="{{ route('documents.updateStatus', $document->id) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <textarea name="comments" id="comments" rows="4" placeholder="Add your comments here..." required></textarea>
               
                        <button type="submit" name="action" value="reject" class="btn btn-danger">Revise</button>
                        <button type="submit" name="action" value="approve" class="btn btn-success" onclick="saveSelectedSections()">Approve</button>
                  
                </form>
            
        </div>
    @endif
    
    @if(auth()->user()->usertype === 'user' || auth()->user()->usertype === 'section')
   
    @if($document->status === 'rejected' && auth()->user()->id === $document->uploader_id)
        <!-- Edit button that redirects to the edit page -->

        <button class="btn btn-primary"class="edit-button" onclick="window.location.href='{{ route('documents.edit', $document->id) }}'">Edit</button>

        <!-- Delete button -->
        <form action="{{ route('documents.destroy', $document->id) }}" method="POST" style="display: inline;">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this document?');">
                Delete
            </button>
        </form>
    @endif
    <!-- <button style="float: right;" class="back-button">Back</button> Back button -->

@endif
    

</div>

<!-- JavaScript for Modal and Real-time Section Handling -->
<script>
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


</script>