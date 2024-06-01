<?php
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/template.php');

if (!current_user_has_permission('manage-programme')) {
  header('Location: /');
  exit;
}
  
render_header();
?>
  
  <a href="/admin/programme/list" class="back">&lt; Back to Manage programme</a>
  
  <article>
    <h3>Manage RCE replays</h3>  
    <ul>
      <li><a href="/admin/programme/replay/edit">Add replay...</a></li>
<?php
        $replays = db_get_replays();
        foreach ($replays as $replay) {
?>
          <li><a href="/admin/programme/replay/edit?item_id=<?php echo $replay['item_id']; ?>"><?php echo $replay['item_id']; ?></a></li>
<?php
        }
?>
    </ul>
    <script>
      if (window.location.search.includes('error')) {
        var $toast = document.createElement('p');
        $toast.id = 'error-toast';
        $toast.textContent = 'Error: ' + new URLSearchParams(window.location.search).get('error');
        $toast.addEventListener('click', e => {
          e.target.remove();
        });
        document.body.appendChild($toast);
        window.history.replaceState({}, document.title, window.location.pathname);
      }
    </script>
  <?php
  render_footer();
  ?>