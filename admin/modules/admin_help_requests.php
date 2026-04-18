<?php
if (session_status() === PHP_SESSION_NONE) session_start();
include '../includes/db.php';

// Send reply
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['admin_reply'])) {
    $reply   = $conn->real_escape_string(trim($_POST['admin_reply']));
    $status  = $conn->real_escape_string($_POST['status'] ?? 'In Progress');
    $userId  = intval($_POST['active_user'] ?? 0);
    $replyTo = intval($_POST['reply_to_id'] ?? 0);
    $reqId   = intval($_POST['request_id'] ?? 0);

    $conn->query("INSERT INTO help_requests (user_id, subject, message, admin_reply, status, sender, reply_to_id, replied_at, created_at)
        VALUES ($userId, 'Admin Reply', '$reply', '$reply', '$status', 'admin', " . ($replyTo ?: 'NULL') . ", NOW(), NOW())");

    if ($reqId) $conn->query("UPDATE help_requests SET status='$status' WHERE id=$reqId");
    header("Location: admin_help_requests.php?user=$userId"); exit;
}

// Users list
$users = $conn->query("SELECT DISTINCT hr.user_id, u.username, u.email,
    (SELECT COUNT(*) FROM help_requests WHERE user_id=hr.user_id AND sender='user' AND admin_reply IS NULL) as unread,
    (SELECT created_at FROM help_requests WHERE user_id=hr.user_id ORDER BY created_at DESC LIMIT 1) as last_msg
    FROM help_requests hr LEFT JOIN users u ON u.id=hr.user_id ORDER BY last_msg DESC");

$activeUserId = intval($_GET['user'] ?? 0);
if (!$activeUserId) {
    $first = $conn->query("SELECT DISTINCT user_id FROM help_requests ORDER BY created_at DESC LIMIT 1")->fetch_assoc();
    $activeUserId = intval($first['user_id'] ?? 0);
}

$chats = []; $activeUserInfo = [];
if ($activeUserId) {
    $r = $conn->query("SELECT * FROM help_requests WHERE user_id=$activeUserId ORDER BY created_at ASC");
    while ($row = $r->fetch_assoc()) $chats[] = $row;
    $activeUserInfo = $conn->query("SELECT username, email FROM users WHERE id=$activeUserId")->fetch_assoc();
}

$msgMap = [];
foreach ($chats as $c) $msgMap[$c['id']] = $c;

$lastUserMsgId = 0;
foreach (array_reverse($chats) as $c) {
    if ($c['sender'] === 'user') { $lastUserMsgId = $c['id']; break; }
}

include '../templates/sidebar.php';
include '../templates/header.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Support Chats - Admin</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">
  <style>
    :root {
      --dark-green:#075e54; --green:#25d366; --light-green:#dcf8c6;
      --bg:#efeae2; --white:#fff; --sidebar-admin:260px;
    }
    * { margin:0; padding:0; box-sizing:border-box; font-family:'Poppins',sans-serif; }
    html, body { height:100%; overflow:hidden; background:var(--bg); }

    /* Layout */
    .chat-layout { margin-left:var(--sidebar-admin); height:calc(100vh - 60px); display:flex; }

    /* ── User List ── */
    .user-list { width:300px; background:#fff; border-right:1px solid #e2e8f0; display:flex; flex-direction:column; flex-shrink:0; }
    .ul-head { padding:14px 16px; background:var(--dark-green); color:#fff; font-size:0.9rem; font-weight:700; display:flex; align-items:center; gap:8px; flex-shrink:0; }
    .ul-search { padding:8px 12px; border-bottom:1px solid #f1f5f9; flex-shrink:0; }
    .ul-search input { width:100%; padding:8px 12px; border:1px solid #e2e8f0; border-radius:20px; font-size:0.82rem; outline:none; background:#f8fafc; }
    .ul-body { flex:1; overflow-y:auto; }
    .ul-body::-webkit-scrollbar { width:4px; }
    .ul-body::-webkit-scrollbar-thumb { background:#e2e8f0; border-radius:4px; }

    .user-item { padding:12px 16px; border-bottom:1px solid #f8fafc; display:flex; align-items:center; gap:10px; text-decoration:none; transition:background 0.15s; cursor:pointer; }
    .user-item:hover { background:#f8fafc; }
    .user-item.active { background:#e7f3ef; border-left:3px solid var(--dark-green); }
    .u-av { width:42px; height:42px; border-radius:50%; background:linear-gradient(135deg,#6366f1,#4f46e5); color:#fff; display:flex; align-items:center; justify-content:center; font-size:16px; font-weight:700; flex-shrink:0; }
    .u-info { flex:1; min-width:0; }
    .u-name  { font-size:0.85rem; font-weight:600; color:#1e293b; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
    .u-preview { font-size:0.72rem; color:#94a3b8; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; margin-top:1px; }
    .u-meta { display:flex; flex-direction:column; align-items:flex-end; gap:4px; flex-shrink:0; }
    .u-time { font-size:0.65rem; color:#94a3b8; }
    .unread { background:#25d366; color:#fff; border-radius:50%; width:18px; height:18px; font-size:0.62rem; font-weight:700; display:flex; align-items:center; justify-content:center; }

    /* ── Chat Area ── */
    .chat-area { flex:1; display:flex; flex-direction:column; min-width:0; }

    .chat-head { background:var(--dark-green); padding:12px 18px; display:flex; align-items:center; gap:12px; flex-shrink:0; }
    .ch-back { display:none; color:#fff; cursor:pointer; margin-right:4px; }
    .ch-av { width:38px; height:38px; border-radius:50%; background:rgba(255,255,255,0.15); color:#fff; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
    .ch-info h3 { font-size:0.92rem; font-weight:700; color:#fff; }
    .ch-info p  { font-size:0.7rem; color:rgba(255,255,255,0.7); }

    /* Messages */
    .chat-body {
      flex:1; overflow-y:auto; padding:14px 18px; display:flex; flex-direction:column; gap:4px;
      background:var(--bg);
      background-image:url("data:image/svg+xml,%3Csvg width='80' height='80' viewBox='0 0 80 80' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='%23000' fill-opacity='0.03'%3E%3Cpath d='M0 0h40v40H0zm40 40h40v40H40z'/%3E%3C/g%3E%3C/svg%3E");
    }
    .chat-body::-webkit-scrollbar { width:5px; }
    .chat-body::-webkit-scrollbar-thumb { background:#ccc; border-radius:10px; }

    .date-div { text-align:center; margin:8px 0; }
    .date-div span { background:rgba(255,255,255,0.85); color:#667781; font-size:0.7rem; padding:3px 12px; border-radius:8px; box-shadow:0 1px 2px rgba(0,0,0,0.08); }

    .msg-wrap { display:flex; align-items:flex-end; gap:6px; max-width:72%; }
    .msg-wrap.user-msg  { align-self:flex-start; }
    .msg-wrap.admin-msg { align-self:flex-end; flex-direction:row-reverse; }

    .bav { width:28px; height:28px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:12px; flex-shrink:0; margin-bottom:2px; }
    .bav.u { background:#e2e8f0; color:#475569; }
    .bav.a { background:linear-gradient(135deg,#f59e0b,#d97706); color:#fff; }

    .bubble { padding:8px 12px 5px; border-radius:10px; font-size:0.85rem; line-height:1.55; position:relative; word-break:break-word; box-shadow:0 1px 2px rgba(0,0,0,0.1); cursor:pointer; }
    .bubble.user  { background:var(--white); color:#1e293b; border-bottom-left-radius:2px; }
    .bubble.admin { background:var(--light-green); color:#1e293b; border-bottom-right-radius:2px; }

    /* Hover reply icon */
    .bubble-wrap { position:relative; display:inline-block; }
    .reply-icon { display:none; position:absolute; top:50%; transform:translateY(-50%); background:#fff; border-radius:50%; width:26px; height:26px; align-items:center; justify-content:center; box-shadow:0 1px 4px rgba(0,0,0,0.2); cursor:pointer; z-index:1; }
    .msg-wrap.user-msg  .reply-icon { right:-32px; }
    .msg-wrap.admin-msg .reply-icon { left:-32px; }
    .bubble-wrap:hover .reply-icon { display:flex; }

    /* Quote */
    .quote-box { background:rgba(0,0,0,0.06); border-left:3px solid var(--dark-green); border-radius:5px; padding:5px 9px; margin-bottom:6px; font-size:0.75rem; color:#555; }
    .bubble.admin .quote-box { border-color:rgba(7,94,84,0.5); }
    .q-name { font-weight:700; color:var(--dark-green); font-size:0.7rem; margin-bottom:1px; }
    .q-text { white-space:nowrap; overflow:hidden; text-overflow:ellipsis; color:#667781; }

    /* Bubble footer */
    .bfoot { display:flex; align-items:center; justify-content:flex-end; gap:3px; margin-top:3px; }
    .btime { font-size:0.63rem; color:#94a3b8; }
    .bubble.admin .btime { color:#6b9e6b; }
    .s-pill { font-size:0.6rem; font-weight:700; padding:1px 6px; border-radius:8px; margin-left:3px; }
    .s-pill.in-progress { background:#dbeafe; color:#1e40af; }
    .s-pill.resolved    { background:#dcfce7; color:#166534; }
    .s-pill.pending     { background:#fef9c3; color:#92400e; }

    /* No reply indicator */
    .no-reply-row { align-self:flex-start; padding:2px 8px; font-size:0.72rem; color:#94a3b8; font-style:italic; }

    /* Footer */
    .chat-footer { background:#f0f0f0; padding:8px 12px; flex-shrink:0; display:flex; flex-direction:column; gap:6px; }

    .reply-bar { display:none; background:#fff; border-left:4px solid var(--dark-green); border-radius:8px; padding:7px 12px; }
    .reply-bar.show { display:flex; align-items:center; gap:10px; }
    .rb-content { flex:1; min-width:0; }
    .rb-name { font-weight:700; color:var(--dark-green); font-size:0.7rem; }
    .rb-text { font-size:0.75rem; color:#667781; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
    .rb-close { cursor:pointer; color:#94a3b8; flex-shrink:0; }

    .input-row { display:flex; align-items:flex-end; gap:8px; }
    .input-row textarea { flex:1; border:none; border-radius:22px; padding:10px 16px; font-size:0.88rem; font-family:'Poppins',sans-serif; resize:none; outline:none; max-height:120px; line-height:1.5; background:#fff; box-shadow:0 1px 3px rgba(0,0,0,0.08); }
    .input-row select { padding:8px 10px; border:none; border-radius:10px; font-size:0.75rem; outline:none; background:#fff; color:#374151; box-shadow:0 1px 3px rgba(0,0,0,0.08); }
    .send-btn { width:44px; height:44px; border-radius:50%; background:var(--dark-green); border:none; color:#fff; cursor:pointer; display:flex; align-items:center; justify-content:center; flex-shrink:0; box-shadow:0 2px 6px rgba(0,0,0,0.2); transition:background 0.2s; }
    .send-btn:hover { background:#128c7e; }

    /* No user */
    .no-user { flex:1; display:flex; flex-direction:column; align-items:center; justify-content:center; color:#94a3b8; gap:10px; }
    .no-user .material-icons-round { font-size:56px; opacity:0.3; }

    /* ── RESPONSIVE ── */
    @media(max-width:900px) {
      .chat-layout { margin-left:0; height:100vh; }
      .user-list { width:260px; }
    }
    @media(max-width:640px) {
      .chat-layout { position:relative; }
      .user-list { position:absolute; left:0; top:0; bottom:0; width:100%; z-index:10; transform:translateX(0); transition:transform 0.3s; }
      .user-list.hidden { transform:translateX(-100%); }
      .chat-area { width:100%; }
      .ch-back { display:flex !important; }
      .msg-wrap { max-width:88%; }
      .reply-icon { display:flex !important; }
    }
  </style>
</head>
<body>
<div class="chat-layout" id="chatLayout">

  <!-- User List -->
  <div class="user-list" id="userList">
    <div class="ul-head"><span class="material-icons-round">forum</span> Support Chats</div>
    <div class="ul-search"><input type="text" id="searchInput" placeholder="🔍 Search users..." oninput="filterUsers()"></div>
    <div class="ul-body" id="userListBody">
      <?php
      $userRows = [];
      while ($u = $users->fetch_assoc()) $userRows[] = $u;
      foreach ($userRows as $u):
        $lastTime = $u['last_msg'] ? date('h:i A', strtotime($u['last_msg'])) : '';
      ?>
      <a href="?user=<?= $u['user_id'] ?>" class="user-item <?= $u['user_id'] == $activeUserId ? 'active' : '' ?>"
         data-name="<?= htmlspecialchars(strtolower($u['username'] ?? '')) ?>"
         onclick="openChat(event, this)">
        <div class="u-av"><?= strtoupper(substr($u['username'] ?? 'U', 0, 1)) ?></div>
        <div class="u-info">
          <div class="u-name"><?= htmlspecialchars($u['username'] ?? 'User') ?></div>
          <div class="u-preview"><?= htmlspecialchars($u['email'] ?? '') ?></div>
        </div>
        <div class="u-meta">
          <span class="u-time"><?= $lastTime ?></span>
          <?php if ($u['unread'] > 0): ?>
            <span class="unread"><?= $u['unread'] ?></span>
          <?php endif; ?>
        </div>
      </a>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- Chat Area -->
  <div class="chat-area">
    <?php if ($activeUserId && $activeUserInfo): ?>

    <div class="chat-head">
      <span class="material-icons-round ch-back" onclick="showUserList()">arrow_back</span>
      <div class="ch-av"><span class="material-icons-round" style="font-size:18px">person</span></div>
      <div class="ch-info">
        <h3><?= htmlspecialchars($activeUserInfo['username'] ?? 'User') ?></h3>
        <p><?= htmlspecialchars($activeUserInfo['email'] ?? '') ?></p>
      </div>
    </div>

    <div class="chat-body" id="chatBody">
      <div class="date-div"><span>Conversation</span></div>

      <?php foreach ($chats as $chat):
        $isAdmin = ($chat['sender'] === 'admin');
        $msgText = $isAdmin ? ($chat['admin_reply'] ?? '') : $chat['message'];
        $replyToMsg = (!empty($chat['reply_to_id']) && isset($msgMap[$chat['reply_to_id']])) ? $msgMap[$chat['reply_to_id']] : null;
        $wrapClass = $isAdmin ? 'admin-msg' : 'user-msg';
        $bubbleClass = $isAdmin ? 'admin' : 'user';
        $time = date('h:i A', strtotime($chat['created_at']));
        $sc = strtolower(str_replace(' ','-',$chat['status']??'pending'));
      ?>
      <div class="msg-wrap <?= $wrapClass ?>" id="msg-<?= $chat['id'] ?>">
        <div class="bav <?= $isAdmin ? 'a' : 'u' ?>">
          <span class="material-icons-round" style="font-size:12px"><?= $isAdmin ? 'support_agent' : 'person' ?></span>
        </div>
        <div class="bubble-wrap">
          <div class="bubble <?= $bubbleClass ?>"
               data-id="<?= $chat['id'] ?>"
               data-text="<?= htmlspecialchars(mb_substr($msgText,0,60), ENT_QUOTES) ?>"
               data-name="<?= $isAdmin ? 'You' : htmlspecialchars($activeUserInfo['username']??'User', ENT_QUOTES) ?>">

            <?php if ($replyToMsg): ?>
              <?php $rText = $replyToMsg['sender']==='admin' ? ($replyToMsg['admin_reply']??'') : $replyToMsg['message']; ?>
              <div class="quote-box" onclick="scrollToMsg(<?= $replyToMsg['id'] ?>)">
                <div class="q-name"><?= $replyToMsg['sender']==='admin' ? 'You' : htmlspecialchars($activeUserInfo['username']??'User') ?></div>
                <div class="q-text"><?= htmlspecialchars(mb_substr($rText,0,55)) ?></div>
              </div>
            <?php endif; ?>

            <?= nl2br(htmlspecialchars($msgText)) ?>

            <div class="bfoot">
              <span class="btime"><?= $time ?></span>
            </div>
          </div>
          <div class="reply-icon" onclick="setReply('<?= $chat['id'] ?>','<?= htmlspecialchars(addslashes(mb_substr($msgText,0,60)), ENT_QUOTES) ?>','<?= $isAdmin ? 'You' : htmlspecialchars(addslashes($activeUserInfo['username']??'User'), ENT_QUOTES) ?>')">
            <span class="material-icons-round" style="font-size:14px;color:#555">reply</span>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>

    <!-- Footer -->
    <div class="chat-footer">
      <div class="reply-bar" id="replyBar">
        <div class="rb-content">
          <div class="rb-name" id="rbName"></div>
          <div class="rb-text" id="rbText"></div>
        </div>
        <span class="material-icons-round rb-close" onclick="clearReply()">close</span>
      </div>
      <form method="POST" id="replyForm">
        <input type="hidden" name="active_user" value="<?= $activeUserId ?>">
        <input type="hidden" name="request_id" value="<?= $lastUserMsgId ?>">
        <input type="hidden" name="reply_to_id" id="replyToId" value="">
        <div class="input-row">
          <textarea name="admin_reply" id="msgInput" placeholder="Type a reply..." rows="1" required
            onkeydown="if(event.key==='Enter'&&!event.shiftKey){event.preventDefault();document.getElementById('replyForm').submit();}"></textarea>
          <select name="status">
            <option value="In Progress">In Progress</option>
            <option value="Resolved">Resolved</option>
            <option value="Pending">Pending</option>
          </select>
          <button type="submit" class="send-btn"><span class="material-icons-round">send</span></button>
        </div>
      </form>
    </div>

    <?php else: ?>
    <div class="no-user">
      <span class="material-icons-round">forum</span>
      <p style="font-size:0.9rem">Select a user to start chatting</p>
    </div>
    <?php endif; ?>
  </div>

</div>

<script>
  // Scroll to bottom
  const cb = document.getElementById('chatBody');
  if (cb) cb.scrollTop = cb.scrollHeight;

  // Auto resize textarea
  const ta = document.getElementById('msgInput');
  if (ta) ta.addEventListener('input', function() {
    this.style.height = 'auto';
    this.style.height = Math.min(this.scrollHeight, 120) + 'px';
  });

  // Reply
  function setReply(id, text, name) {
    document.getElementById('replyToId').value = id;
    document.getElementById('rbName').textContent = name;
    document.getElementById('rbText').textContent = text + (text.length >= 60 ? '...' : '');
    document.getElementById('replyBar').classList.add('show');
    if (ta) ta.focus();
    const orig = document.getElementById('msg-' + id);
    if (orig) { orig.style.opacity='0.5'; setTimeout(()=>orig.style.opacity='1',900); }
  }

  function clearReply() {
    document.getElementById('replyToId').value = '';
    document.getElementById('replyBar').classList.remove('show');
  }

  function scrollToMsg(id) {
    const el = document.getElementById('msg-' + id);
    if (el) { el.scrollIntoView({behavior:'smooth',block:'center'}); el.style.opacity='0.4'; setTimeout(()=>el.style.opacity='1',900); }
  }

  // Mobile: show/hide user list
  function showUserList() {
    document.getElementById('userList').classList.remove('hidden');
  }
  function openChat(e, el) {
    if (window.innerWidth <= 640) {
      document.getElementById('userList').classList.add('hidden');
    }
  }

  // Search users
  function filterUsers() {
    const q = document.getElementById('searchInput').value.toLowerCase();
    document.querySelectorAll('.user-item').forEach(item => {
      item.style.display = item.dataset.name.includes(q) ? '' : 'none';
    });
  }

  // Auto hide user list on mobile if user selected
  if (window.innerWidth <= 640 && <?= $activeUserId ? 'true' : 'false' ?>) {
    document.getElementById('userList').classList.add('hidden');
  }

  // ── Real-time polling ──
  const activeUserId = <?= $activeUserId ?>;
  let lastMsgId = <?= !empty($chats) ? end($chats)['id'] : 0 ?>;
  const activeUname = <?= json_encode($activeUserInfo['username'] ?? '') ?>;

  function appendAdminMsg(chat) {
    const isAdmin = chat.sender === 'admin';
    const msgText = isAdmin ? (chat.admin_reply || '') : chat.message;
    const time = new Date(chat.created_at).toLocaleTimeString([], {hour:'2-digit',minute:'2-digit'});
    const wrapClass   = isAdmin ? 'admin-msg' : 'user-msg';
    const bubbleClass = isAdmin ? 'admin' : 'user';
    const bavClass    = isAdmin ? 'a' : 'u';
    const icon        = isAdmin ? 'support_agent' : 'person';
    const nameLabel   = isAdmin ? 'You' : activeUname;

    const div = document.createElement('div');
    div.className = 'msg-wrap ' + wrapClass;
    div.id = 'msg-' + chat.id;
    div.innerHTML = `
      <div class="bav ${bavClass}"><span class="material-icons-round" style="font-size:12px">${icon}</span></div>
      <div class="bubble-wrap">
        <div class="bubble ${bubbleClass}" data-id="${chat.id}" data-text="${msgText.substring(0,60).replace(/"/g,'&quot;')}" data-name="${nameLabel}">
          ${msgText.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/\n/g,'<br>')}
          <div class="bfoot"><span class="btime">${time}</span></div>
        </div>
        <div class="reply-icon" onclick="setReply('${chat.id}','${msgText.substring(0,60).replace(/'/g,"\\'").replace(/\n/g,' ')}','${nameLabel}')">
          <span class="material-icons-round" style="font-size:14px;color:#555">reply</span>
        </div>
      </div>`;

    const chatBody = document.getElementById('chatBody');
    chatBody.appendChild(div);
    chatBody.scrollTop = chatBody.scrollHeight;
    lastMsgId = chat.id;
  }

  function updateUnreadBadges(unreadMap) {
    document.querySelectorAll('.user-item').forEach(item => {
      const uid = new URL(item.href, location.origin).searchParams.get('user');
      const badge = item.querySelector('.unread');
      const cnt = unreadMap[uid] || 0;
      if (cnt > 0) {
        if (badge) { badge.textContent = cnt; }
        else {
          const meta = item.querySelector('.u-meta');
          if (meta) { const s = document.createElement('span'); s.className='unread'; s.textContent=cnt; meta.appendChild(s); }
        }
      } else {
        if (badge) badge.remove();
      }
    });
  }

  function pollAdmin() {
    fetch('get_chat_updates.php?user_id=' + activeUserId + '&after_id=' + lastMsgId)
      .then(r => r.json())
      .then(data => {
        if (data.messages) data.messages.forEach(appendAdminMsg);
        if (data.unread)   updateUnreadBadges(data.unread);
      })
      .catch(() => {});
  }

  if (activeUserId) setInterval(pollAdmin, 4000);
  else setInterval(() => {
    fetch('get_chat_updates.php?user_id=0&after_id=0')
      .then(r => r.json())
      .then(data => { if (data.unread) updateUnreadBadges(data.unread); })
      .catch(() => {});
  }, 5000);
</script>
</body>
</html>
