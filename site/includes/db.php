<?php

require_once('config.php');
require_once('secrets.php');

function db_connect() {
  mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
  return new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
}

function db_check_member_creds($email, $password) {
  $mysqli = db_connect();

  $stmt = $mysqli->prepare("SELECT password FROM members WHERE email = ?");
  $stmt->bind_param("s", $email);
  $stmt->execute();
  $stmt->bind_result($hash);
  if (!$stmt->fetch()) {
    // No such user
    return false;
  }
  $stmt->close();

  return password_verify($password, $hash);
}

function db_insert_session($session_token, $email, $expires_at) {
  $mysqli = db_connect();

  $stmt = $mysqli->prepare("INSERT INTO sessions (session_id, expires_at, badge_no) SELECT ?, ?, badge_no FROM members WHERE email = ?");
  $expires_at_ts = date('Y-m-d H:i:s', $expires_at);
  $stmt->bind_param("sss", $session_token, $expires_at_ts, $email);
  $stmt->execute();
  $stmt->close();
}

function db_session_exists($session_token) {
  $mysqli = db_connect();

  $stmt = $mysqli->prepare("SELECT 1 FROM sessions WHERE session_id = ? AND expires_at > NOW()");
  $stmt->bind_param("s", $session_token);
  $stmt->execute();
  $stmt->bind_result($email);
  if (!$stmt->fetch()) {
    // No such session
    return false;
  }
  $stmt->close();

  return true;
}

function db_get_badge_no_by_session($session_token) {
  $mysqli = db_connect();

  $stmt = $mysqli->prepare("SELECT badge_no FROM sessions WHERE session_id = ? AND expires_at > NOW()");
  $stmt->bind_param("s", $session_token);
  $stmt->execute();
  $stmt->bind_result($badge_no);
  if (!$stmt->fetch()) {
    // No such session
    return null;
  }
  $stmt->close();

  return $badge_no;
}

function db_get_user_name_by_session($session_token) {
  $mysqli = db_connect();

  $stmt = $mysqli->prepare("SELECT name FROM members WHERE badge_no = (SELECT badge_no FROM sessions WHERE session_id = ? AND expires_at > NOW())");
  $stmt->bind_param("s", $session_token);
  $stmt->execute();
  $stmt->bind_result($name);
  if (!$stmt->fetch()) {
    // No such session
    return null;
  }
  $stmt->close();

  return $name;
}

function db_delete_session($session_token) {
  $mysqli = db_connect();

  $stmt = $mysqli->prepare("DELETE FROM sessions WHERE session_id = ?");
  $stmt->bind_param("s", $session_token);
  $stmt->execute();
  $stmt->close();
}

function db_create_member($badge_id, $name, $email, $type, $invite_url) {
  $mysqli = db_connect();

  $mysqli->begin_transaction();
  try {
    $members_stmt = $mysqli->prepare("INSERT INTO members (badge_no, name, type, email) VALUES (?, ?, ?, ?)");
    $members_stmt->bind_param("ssss", $badge_id, $name, $type, $email);
    $members_stmt->execute();
    $members_stmt->close();

    $invites_stmt = $mysqli->prepare("INSERT INTO hopin_invites (badge_no, invite_url) VALUES (?, ?)");
    $invites_stmt->bind_param("ss", $badge_id, $invite_url);
    $invites_stmt->execute();
    $invites_stmt->close();
  
    $mysqli->commit();
  } catch (mysqli_sql_exception $exception) {
    $mysqli->rollback();
    throw $exception;
  }
}

function db_get_magic_link($session_token) {
  $mysqli = db_connect();

  $stmt = $mysqli->prepare("SELECT invite_url FROM hopin_invites WHERE badge_no = (SELECT badge_no FROM sessions WHERE session_id = ? AND expires_at > NOW())");
  $stmt->bind_param("s", $session_token);
  $stmt->execute();
  $stmt->bind_result($invite_url);
  if (!$stmt->fetch()) {
    // No such session
    return null;
  }
  $stmt->close();

  return $invite_url;
}

function db_set_discord_id($badge_no, $discord_id, $discord_username) {
  $mysqli = db_connect();

  $stmt = $mysqli->prepare("REPLACE INTO discord_ids(badge_no, discord_id, username) VALUES (?, ?, ?)");
  $stmt->bind_param("sss", $badge_no, $discord_id, $discord_username);
  $stmt->execute();
  $stmt->close();
}

function db_member_exists($email) {
  $mysqli = db_connect();

  $stmt = $mysqli->prepare("SELECT 1 FROM members WHERE email = ?");
  $stmt->bind_param("s", $email);
  $stmt->execute();
  $stmt->bind_result($exists);
  $stmt->fetch();
  $stmt->close();

  return $exists;
}

function db_insert_login_link($email, $login_code, $expires_at) {
  $mysqli = db_connect();

  $expires_at_ts = date('Y-m-d H:i:s', $expires_at);
  $stmt = $mysqli->prepare("INSERT INTO login_links (login_code, badge_no, expires_at) SELECT ?, badge_no, ? FROM members WHERE email = ?");
  $stmt->bind_param("sss", $login_code, $expires_at_ts, $email);
  $stmt->execute();
  $stmt->close();
}

function db_get_login_link_expiry($email, $login_code) {
  $mysqli = db_connect();

  $stmt = $mysqli->prepare("SELECT expires_at FROM login_links WHERE login_code = ? AND badge_no = (SELECT badge_no FROM members WHERE email = ?)");
  $stmt->bind_param("ss", $login_code, $email);
  $stmt->execute();
  $stmt->bind_result($expires_at);
  if (!$stmt->fetch()) {
    // No such link
    return null;
  }
  $stmt->close();

  return strtotime($expires_at);
}

function db_has_permission($session_id, $permission) {
  $mysqli = db_connect();

  $stmt = $mysqli->prepare("SELECT 1 FROM roles_permissions JOIN users_roles USING (role_id) JOIN sessions USING (badge_no) WHERE session_id = ? AND permission = ?");
  $stmt->bind_param("ss", $session_id, $permission);
  $stmt->execute();
  $stmt->bind_result($has_permission);
  $stmt->fetch();
  $stmt->close();

  return $has_permission;
}

function db_get_member_discord_data() {
  $mysqli = db_connect();

  $result = $mysqli->query("SELECT members.badge_no, name, discord_ids.discord_id, discord_ids.username from members LEFT OUTER JOIN discord_ids USING (badge_no) ORDER by badge_no");
  $members = [];
  $cur_member = null;
  while ($row = $result->fetch_assoc()) {
    if ($cur_member && $cur_member['badge_no'] === $row['badge_no']) {
      $cur_member['discord_ids'][] = ['id' => $row['discord_id'], 'username' => $row['username']];
    } else {
      if ($cur_member) {
        $members[] = $cur_member;
      }
      $cur_member = ['badge_no' => $row['badge_no'], 'name' => $row['name'], 'discord_ids' => []];
      if ($row['discord_id']) {
        $cur_member['discord_ids'][] = ['id' => $row['discord_id'], 'username' => $row['username']];
      }
    }
  }
  if ($cur_member) {
    $members[] = $cur_member;
  }
  $result->close();

  return $members;
}

function db_get_discord_ids() {
  $mysqli = db_connect();

  $result = $mysqli->query("SELECT discord_id FROM discord_ids");
  $discord_ids = [];
  while ($row = $result->fetch_assoc()) {
    $discord_ids[] = $row['discord_id'];
  }
  $result->close();

  return $discord_ids;
}

function db_get_hopin_links() {
  $mysqli = db_connect();

  $result = $mysqli->query("SELECT members.badge_no, name, invite_url from members LEFT OUTER JOIN hopin_invites USING (badge_no) ORDER by badge_no");
  $members = [];
  while ($row = $result->fetch_assoc()) {
    $members[] = ['badge_no' => $row['badge_no'], 'name' => $row['name'], 'invite_url' => $row['invite_url']];
  }
  $result->close();

  return $members;
}

?>