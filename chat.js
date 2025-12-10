
        // Get all message containers
        const messageContainers = document.querySelectorAll('.message-container');

        // Track the timestamp when the touch starts
        let touchStartTime = 0;

        // Add event listeners to message containers
        messageContainers.forEach(container => {
            // Add event listener for left-click (on desktop)
            container.addEventListener('click', function(event) {
                // Check if left-clicked on the message container (not the delete button)
                if (!event.target.classList.contains('delete-button')) {
                    // Toggle delete button visibility
                    toggleDeleteButton(container);
                }
            });

            // Add event listener for touch start (on mobile)
            container.addEventListener('touchstart', function() {
                touchStartTime = new Date().getTime();
            });

            // Add event listener for touch end (on mobile)
            container.addEventListener('touchend', function(event) {
                const touchEndTime = new Date().getTime();
                const touchDuration = touchEndTime - touchStartTime;

                // Check if touch duration is within the threshold for a long press (500ms)
                if (touchDuration < 500) {
                    // Toggle delete button visibility
                    toggleDeleteButton(container);
                }
            });
        });

        // Toggle delete button visibility for a message container
        function toggleDeleteButton(container) {
            const deleteButton = container.querySelector('.delete-button');
            const isHidden = deleteButton.style.display === 'none' || deleteButton.style.display === '';

            // Toggle the display property based on the current visibility state
            deleteButton.style.display = isHidden ? 'block' : 'none';
        }

        // Delete a message
        function deleteMessage(messageId) {
            // Send an AJAX request to delete the message
            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'delete_message.php', true);
            xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    // Reload the page to update the message history
                    location.reload();
                }
            };
            xhr.send('id=' + messageId);
        }
    


        // Function to fetch new messages using AJAX
        function fetchNewMessages() {
            const lastMessageId = getLastMessageId(); // Implement this function to get the last message ID displayed in the chat history
            
            // Send an AJAX request to fetch new messages
            const xhr = new XMLHttpRequest();
            xhr.open('GET', `fetch_messages.php?username=<?php echo $chatUser['username']; ?>&lastMessageId=${lastMessageId}`, true);
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    const newMessages = JSON.parse(xhr.responseText);
                    if (newMessages.length > 0) {
                        // Process and append the new messages to the chat history
                        newMessages.forEach(function(message) {
                            appendMessageToHistory(message);
                        });
                    }
                }
            };
            xhr.send();
        }


        
        // Function to append a new message to the chat history
        function appendMessageToHistory(message) {
            const chatHistory = document.querySelector('.chat-history');

            // Create HTML elements for the new message
            const messageContainer = document.createElement('div');
            messageContainer.classList.add('message-container');
            messageContainer.classList.add((message.from_user === <?php echo $user_id; ?>) ? 'sent' : 'received');

            const messageTime = document.createElement('span');
            messageTime.textContent = message.time_sent;
            messageTime.classList.add('message-time');

            const messageText = document.createElement('p');
            messageText.textContent = message.message;
            messageText.classList.add('message');

            // Append the elements to the chat history
            messageContainer.appendChild(messageTime);
            messageContainer.appendChild(messageText);
            chatHistory.appendChild(messageContainer);
        }
 
        // Function to get the ID of the last message displayed in the chat history
        function getLastMessageId() {
            const messageContainers = document.querySelectorAll('.message-container');
            const lastMessageContainer = messageContainers[messageContainers.length - 1];
            return lastMessageContainer ? lastMessageContainer.getAttribute('data-message-id') : 0;
        }
 
        // Periodically fetch new messages every 1.5 seconds (adjust as needed)
        setInterval(fetchNewMessages, 1500);
    