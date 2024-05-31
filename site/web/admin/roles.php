<?php
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/db.php');
require_once(getenv('CONFIG_LIB_DIR') . '/template.php');

if (!current_user_has_permission('manage-roles')) {
  header('Location: /');
  exit;
}

function render_form_body($role_id = 1) {
?>
    <select id="role_id" name="role_id" hx-get="/admin/roles" hx-trigger="change" hx-push-url="true" hx-target="closest form">
<?php
      $roles = db_get_roles();
      foreach ($roles as $role) {
        $selected = ($role['role_id'] == $role_id) ? 'selected' : '';
?>
          <option value="<?php echo $role['role_id']; ?>"<?php echo $selected; ?>><?php echo $role['name']; ?></option>
<?php
      }
?>
    </select>
    <a href="#" hx-on:click="addRole(event)">New role...</a>
    <fieldset id="permissions">
<?php
    $permissions = db_get_role_permissions($role_id);
    foreach ($permissions as $permission) {
      $checked = $permission['has_permission'] ? 'checked' : '';
?>
      <label>
        <input type="checkbox" name="permission_<?php echo $permission['permission_id']; ?>" <?php echo $checked; ?>>
        <?php echo $permission['name']; ?>
      </label>
<?php
    }
?>
    </fieldset>
    <input type="submit" hx-post="/admin/roles" hx-target="closest form" value="Save">
    <input type="button" hx-get="/admin/roles" hx-target="closest form" hx-include="#role_id" value="Reset">
    <input type="button" hx-on:click="deleteRole(event)" value="Delete">
<?php
}

if (array_key_exists('HTTP_HX_REQUEST', $_SERVER)) {
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (array_key_exists('action', $_POST) && $_POST['action'] === 'delete') {
      $role_id = $_POST['role_id'];
      db_delete_role($role_id);
    } else if (array_key_exists('name', $_POST)) {
      $name = $_POST['name'];
      $role_id = db_create_role($name);
    } else {
      $role_id = $_POST['role_id'];
      $permissions = db_get_role_permissions($role_id);
      foreach ($permissions as $permission) {
        $has_permission = array_key_exists('permission_' . $permission['permission_id'], $_POST);
        db_set_role_permission($role_id, $permission['permission_id'], $has_permission);
      }
    }
  } else {
    $role_id = $_GET['role_id'];
  }
  render_form_body($role_id);
  exit;
}

  render_header();
?>

<a href="/" class="back">&lt; Back to member portal</a>

<article>
  <h3>Manage roles</h3>  
  <form id="roles-form" autocomplete="off">
<?php
    render_form_body($_GET['role_id'] ?? 1);
?>
  </form>
  <script>
    function addRole(e) {
      e.preventDefault();

      var name = prompt('Enter the name of the new role');
      if (name) {
        htmx.ajax('POST', '/admin/roles', {
          target: '#roles-form',
          values: {name: name},
        });
      }
    }

    function deleteRole(e) {
      e.preventDefault();

      if (confirm('Are you sure you want to delete this role?')) {
        htmx.ajax('POST', '/admin/roles', {
          target: '#roles-form',
          values: {
            action: 'delete',
            role_id: document.getElementById('role_id').value,
          },
        });
      }
    }
  </script>
<?php
render_footer();
?>