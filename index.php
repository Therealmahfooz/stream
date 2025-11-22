<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <title>MD Watch Party</title>
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />
  <style>
    :root { color-scheme: dark; --primary: #e63946; --bg: #000; --panel: #101017; --border: #232336; }
    body { margin: 0; background: var(--bg); color: #eee; font-family: system-ui,sans-serif; min-height: 100vh; display: flex; flex-direction: column; overflow: hidden; }

    #landing-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: var(--bg); z-index: 1000; display: flex; flex-direction: column; align-items: center; justify-content: center; }
    .landing-box { background: var(--panel); padding: 30px; border-radius: 16px; border: 1px solid var(--border); text-align: center; max-width: 400px; width: 90%; }
    .action-btn { display: block; width: 100%; padding: 12px; margin: 10px 0; border: none; border-radius: 8px; font-size: 16px; font-weight: bold; cursor: pointer; }
    .btn-create { background: var(--primary); color: white; }
    .btn-join { background: #232336; color: #eee; border: 1px solid #36364c; }
    .join-input-group { display: none; margin-top: 15px; }
    .join-input-group input { width: 70%; padding: 10px; border-radius: 6px; border: 1px solid var(--border); background: #000; color: white; }
    
    header { padding: 0 16px; height: 50px; background: var(--panel); border-bottom: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; flex-shrink: 0;}
    .room-info { display: flex; align-items: center; gap: 10px; background: #16161b; padding: 4px 10px; border-radius: 6px; font-size: 13px; }
    .copy-btn { padding: 4px 8px; font-size: 12px; cursor: pointer; background: #18181d; border: 1px solid var(--border); color: #eee; border-radius: 4px; }

    #main-app { display: none; width: 100%; height: calc(100vh - 50px);}
    .layout-container { display: grid; grid-template-columns: 1fr 350px; grid-template-rows: 100%; height: 100%; width: 100%; }

    .video-section { display: flex; flex-direction: column; background: #000; overflow-y: auto; border-right: 1px solid var(--border);}
    .video-wrapper { width: 100%; background: #000; display: flex; align-items: center; justify-content: center; flex-shrink: 0;}
    video { width: 100%; max-height: 80vh; aspect-ratio: 16/9; background: #000;}
    .controls-bar { padding: 10px; background: var(--bg); display: flex; justify-content: center; gap: 20px; border-bottom: 1px solid var(--border);}
    .icon-btn { background: none; border: none; color: #eee; font-size: 24px; cursor: pointer; padding: 5px;}
    .icon-btn:active { transform: scale(0.9);}
    .icon-play { color: var(--primary); font-size: 30px;}

    .settings-area { padding: 15px; color: #aaa; font-size: 14px; }
    details { background: var(--panel); border: 1px solid var(--border); border-radius: 8px; padding: 10px; margin-bottom: 10px;}
    summary { cursor: pointer; font-weight: bold; color: #eee; list-style: none; display: flex; justify-content: space-between; align-items: center; }
    summary::after { content: "▼"; font-size: 10px;}
    details[open] summary::after { content: "▲"; }
    
    .chat-section { display: flex; flex-direction: column; background: var(--panel); height: 100%;}
    .chat-header { padding: 10px; font-weight: bold; border-bottom: 1px solid var(--border); background: #111116; text-align: center; }
    .chat-box { flex: 1; overflow-y: auto; padding: 10px; display: flex; flex-direction: column; gap: 8px;}
    .chat-input-area { padding: 10px; background: #000; border-top: 1px solid var(--border); display: flex; gap: 8px; position: relative; }
    .chat-input-area input { flex: 1; background: #15151a; border: 1px solid var(--border); color: white; padding: 10px; border-radius: 6px;}
    .chat-msg { background: #18181e; padding: 8px 12px; border-radius: 8px; font-size: 13px; align-self: flex-start; max-width: 90%; word-break: break-word;}
    .chat-msg strong { display: block; font-size: 11px; margin-bottom: 2px; }
    .chat-msg .username-red { color: #e63946; }
    .chat-msg .username-white { color: #fff; }
    .btn-primary { background: var(--primary); color: white; border: none; padding: 0 15px; border-radius: 6px; cursor: pointer;}

    @media (max-width: 768px) {
      body { overflow: auto; }
      #main-app { height: auto; min-height: 100vh;}
      .layout-container { display: flex; flex-direction: column; height: auto; }
      .video-section { border-right: none; border-bottom: 1px solid var(--border);}
      .video-wrapper { position: sticky; top: 0; z-index: 1; background: #000;}
      .chat-section { min-height: 300px; height: 400px; width: 100%; flex: 1;}
      .controls-bar { padding: 8px; gap: 15px;}
      .icon-btn { font-size: 22px;}
      .icon-play { font-size: 26px;}
      .settings-area { padding: 10px;}
    }
    .hidden { display: none; }
  </style>
</head>
<body>
  <div id="landing-overlay">
    <div class="landing-box">
      <h1 style="margin:0; color:#e63946;">MD Watch Party 🍿</h1>
      <p style="color:#aaa; margin-top:5px;">Watch parties made simple.</p>
      <button class="action-btn btn-create" id="btnCreateRoom">Create Room</button>
      <button class="action-btn btn-join" id="btnShowJoin">Join Room</button>
      <div class="join-input-group" id="joinGroup">
        <input type="text" id="landingRoomInput" placeholder="Enter Code..." />
        <button class="btn-primary" id="btnEnterRoom" style="padding:10px;">Go</button>
      </div>
    </div>
  </div>

  <div id="main-app">
    <header>
      <strong>MD Watch Party</strong>
      <div class="room-info">
        <span>Code: <strong id="displayRoomCode">...</strong></span>
        <span style="margin-left:10px; color:#4cc9f0;">👥 <strong id="userCount">1</strong></span>
        <button class="copy-btn" id="btnCopyLink" style="margin-left:10px;">📋</button>
      </div>
    </header>
    <div class="layout-container">
      <div class="video-section">
        <div class="video-wrapper">
          <video id="player" playsinline></video>
        </div>
        <div class="controls-bar">
          <button id="rew10" class="icon-btn">⏪</button>
          <button id="sendPlay" class="icon-btn icon-play">▶️</button>
          <button id="sendPause" class="icon-btn">⏸️</button>
          <button id="ff10" class="icon-btn">⏩</button>
          <button id="reloadSrc" class="icon-btn" style="font-size:18px; opacity:0.7;">🔄</button>
        </div>
        <div class="settings-area">
          <details>
            <summary>📂 Upload Video Source</summary>
            <form id="uploadForm" enctype="multipart/form-data" style="margin-top:10px; display:flex; flex-direction:column; gap:10px;">
              <input type="file" id="file" name="video" accept="video/*, .mkv" required style="background:#000; border:1px solid #333; padding:5px; border-radius:4px; width:90%;" />
              <button type="submit" id="uploadBtn" class="btn-primary" style="padding:10px;">Upload & Play</button>
              <p style="font-size:11px; margin:0;">Supports MP4, WebM, MKV. (Uses upload.php - **Note: You need to create this file**)</p>
            </form>
          </details>

          <details style="margin-top:10px;">
              <summary>🔗 Set Video Link (Drive/Dropbox/Direct)</summary>
              <form id="linkForm" style="margin-top:10px; display:flex; flex-direction:column; gap:10px;">
                  <input type="url" id="videoUrlInput" placeholder="Paste Video URL here..." required style="background:#000; border:1px solid #333; padding:10px; border-radius:4px; width:90%;" />
                  <button type="submit" id="setLinkBtn" class="btn-primary" style="padding:10px;">Set Link & Play</button>
                  <p style="font-size:11px; margin:0; color:#ffb703;">*Direct links are best. For Drive/Dropbox, ensure the link points to the *video file* and is public.</p>
              </form>
          </details>
          </div>
      </div>
      <div class="chat-section">
        <div class="chat-header">Live Chat 💬</div>
        <div class="chat-box" id="chatBox">
          <div style="text-align:center; color:#555; font-size:12px; margin-top:20px;">Say Hi! 👋</div>
        </div>
        <form id="chatForm" class="chat-input-area" autocomplete="off">
          <input type="text" id="chatUser" placeholder="Name" style="width:30%;" required />
          <input type="text" id="chatMsg" placeholder="Message..." required autocomplete="off"/>
          <button type="submit" class="btn-primary">➤</button>
        </form>
      </div>
    </div>
  </div>
  <script>
    let ROOM = null;
    const player = document.getElementById('player');
    const uploadForm = document.getElementById('uploadForm');
    const fileInput = document.getElementById('file');
    const uploadBtn = document.getElementById('uploadBtn');
    let lastAppliedUrl = null;
    let applyingRemote = false;
    let lastChatId = 0;
    let myUserId = sessionStorage.getItem('myUserId');
    if (!myUserId) { myUserId = 'u_' + Math.random().toString(36).substr(2, 5); sessionStorage.setItem('myUserId', myUserId); }
    let userList = []; // Store users for coloring

    function initRoom() {
      const urlParams = new URLSearchParams(window.location.search);
      const roomParam = urlParams.get('room');
      if (roomParam) enterRoom(roomParam);
      else document.getElementById('landing-overlay').style.display = 'flex';
    }

    function generateRoomCode() { return Math.random().toString(36).substring(2, 8).toUpperCase(); }
    document.getElementById('btnCreateRoom').addEventListener('click', () => {
      const newCode = generateRoomCode();
      const newUrl = window.location.protocol + "//" + window.location.host + window.location.pathname + '?room=' + newCode;
      window.history.pushState({path: newUrl}, '', newUrl);
      enterRoom(newCode);
    });
    document.getElementById('btnShowJoin').addEventListener('click', () => {
      document.getElementById('btnCreateRoom').classList.add('hidden');
      document.getElementById('btnShowJoin').classList.add('hidden');
      document.getElementById('joinGroup').style.display = 'block';
    });
    document.getElementById('btnEnterRoom').addEventListener('click', () => {
      const code = document.getElementById('landingRoomInput').value.trim();
      if(code) {
        const newUrl = window.location.protocol + "//" + window.location.host + window.location.pathname + '?room=' + code;
        window.history.pushState({path: newUrl}, '', newUrl);
        enterRoom(code);
      }
    });

    function enterRoom(roomCode) {
      ROOM = roomCode;
      document.getElementById('displayRoomCode').textContent = ROOM;
      document.getElementById('landing-overlay').style.display = 'none';
      document.getElementById('main-app').style.display = 'block';
      fetchState(true);
      setInterval(() => fetchState(false), 1000);
      setInterval(fetchMessages, 1000);
      setInterval(() => { if (!player.paused && !applyingRemote) sendState('play', player.currentTime); }, 2000);
    }
    document.getElementById('btnCopyLink').addEventListener('click', () => {
      navigator.clipboard.writeText(window.location.href).then(() => alert('Link Copied!'));
    });

    // Controls
    document.getElementById('sendPlay').addEventListener('click', () => { player.play(); sendState('play', player.currentTime); });
    document.getElementById('sendPause').addEventListener('click', () => { player.pause(); sendState('pause', player.currentTime); });
    document.getElementById('rew10').addEventListener('click', () => { player.currentTime = Math.max(0, player.currentTime - 10); sendState('seek', player.currentTime); });
    document.getElementById('ff10').addEventListener('click', () => { player.currentTime += 10; sendState('seek', player.currentTime); });
    document.getElementById('reloadSrc').addEventListener('click', () => fetchState(true));

    player.addEventListener('play', () => { if(!applyingRemote) sendState('play', player.currentTime); });
    player.addEventListener('pause', () => { if(!applyingRemote) sendState('pause', player.currentTime); });
    player.addEventListener('seeked', () => { if(!applyingRemote) sendState('seek', player.currentTime); });

    async function sendState(status, time) {
      if(applyingRemote) return;
      const body = new URLSearchParams({ room: ROOM, status, time: String(time || 0) });
      try { await fetch('update.php', { method: 'POST', headers: { 'Content-Type': 'application/x-www-form-urlencoded' }, body }); } catch (e) {}
    }

    async function fetchState(force) {
      try {
        const res = await fetch(`fetch.php?room=${encodeURIComponent(ROOM)}&user_id=${myUserId}`);
        const { ok, data } = await res.json();
        if (!ok || !data) return;
        if (data.online_users) document.getElementById('userCount').textContent = data.online_users;
        if (data.video_url && data.video_url !== lastAppliedUrl) {
          lastAppliedUrl = data.video_url;
          applyingRemote = true; player.src = data.video_url; try { await player.load(); } catch(e){} applyingRemote = false;
        }
        const serverTime = Number(data.current_time || 0);
        const drift = Math.abs(player.currentTime - serverTime);
        if (data.status === 'pause') {
            if (!player.paused) { applyingRemote = true; player.pause(); player.currentTime = serverTime; applyingRemote = false; }
            else if (drift > 0.5) { applyingRemote = true; player.currentTime = serverTime; applyingRemote = false; }
        } else if (data.status === 'play') {
          if (player.paused) { applyingRemote = true; player.currentTime = serverTime; player.play().catch(e=>{}); applyingRemote = false; }
          else if (drift > 10) { applyingRemote = true; player.currentTime = serverTime; applyingRemote = false; }
        }
      } catch (e) {}
    }

    // 📂 Upload Form Handler (e.preventDefault() added/confirmed)
    uploadForm.addEventListener('submit', async (e) => {
      e.preventDefault(); // <--- FIX: Prevents page reload
      const file = fileInput.files[0];
      if (!file) return alert('Select a file');
      const fd = new FormData(); fd.append('video', file); fd.append('room', ROOM);
      uploadBtn.textContent = "Uploading... ⏳"; uploadBtn.disabled = true;
      try {
        // NOTE: You need to create 'upload.php' to handle the file save and DB update
        const res = await fetch('upload.php', { method: 'POST', body: fd }); 
        const json = await res.json();
        if (!json.ok) throw new Error(json.error || 'Upload failed');
        alert('Upload Complete!'); fetchState(true);
      } catch (err) { alert('Error: ' + err.message); } finally { uploadBtn.textContent = "Upload & Play"; uploadBtn.disabled = false; }
    });

    // 🔗 Video Link Form Handler (NEW CODE)
    const linkForm = document.getElementById('linkForm');
    const videoUrlInput = document.getElementById('videoUrlInput');
    const setLinkBtn = document.getElementById('setLinkBtn');

    linkForm.addEventListener('submit', async (e) => {
      e.preventDefault(); // <--- Prevents page reload
      const url = videoUrlInput.value.trim();
      if (!url) return alert('Enter a valid URL');

      setLinkBtn.textContent = "Setting... ⏳";
      setLinkBtn.disabled = true;

      try {
          // set_url.php ko call kar ke link save karein
          const body = new URLSearchParams({ room: ROOM, url: url });
          const res = await fetch('set_url.php', {
              method: 'POST',
              headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
              body: body
          });
          const json = await res.json();
          if (!json.ok) throw new Error(json.error || 'Failed to set link');
          
          alert('Link Set! Please wait for sync.');
          fetchState(true); // New URL apply karne ke liye state fetch karein
      } catch (err) {
          alert('Error: ' + err.message);
      } finally {
          setLinkBtn.textContent = "Set Link & Play";
          setLinkBtn.disabled = false;
      }
    });

    const chatForm = document.getElementById('chatForm');
    const chatBox = document.getElementById('chatBox');
    const chatUser = document.getElementById('chatUser');
    const chatMsg = document.getElementById('chatMsg');
    if(localStorage.getItem('chatName')) chatUser.value = localStorage.getItem('chatName');

    // Keyboard-aware smooth chat experience
    function scrollChatToBottom() {
      chatBox.scrollTop = chatBox.scrollHeight;
    }

    // 💬 Chat Form Handler (e.preventDefault() added/confirmed)
    chatForm.addEventListener('submit', async (e) => {
      e.preventDefault(); // <--- FIX: Prevents page reload
      const user = chatUser.value.trim(); const msg = chatMsg.value.trim();
      if(!user || !msg) return;
      localStorage.setItem('chatName', user);
      chatMsg.value = '';
      chatMsg.blur(); // hides keyboard on mobile
      try {
        const fd = new FormData(); fd.append('room', ROOM); fd.append('user', user); fd.append('message', msg);
        await fetch('chat_send.php', { method: 'POST', body: fd }); fetchMessages();
      } catch(e) {}
    });

    let knownUserOrder = [];

    async function fetchMessages() {
      try {
        const res = await fetch(`chat_fetch.php?room=${encodeURIComponent(ROOM)}&lastId=${lastChatId}`);
        const data = await res.json();
        if(data.ok && data.messages.length > 0) {
          let shouldScroll = (chatBox.scrollTop + chatBox.clientHeight >= chatBox.scrollHeight - 50);
          data.messages.forEach(m => {
            // Determine user order for red/white name
            if (!knownUserOrder.includes(m.username)) knownUserOrder.push(m.username);
            const classN = knownUserOrder[0] === m.username ? "username-red" : "username-white";
            const div = document.createElement('div'); 
            div.className = 'chat-msg';
            div.innerHTML = `<strong class="${classN}">${m.username}</strong> ${m.message}`;
            chatBox.appendChild(div); lastChatId = m.id;
          });
          if(shouldScroll) scrollChatToBottom();
        }
      } catch(e) {}
    }

    // Keyboard input UX helper on mobile (chat always visible above keyboard)
    chatMsg.addEventListener('focus', () => {
      setTimeout(scrollChatToBottom, 150);
    });

    // Show main UI
    initRoom();
  </script>
</body>
</html>