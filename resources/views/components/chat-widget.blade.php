<diV class="chat-floating-button" onclick="toggleChatModal()"><i class='bx bxs-message-alt-detail'></i>

</diV>


<div id="chatModal" class="chat-modal">
    <div class="chatTitle">
        <h4>MESSAGES<i class='bx bx-minus' id="minimizeBtn"></i></h4>
        
        <i class='bx bx-search-alt' id="searchChatBtn"></i>
        <input type="text" id="searchUser" class="search-user" placeholder="Search Users."> 
    </div>
        <ul class="message-item">
            @foreach ($users as $user)
            <div class="item-box">
                <li class="user-item">
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
</div>
