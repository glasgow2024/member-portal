<?php
$_REQUEST['allow_implicit_anonymous'] = true;
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/template.php');

render_header(
  'Under constructions',
  'Under construction.',
  ['Home' => '/', $_GET['page']]
);
?>
<article>
  <h2><?php echo $_GET['page']; ?></h2>
  <p>We&apos;re still working on this page.</p>
  <p>If you are involved in testing the site, this is probably fine. If you are not, please let us know.</p>
</article>

<?php
render_footer();
?>