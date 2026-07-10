const Ajax = {
  async get(url) {
    const response = await fetch(url, { credentials: 'same-origin' });
    return response.json();
  },
  async post(url, data = {}) {
    const body = new URLSearchParams(data);
    const response = await fetch(url, {
      method: 'POST',
      credentials: 'same-origin',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body,
    });
    return response.json();
  },
};
