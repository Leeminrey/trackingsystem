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

     <!-- CONVERSTATION CONTAINER-->
     <div id="openChatWith" class="converstationWith">
     <!-- HEADER -->
    <div class="chat-header">
        <i class="bx bx-arrow-back" onclick="backToUserList()" style="cursor: pointer;"></i>
        <div class="chat-user-info">
            <h4 id="chatWithName">Edward Mabini</h4>
            <span class="status">Online</span>
        </div>
        <div class="minimize-modal">
             <i class='bx bx-minus' id="minimizeBtn"></i>
        </div>
    </div>

    <div class="chat-messages" id="chatMessages">
            <div class="message incoming">
                <div class="bubble">Baby!, Kumain na po ba ikaw? 👉🥺👈</div>
                <div class="timestamp">2 min ago</div>
            </div>

            <div class="message outgoing">
                <div class="bubble">Hindi pa baby ko 🥺🥺🥺, bilan mo ako burger and milktea baby ko. 👉🥺👈🥰🥰</div>
                <div class="timestamp">2 min ago.</div>
            </div>

            <div class="message incoming">
                <div class="bubble">Sige baby wait moko and dadalin ko d'yan. Bembang kita after mo kumain ah? 😁😁😁😁</div>
                <div class="timestamp">1 min ago</div>
            </div>

            <div class="message outgoing">
                <div class="bubble">Sige baby shave na rin ako habang wala ka pa hehe... 👉👌🥵💦</div>
                <div class="timestamp">1 min ago.</div>
            </div>
            
        </div>

        <!-- INPUT BAR-->
        <div class="chat-input">
            <input type="text" placeholder="Type your message here bitch..." />
            <button class="send-btn"><i class="bx bx-send"></i></button>
    </div>

  
     </div>
</div>

    