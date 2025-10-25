document.addEventListener("DOMContentLoaded", async () => {
  const contactList = document.querySelector(".contact-list");
  const chatHeader = document.getElementById("chat-header");
  const chatMessages = document.getElementById("chat-messages");
  const messageInput = document.getElementById("message-input");
  const sendButton = document.getElementById("send-btn");

  let selectedContact = null;
  let currentUser = null;
  let messageRefreshInterval = null;

  // Get current logged-in user
  try {
    const res = await fetch("../Admin/Php/get_logged_user.php");
    const data = await res.json();

    if (data.error) {
      alert("Please log in to access messages");
      window.location.href = "../index.html";
      return;
    }

    currentUser = data;

    // Check if user has permission to access messages
    const allowedRoles = ['admin', 'teacher', 'studentcouncil'];
    if (!allowedRoles.includes(data.role.toLowerCase())) {
      alert("You don't have permission to access the messaging feature");
      window.history.back();
      return;
    }

  } catch (err) {
    console.error("Failed to get user info:", err);
    alert("Error loading user information");
    return;
  }

  // Load contacts
  async function loadContacts() {
    try {
      const res = await fetch("../Admin/Php/get_contacts.php");
      const data = await res.json();

      if (!data.success) {
        contactList.innerHTML = `<p style="color:#f44;">Error: ${data.message}</p>`;
        return;
      }

      contactList.innerHTML = "";

      if (data.contacts.length === 0) {
        contactList.innerHTML = "<p style='color:#999;'>No contacts available</p>";
        return;
      }

      data.contacts.forEach(contact => {
        const btn = document.createElement("button");
        btn.classList.add("contact");
        btn.dataset.id = contact.id;
        btn.dataset.type = contact.type;

        if (contact.type === 'club') {
          btn.innerHTML = `<span>📢 ${contact.fullname}</span>`;
        } else {
          btn.innerHTML = `
            <span>${contact.fullname}</span>
            <small style="display:block;font-size:0.8em;opacity:0.7;">${contact.role}</small>
          `;
        }

        btn.addEventListener("click", () => {
          // Remove active class from all contacts
          document.querySelectorAll('.contact').forEach(c => c.classList.remove('active'));
          btn.classList.add('active');

          selectedContact = contact;
          chatHeader.textContent = contact.type === 'club'
            ? `${contact.fullname}`
            : `Chat with ${contact.fullname} (${contact.role})`;

          messageInput.disabled = false;
          sendButton.disabled = false;

          loadMessages();

          // Start auto-refresh for this conversation
          if (messageRefreshInterval) {
            clearInterval(messageRefreshInterval);
          }
          messageRefreshInterval = setInterval(loadMessages, 3000); // Refresh every 3 seconds
        });

        contactList.appendChild(btn);
      });

    } catch (err) {
      console.error("Error loading contacts:", err);
      contactList.innerHTML = "<p style='color:red;'>Error loading contacts</p>";
    }
  }

  // Load messages for selected contact
  async function loadMessages() {
    if (!selectedContact) return;

    try {
      const res = await fetch("../Admin/Php/get_conversation.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
          contact_id: selectedContact.id,
          type: selectedContact.type
        })
      });

      const data = await res.json();

      if (data.success) {
        chatMessages.innerHTML = "";

        if (data.messages.length === 0) {
          chatMessages.innerHTML = "<p class='placeholder' style='color:#999;'>No messages yet. Start the conversation!</p>";
          return;
        }

        data.messages.forEach(msg => {
          const div = document.createElement("div");
          div.classList.add("message");
          div.classList.add(msg.is_own ? "outgoing" : "incoming");

          const time = new Date(msg.timestamp).toLocaleTimeString([], {
            hour: '2-digit',
            minute: '2-digit'
          });

          if (selectedContact.type === 'club' && !msg.is_own) {
            // Show sender name in group chats
            div.innerHTML = `
              <div style="font-size:0.8em;opacity:0.8;margin-bottom:3px;">${msg.sender_name}</div>
              <div>${msg.message}</div>
              <div style="font-size:0.75em;opacity:0.6;margin-top:3px;">${time}</div>
            `;
          } else {
            div.innerHTML = `
              <div>${msg.message}</div>
              <div style="font-size:0.75em;opacity:0.6;margin-top:3px;">${time}</div>
            `;
          }

          chatMessages.appendChild(div);
        });

        // Scroll to bottom
        chatMessages.scrollTop = chatMessages.scrollHeight;

      } else {
        chatMessages.innerHTML = `<p style='color:#f44;'>Error: ${data.message}</p>`;
      }
    } catch (err) {
      console.error("Error loading messages:", err);
      chatMessages.innerHTML = "<p style='color:red;'>Error loading messages</p>";
    }
  }

  // Send message
  async function sendMessage() {
    if (!selectedContact) {
      alert("Please select a contact first");
      return;
    }

    const message = messageInput.value.trim();
    if (!message) return;

    try {
      const res = await fetch("../Admin/Php/send_message_new.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
          contact_id: selectedContact.id,
          type: selectedContact.type,
          message: message
        })
      });

      const data = await res.json();

      if (data.success) {
        messageInput.value = "";
        loadMessages(); // Refresh messages
      } else {
        alert("Failed to send message: " + data.message);
      }
    } catch (err) {
      console.error("Error sending message:", err);
      alert("Error sending message");
    }
  }

  // Event listeners
  sendButton.addEventListener("click", sendMessage);

  messageInput.addEventListener("keypress", (e) => {
    if (e.key === "Enter" && !e.shiftKey) {
      e.preventDefault();
      sendMessage();
    }
  });

  // Clean up interval on page unload
  window.addEventListener("beforeunload", () => {
    if (messageRefreshInterval) {
      clearInterval(messageRefreshInterval);
    }
  });

  // Initial load
  loadContacts();
});