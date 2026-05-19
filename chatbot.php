<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>College Chatbot | Peach Theme Dashboard</title>
<style>
  body {
    background: linear-gradient(120deg, #ffe0d1, #ebb9b0);
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    margin: 0;
    font-family: 'Poppins', sans-serif;
  }

  .main-container {
    display: flex;
    gap: 20px;
    width: 90%;
    max-width: 1100px;
    height: 90vh;
    flex-wrap: wrap;
  }

  .chat-container, .faq-container {
    background: #ffffff;
    border-radius: 20px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.15);
    overflow: hidden;
    display: flex;
    flex-direction: column;
  }

  .chat-container {
    flex: 2;
    min-width: 340px;
    height: 100%;
  }

  .faq-container {
    flex: 1;
    min-width: 280px;
    height: 100%;
    background: #fff7f4;
  }

  .chat-header {
    background: linear-gradient(120deg, #ffe0d1, #ebb9b0);
    color: #6A1E1E;
    padding: 18px;
    text-align: center;
    font-weight: 600;
    font-size: 1.3rem;
    border-radius: 20px 20px 0 0;
  }

  .selectors {
    display: flex;
    gap: 10px;
    justify-content: center;
    padding: 12px 0;
    background: #fff2ee;
    border-bottom: 1px solid #ffccbf;
    flex-wrap: wrap;
  }

  .selectors select {
    padding: 10px 15px;
    border-radius: 10px;
    border: 1px solid #ffc7b8;
    outline: none;
    font-size: 0.95rem;
    background: white;
    cursor: pointer;
  }

  .selectors select:hover {
    border-color: #e48a8a;
    box-shadow: 0 0 6px rgba(250, 170, 170, 0.6);
  }

  .selectors button {
    padding: 10px 20px;
    border-radius: 10px;
    border: none;
    background: linear-gradient(120deg, #ffe0d1, #ebb9b0);
    color: #6A1E1E;
    font-weight: 600;
    cursor: pointer;
  }

  .selectors button:hover {
    opacity: 0.85;
  }

  .chat-messages {
    flex: 1;
    padding: 15px;
    overflow-y: auto;
    background: #fff7f4;
  }

  .message {
    max-width: 75%;
    margin: 8px 0;
    padding: 12px 15px;
    border-radius: 15px;
    display: inline-block;
    clear: both;
    word-wrap: break-word;
    font-size: 0.95rem;
  }

  .bot {
    background: #ffeae4;
    color: #333;
    float: left;
  }

  .user {
    background: linear-gradient(120deg, #ffe0d1, #ebb9b0);
    color: #6A1E1E;
    float: right;
  }

  .chat-input {
    display: flex;
    border-top: 1px solid #ffcdc0;
    background: #fff2ee;
  }

  .chat-input input {
    flex: 1;
    padding: 12px 15px;
    border: none;
    outline: none;
    background: transparent;
    font-size: 0.95rem;
  }

  .chat-input button {
    background: linear-gradient(120deg, #ffe0d1, #ebb9b0);
    color: #6A1E1E;
    border: none;
    padding: 0 20px;
    cursor: pointer;
    border-radius: 0 0 20px 0;
  }

  .faq-header {
    background: linear-gradient(120deg, #ffe0d1, #ebb9b0);
    color: #6A1E1E;
    text-align: center;
    font-weight: 600;
    padding: 15px;
    font-size: 1.1rem;
  }

  .faq-list {
    flex: 1;
    overflow-y: auto;
    padding: 15px;
  }

  .faq-item {
    background: #fff7f4;
    margin-bottom: 10px;
    padding: 10px 15px;
    border-radius: 10px;
    border-left: 4px solid #e48a8a;
    cursor: pointer;
  }

  .faq-item:hover {
    background: #ffeae4;
  }

  @media (max-width: 900px) {
    .main-container {
      flex-direction: column;
      align-items: center;
    }
    .chat-container, .faq-container {
      width: 100%;
      height: 50%;
    }
    .faq-container {
      order: 2;
    }
  }
</style>
</head>
<body>

<div class="main-container">
  <div class="chat-container">
    <div class="chat-header">🏛️ College Chatbot</div>

    <div class="selectors">
      <select id="stream">
        <option value="BSC IT">BSC IT</option>
        <option value="BSC CS">BSC CS</option>
        <option value="DSAI">DSAI</option>
      </select>
      <select id="year">
        <option value="First Year">First Year</option>
        <option value="Second Year">Second Year</option>
        <option value="Third Year">Third Year</option>
      </select>
      <button id="setContextBtn">Set Context</button>
    </div>

    <div class="chat-messages" id="chatMessages">
      <div class="message bot">Please set stream and year to start.</div>
    </div>

    <div class="chat-input">
      <input type="text" id="chatInput" placeholder="Type a message..." />
      <button id="sendBtn">Send</button>
    </div>
  </div>

  <div class="faq-container">
    <div class="faq-header">📜 FAQs List</div>
    <div class="faq-list" id="faqList">Loading FAQs...</div>
  </div>
</div>

<script>
  const input = document.getElementById("chatInput");
  const sendBtn = document.getElementById("sendBtn");
  const messages = document.getElementById("chatMessages");
  const setContextBtn = document.getElementById("setContextBtn");
  const streamSelect = document.getElementById("stream");
  const yearSelect = document.getElementById("year");
  const faqList = document.getElementById("faqList");

  let contextSet = false;
  let currentStream = "";
  let currentYear = "";

  function addMessage(text, sender) {
    const div = document.createElement("div");
    div.classList.add("message", sender);
    div.innerText = text;
    messages.appendChild(div);
    messages.scrollTop = messages.scrollHeight;
  }

  setContextBtn.addEventListener("click", async () => {
    const stream = streamSelect.value;
    const year = yearSelect.value;

    currentStream = stream;
    currentYear = year;

    const res = await fetch("coonect_chat.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ set_context: true, stream, year })
    });
    const data = await res.json();
    addMessage(data.answer, "bot");
    contextSet = true;

    loadFAQs(stream, year);
  });

  async function botReply(userText) {
    if (!contextSet) {
      addMessage("Please set stream and year first.", "bot");
      return;
    }

    const typingDiv = document.createElement("div");
    typingDiv.classList.add("message", "bot");
    typingDiv.innerText = "Typing...";
    messages.appendChild(typingDiv);

    try {
      const res = await fetch("chat.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ message: userText })
      });
      const data = await res.json();
      setTimeout(() => {
        typingDiv.innerHTML = data.answer;
        messages.scrollTop = messages.scrollHeight;
      }, 800);
    } catch (err) {
      typingDiv.innerHTML = "Oops! Something went wrong.";
    }
  }

  function handleSend() {
    const text = input.value.trim();
    if (text !== "") {
      addMessage(text, "user");
      input.value = "";
      botReply(text);
    }
  }

  sendBtn.addEventListener("click", handleSend);
  input.addEventListener("keypress", (e) => { if (e.key === "Enter") handleSend(); });

  async function loadFAQs(stream, year) {
    try {
      const res = await fetch(`get_faqs.php?stream=${encodeURIComponent(stream)}&year=${encodeURIComponent(year)}&t=${Date.now()}`);
      const data = await res.json();
      faqList.innerHTML = "";
      if (data.length > 0) {
        data.forEach(item => {
          const div = document.createElement("div");
          div.classList.add("faq-item");
          div.textContent = item.question;
          div.addEventListener("click", () => {
            addMessage(item.question, "user");
            botReply(item.question);
          });
          faqList.appendChild(div);
        });
      } else {
        faqList.innerHTML = "<p>No FAQs available.</p>";
      }
    } catch (err) {
      faqList.innerHTML = "<p>Error loading FAQs.</p>";
    }
  }
</script>
</body>
</html>
