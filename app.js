    // Connect to the WebSocket server
    let socket = new WebSocket("ws://localhost:8080/chat");

    // Function to create a new message HTML structure
    function createMessageTemplate(message) {
        return `
        <div class="msg left-msg">
            <div class="msg-img" style="background-image: url(https://image.flaticon.com/icons/svg/327/327779.svg)">
            </div>
            <div class="msg-bubble">
                <div class="msg-text">
                    ${message}
                </div>
            </div>
        </div>`;
    }

    // Event handler for when a new message is received
    socket.onmessage = function (event) {
        let chatBox = document.getElementById('messages');
        let newMessage = createMessageTemplate(event.data);

        // Add the new message to the chat box
        chatBox.innerHTML += newMessage;

        // Scroll to the bottom of the chat box
        chatBox.scrollTop = chatBox.scrollHeight;
    };
    socket.onclose = function (event) {
        console.log("WebSocket connection closed");
    };