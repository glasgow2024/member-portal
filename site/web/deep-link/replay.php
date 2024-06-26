<?php
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/db.php');
require_once(getenv('CONFIG_LIB_DIR') . '/template.php');
require_once(getenv('CONFIG_LIB_DIR') . '/deep_link.php');

if (!array_key_exists('item_id', $_GET)) {
  die('Missing required query parameter "item_id"');
}

$item_id = $_GET['item_id'];
$replay = db_get_replay($item_id);

if (!$replay['replay_url']) {
  render_header(
    'Go to an item replay',
    'Replay not available.',
    ['Home' => '/', 'Got to an item replay']
  );
  ?>
  <article>
    <h2>No replay available yet</h2>
    <p>Replay isn&apos;t available for this item yet. We&apos;re working to get replays up as soon as we can, so check back soon.</p>
  </article>
  <?php
  render_footer();
  exit;
}

render_rce_wizard("Go to an item replay", $replay['replay_url']);
?>