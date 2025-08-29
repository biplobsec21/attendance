<!-- Chat Box -->
<div id="chat-box">
    <div id="chat-header">
        <h3 class="text-sm font-bold text-gray-700">Live Chat</h3>
        <button id="close-chat" class="text-gray-500 hover:text-gray-700">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
            </svg>
        </button>
    </div>
    <div id="chat-messages">
        <!-- Chat messages will be appended here -->
    </div>
    <div id="chat-input-area">
        <input type="text" id="chat-input" class="flex-grow rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 p-2 mr-2" placeholder="Type your message...">
        <button id="send-chat" class="bg-orange-500 text-white rounded-md px-4 py-2 hover:bg-orange-600 transition-colors">Send</button>
    </div>
</div>

<!-- Chat Toggle Button -->
<button id="chat-toggle-button" aria-label="Toggle chat">
    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 4v-4z" />
    </svg>
</button>
