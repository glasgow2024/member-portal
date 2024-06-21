<?php
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/template.php');

if (!current_user_has_permission('manage-programme')) {
  header('Location: /');
  exit;
}
  
render_header("Manage RCE stages", "Manage RCE stages.");
?>
  
  <a href="/admin/programme/list" class="back">&lt; Back to Manage programme</a>
  
  <article>
    <h2>Manage <abbr title="RingCentral Events">RCE</abbr> stages</h2>
    <script>
      if (window.location.search.includes('error')) {
        var $toast = document.createElement('p');
        $toast.classList = ['error'];
        $toast.textContent = new URLSearchParams(window.location.search).get('error');
      
        var $img = document.createElement('img');
        $img.src = '/resources/error.svg';
        $img.alt = 'error';
        $img.ariaLabel = 'error';
        $toast.prepend($img);

        document.write($toast.outerHTML);
        window.history.replaceState({}, document.title, window.location.pathname);
      }
    </script>
    <ul>
      <li><a href="/admin/programme/stages/edit">Add stage...</a></li>
<?php
        $stages = db_get_stages();
        foreach ($stages as $stage) {
?>
          <li><a aria-label="Edit stage <?php echo $stage['room_id']; ?>" href="/admin/programme/stages/edit?room_id=<?php echo $stage['room_id']; ?>"><?php echo $stage['room_id']; ?></a></li>
<?php
        }
?>
    </ul>
  <?php
  render_footer();
  ?>