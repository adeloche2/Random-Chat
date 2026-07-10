const messagesEl = document.querySelector('#messages');
const statusEl = document.querySelector('#status');
const typingEl = document.querySelector('#typing');
const timerEl = document.querySelector('#timer');
const form = document.querySelector('#chat-form');
const input = document.querySelector('#message');
const sendButton = form.querySelector('button[type="submit"]');
let lastMessageId = 0;
let typingTimer;

function setChatEnabled(enabled) {
  input.disabled = !enabled;
  sendButton.disabled = !enabled;
}

function addMessage(message) {
  const bubble = document.createElement('div');
  bubble.className = `message ${message.user_id === window.userId ? 'mine' : 'theirs'}`;
  bubble.textContent = message.message;
  messagesEl.appendChild(bubble);
  messagesEl.scrollTop = messagesEl.scrollHeight;
  lastMessageId = Math.max(lastMessageId, Number(message.id));
}

function updateTimer(seconds) {
  const minutes = String(Math.floor(seconds / 60)).padStart(2, '0');
  const remainder = String(seconds % 60).padStart(2, '0');
  timerEl.textContent = `${minutes}:${remainder}`;
}

async function connect() {
  statusEl.textContent = 'Looking for a stranger...';
  const data = await Ajax.post('api/connect.php');
  window.userId = data.user_id;
  handleStatus(data);
}

function handleStatus(data) {
  if (data.status === 'matched' || data.status === 'chatting') {
    statusEl.textContent = 'Connected to a stranger';
    setChatEnabled(true);
  } else {
    statusEl.textContent = 'Waiting for another user...';
    setChatEnabled(false);
  }
}

async function poll() {
  const data = await Ajax.get(`api/receive.php?after_id=${lastMessageId}`);
  handleStatus(data);
  data.messages.forEach(addMessage);
  typingEl.classList.toggle('d-none', !data.typing);
  updateTimer(data.remaining_seconds || 0);
}

form.addEventListener('submit', async (event) => {
  event.preventDefault();
  const message = input.value.trim();
  if (!message) return;
  await Ajax.post('api/send.php', { message });
  input.value = '';
  await poll();
});

input.addEventListener('input', () => {
  Ajax.post('api/typing.php', { typing: '1' });
  clearTimeout(typingTimer);
  typingTimer = setTimeout(() => Ajax.post('api/typing.php', { typing: '0' }), 1200);
});

document.querySelector('#next').addEventListener('click', async () => {
  messagesEl.innerHTML = '';
  lastMessageId = 0;
  handleStatus(await Ajax.post('api/next.php'));
});

document.querySelector('#disconnect').addEventListener('click', async () => {
  await Ajax.post('api/disconnect.php');
  statusEl.textContent = 'Disconnected';
  setChatEnabled(false);
});

connect();
setInterval(poll, 2000);
setInterval(() => Ajax.post('api/heartbeat.php'), 5000);
