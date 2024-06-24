<?php
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/template.php');

if (!current_user_has_permission('manage-programme')) {
  header('Location: /');
  exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $action = $_POST['action'] ?: null;
  $item_id = $_POST['item_id'] ?: null;
  $title = $_POST['title'] ?: null;
  $replay_url = $_POST['replay_url'];

  if ($action == "add") {
    try {
      db_add_replay($item_id, $title, $replay_url);
    } catch (Exception $e) {
      header('Location: /admin/programme/replay/list?error=' . $e->getMessage());
      throw $e;
    }
  } else if ($action == "delete") {
    try {
      db_delete_replay($item_id);
    } catch (Exception $e) {
      header('Location: /admin/programme/replay/list?error=' . $e->getMessage());
      throw $e;
    }
  } else if ($action == "edit") {
    try {
      db_edit_replay($item_id, $title, $replay_url);
    } catch (Exception $e) {
      header('Location: /admin/programme/feplay/list?error=' . $e->getMessage());
      throw $e;
    }
  }
  header('Location: /admin/programme/replay/list');
  exit;
}

if (array_key_exists('item_id', $_GET)) {
  $item_id = $_GET['item_id'];
  $replay = db_get_replay($item_id);
}

if (array_key_exists('item_id', $_GET)) {
  $title = 'Edit ' . $item_id;
} else {
  $title = 'Add replay';
}

render_header(
  'Add/edit a RCE replay',
  'Manage RCE replays.',
  [
    'Home' => '/',
    'Manage programme' => '/admin/programme/list',
    'Manage RCE replays' => '/admin/programme/replay/list',
    $title
  ]
);
?>
  <article>
    <h2><?php echo $title; ?></h2>
    <form action="/admin/programme/replay/edit" method="POST" class="vertical">
      <fieldset>
        <legend>Replay details</legend>
      <?php
if (array_key_exists('item_id', $_GET)) {
?>
        <input type="hidden" name="item_id" value="<?php echo $item_id; ?>">
        
        <input type="hidden" name="action" value="edit">
<?php
} else {
?>
        <input type="hidden" name="action" value="add">
        <p><label>Plano Item ID: <input name="item_id" required pattern="^[a-zA-Z0-9]{8}-[a-zA-Z0-9]{4}-[a-zA-Z0-9]{4}-[a-zA-Z0-9]{4}-[a-zA-Z0-9]{12}$"></label></p>
<?php
}
?>
        <p><label>Title: <input name="title" maxlength="256" value="<?php echo $replay['title']; ?>"></label></p>
        <p><label>Replay URL: <input type="text" name="replay_url" value="<?php echo $replay['replay_url']; ?>"></label></p>
      </fieldset>
      <input type="submit" value="Save">
      <?php
if (array_key_exists('item_id', $_GET)) {
?>
      <input id="delete" type="button" value="Delete">
<?php
}
?>
    </form>

    <script>
      var $delete = document.getElementById('delete');
      if ($delete) {
        $delete.addEventListener('click', function(e) {
          if (confirm('Are you sure you want to delete <?php echo $item_id; ?>?')) {
            var form = document.createElement('form');
            form.method = 'POST';
            form.action = '/admin/programme/replay/edit';
            var itemId = '<?php echo $item_id; ?>';
            var input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'action';
            input.value = 'delete';
            form.appendChild(input);
            var input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'item_id';
            input.value = itemId;
            form.appendChild(input);
            document.body.appendChild(form);
            form.submit();
          }
        });
      }
    </script>
    <article>
  <?php
  render_footer();
  ?>