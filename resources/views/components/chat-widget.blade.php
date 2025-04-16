<diV class="chat-floating-button" onclick="toggleChatModal()"><i class='bx bxs-message-alt-detail'></i>

</diV>


<div id="chatModal" class="chat-modal">
    <div class="chatTitle" >
        <h4>MESSAGES<i class='bx bx-minus' id="minimizeBtn"></i></h4>
        
        <i class='bx bx-search-alt' id="searchChatBtn"></i>
        <input type="text" id="searchUser" class="search-user" placeholder="Search Users."> 
    </div>
        <ul class="message-item">
            @foreach ($users as $user)
            <div class="item-box">
                <li class="user-item"
                    data-user-id="{{$user->id }}"
                    data-user-name="{{ $user->name}}"
                    data-user-role="{{ $user->role }}"
                    onclick="openConversation(this)">
                    <div class="user-icon" style="background-color: {{ '#' . substr(md5($user->id), 0, 6) }};">
                        <h2>{{strtoupper(substr($user->name, 0, 1))}}</h3>
                    </div>
                    <h3 class="user-names">{{$user->name}}</h3>
                    <p class="message">You: Sobrang mahabang message tangina na dapat putulin para hindi masira ang layout.</p>
                    <p class="timeIndex" style="font-size: 10px;">12:00 PM</p>
                </li>
            </div>
            @endforeach
        </ul>

     <!-- CONVERSTATION CONTAINER-->
     <div id="openChatWith" class="converstationWith" style="display: none";>
     <!-- HEADER -->
    <div class="chat-header">
        <i class="bx bx-arrow-back" onclick="backToUserList()" style="cursor: pointer;"></i>
        <div class="chat-user-info">
            <h4 id="chatWithName">Sample Name</h4>
            <span class="status">(sample section)</span>
        </div>
        
    </div>

    <div class="chat-messages" id="chatMessages">
            <div class="message incoming">
                <div class="bubble"></div>
                <div class="timestamp"></div>
            </div>

            <div class="message outgoing">
                <div class="bubble"></div>
                <div class="timestamp"></div>
            </div>

            
    </div>

        <!-- INPUT BAR-->
    <div class="chat-input">
            <textarea id="chatInput" placeholder="Aa" rows="1"></textarea>
            <button class="send-btn" onclick="sendMessage()"><i class="bx bx-send"></i></button>
    </div>

  
     </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        let selectedUserId = null;
        let selectedUserName = null;
        let receiverRole = null;
    
        // Open conversation when clicking a user
        function openConversation(userElement) {
            selectedUserId = userElement.getAttribute('data-user-id');
            selectedUserName = userElement.getAttribute('data-user-name');
            receiverRole = userElement.getAttribute('data-user-role');
    
            // Update the header with the user's name and role
            document.getElementById('chatWithName').innerText = selectedUserName;
            document.querySelector('.status').innerText = `(${receiverRole})`;
    
            // Show the conversation container
            document.querySelector('.message-item').style.display = 'none';
            document.querySelector('.chatTitle').style.display = 'none';
            document.getElementById('openChatWith').style.display = 'block';
    
            // Fetch existing messages between the two users
            fetchMessages(selectedUserId);
    
            // Listen to the private chat channel for new messages
            listenForMessages(selectedUserId);
        }
    
        // Fetch previous messages between users
        function fetchMessages(userId) {
            axios.get(`/messages/${userId}`)
                .then(response => {
                    const messages = response.data;
                    const messagesContainer = document.getElementById('chatMessages');
                    messagesContainer.innerHTML = '';
    
                    // Display all messages
                    messages.forEach(message => {
                        const messageElement = document.createElement('div');
                        messageElement.classList.add(message.sender_id === selectedUserId ? 'incoming' : 'outgoing');
                        messageElement.innerHTML = `
                            <div class="bubble">${message.messages}</div>
                            <div class="timestamp">${message.created_at}</div>
                        `;
                        messagesContainer.appendChild(messageElement);
                    });
    
                    // Scroll to the bottom
                    messagesContainer.scrollTop = messagesContainer.scrollHeight;
                })
                .catch(error => {
                    console.log('Error fetching messages:', error);
                });
        }
    
        // Listen for new messages from Echo
        function listenForMessages(userId) {
            window.Echo.private('chat.' + userId)
                .listen('Messagesent', function(event) {
                    const messageContainer = document.getElementById('chatMessages');
                    const message = event.message;
    
                    // Display the new incoming message
                    const messageElement = document.createElement('div');
                    messageElement.classList.add(message.sender_id === selectedUserId ? 'incoming' : 'outgoing');
                    messageElement.innerHTML = `
                        <div class="bubble">${message.messages}</div>
                        <div class="timestamp">${message.created_at}</div>
                    `;
                    messageContainer.appendChild(messageElement);
    
                    // Scroll to the bottom
                    messageContainer.scrollTop = messageContainer.scrollHeight;
                });
        }
    
        // Send a message
        document.querySelector('.send-btn').addEventListener('click', function() {
            const messageInput = document.getElementById('chatInput').value;
            
            // Check if the message is not empty
            if (messageInput.trim()) {
                // Log the data being sent (Optional: for debugging purposes)
                console.log({
                    receiver_id: selectedUserId,
                    messages: messageInput
                });

                // Send the message via axios
                axios.post('/messages', {
                    receiver_id: selectedUserId,  // The receiver ID, this should be set from your UI logic
                    messages: messageInput       // The message content
                })
                .then(response => {
                    console.log('Message saved:', response.data);  // Log the response to see if the message was saved
                    document.getElementById('chatInput').value = ''; // Clear the input field after sending
                    document.getElementById('chatInput').focus();   // Focus back on input field
                    
                    // Scroll to the bottom after sending
                    const messagesContainer = document.getElementById('chatMessages');
                    messagesContainer.scrollTop = messagesContainer.scrollHeight;
                })
                .catch(error => {
                    console.log('Error sending message:', error);
                    alert('There was an error sending the message. Please try again.');
                });
            }
        });

        // Minimize chat modal
        document.getElementById('minimizeBtn').addEventListener('click', function() {
            document.getElementById('chatModal').style.display = 'none';
        });
    
        // Back to user list from conversation
        function backToUserList() {
            document.querySelector('.message-item').style.display = 'block';    
            document.querySelector('.chatTitle').style.display = 'block';
            document.getElementById('openChatWith').style.display = 'none';
        }
    
        window.openConversation = openConversation; // Make openConversation accessible
        window.backToUserList = backToUserList; // Make backToUserList accessible
    });
</script>
