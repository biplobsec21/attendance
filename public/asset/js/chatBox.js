document.addEventListener('DOMContentLoaded', function() {
    const chatToggleButton = document.getElementById('chat-toggle-button');
    const chatBox = document.getElementById('chat-box');
    const closeChatButton = document.getElementById('close-chat');
    const chatInput = document.getElementById('chat-input');
    const sendChatButton = document.getElementById('send-chat');
    const chatMessages = document.getElementById('chat-messages');
    
    if (chatToggleButton && chatBox && closeChatButton && chatInput && sendChatButton && chatMessages) {
        chatToggleButton.addEventListener('click', () => {
            chatBox.style.display = 'flex';
            chatToggleButton.style.display = 'none';
        });

        closeChatButton.addEventListener('click', () => {
            chatBox.style.display = 'none';
            chatToggleButton.style.display = 'flex';
        });
        
        sendChatButton.addEventListener('click', sendMessage);
        chatInput.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                sendMessage();
            }
        });

        function sendMessage() {
            const messageText = chatInput.value.trim();
            if (messageText) {
                const messageElement = document.createElement('div');
                messageElement.classList.add('flex', 'justify-end');
                messageElement.innerHTML = `<span class="bg-orange-500 text-white rounded-lg p-2 max-w-[80%]">${messageText}</span>`;
                chatMessages.prepend(messageElement);
                chatInput.value = '';
                chatMessages.scrollTop = chatMessages.scrollHeight; // Scroll to bottom
            }
        }
    }
});
