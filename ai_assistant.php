<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include "config.php"; // Database connection file

$name = $_SESSION['name'];

// Fetch user bookings from the database (current bookings schema).
$bookings_query = "SELECT date, start_time AS time_from, end_time AS time_to, room_number AS classroom
                   FROM bookings
                   WHERE name = ?";
$stmt = $conn->prepare($bookings_query);
$stmt->bind_param("s", $name);
$stmt->execute();
$result = $stmt->get_result();

$bookings = [];
while ($row = $result->fetch_assoc()) {
    $bookings[] = $row;
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Assistant</title>
    <style>
        /* General Styles */
        body {
            background: #f4f7f9;
            font-family: 'Poppins', sans-serif;
            color: #333;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .chat-container {
            width: 500px;
            height: 600px;
            background: #fff;
            box-shadow: 0px 4px 20px rgba(0, 0, 0, 0.1);
            border-radius: 12px;
            display: flex;
            flex-direction: column;
        }

        /* Chat Header */
        .chat-header {
            padding: 15px;
            background: #0056b3;
            color: white;
            text-align: center;
            font-size: 20px;
            font-weight: bold;
            border-top-left-radius: 12px;
            border-top-right-radius: 12px;
        }

        /* Chat Messages */
        .chat-box {
            flex-grow: 1;
            padding: 15px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
        }

        .message {
            max-width: 80%;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 10px;
            font-size: 15px;
        }

        .user {
            background: #0056b3;
            color: white;
            align-self: flex-end;
        }

        .ai {
            background: #e3e6ea;
            color: #333;
            align-self: flex-start;
        }

        /* Chat Input */
        .chat-input {
            display: flex;
            border-top: 1px solid #ddd;
            padding: 10px;
            background: #f4f7f9;
            border-bottom-left-radius: 12px;
            border-bottom-right-radius: 12px;
        }

        input {
            flex-grow: 1;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 16px;
            outline: none;
        }

        button {
            padding: 10px 15px;
            margin-left: 10px;
            background: #0056b3;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            transition: 0.3s;
        }

        button:hover {
            background: #004494;
        }
    </style>
</head>
<body>

<div class="chat-container">
    <div class="chat-header">
        AI Chat Assistant
    </div>

    <div class="chat-box" id="chat-box">
        <div class="message ai">Hello! How can I assist you today? 😊</div>
    </div>

    <div class="chat-input">
        <input type="text" id="userInput" placeholder="Type a message..." onkeypress="handleKeyPress(event)">
        <button onclick="sendMessage()">Send</button>
    </div>
</div>

<script>
    let bookings = <?php echo json_encode($bookings); ?>;

    function sendMessage() {
        let inputField = document.getElementById("userInput");
        let chatBox = document.getElementById("chat-box");
        let userMessage = inputField.value.trim();

        if (userMessage === "") return;

        // Display user message
        let userDiv = document.createElement("div");
        userDiv.className = "message user";
        userDiv.textContent = userMessage;
        chatBox.appendChild(userDiv);

        // Scroll to bottom
        chatBox.scrollTop = chatBox.scrollHeight;

        // Simulate AI Response
        setTimeout(() => {
            let aiDiv = document.createElement("div");
            aiDiv.className = "message ai";
            aiDiv.innerHTML = "🤖 Thinking...";
            chatBox.appendChild(aiDiv);
            chatBox.scrollTop = chatBox.scrollHeight;

            setTimeout(() => {
                aiDiv.innerHTML = getAIResponse(userMessage);
                chatBox.scrollTop = chatBox.scrollHeight;
            }, 1000);
        }, 500);

        // Clear input field
        inputField.value = "";
    }

    function handleKeyPress(event) {
        if (event.key === "Enter") {
            sendMessage();
        }
    }

    function getAIResponse(userText) {
        userText = userText.toLowerCase();

        if (userText.includes("schedule") || userText.includes("booking")) {
            let response = "<strong>📅 Your Upcoming Bookings:</strong><br>";

            if (bookings.length === 0) {
                response += "❌ No bookings found.";
            } else {
                bookings.forEach(b => {
                    response += `📍 ${b.classroom} - ${b.date} at ${b.time_from} to ${b.time_to}<br>`;
                });
            }
            return response;
        } else if (userText.includes("hello") || userText.includes("hi")) {
            return "👋 Hello! How can I assist you today?";
        } else {
            return "🤔 I'm not sure about that. Try asking about your schedule or bookings.";
        }
    }
</script>

</body>
</html>
