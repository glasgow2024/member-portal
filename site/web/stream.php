<?php
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/template.php');

if (array_key_exists('invite', $_GET)) {
  if (!current_user_has_permission('see-rce')) {
    header('Location: /');
    exit;
  }
  $magic_link = db_get_magic_link($_COOKIE['session']);
  header('Location: ' . $magic_link);
  setcookie('seen-rce-invite', '1', time() + 60*60*24*365, '/');
  exit;
}

render_header();
?>

<a href="/" class="back">&lt; Back to member portal</a>

<article>
  <h3>Stream and catch-up</h3>
  <p>We are using a platform called RingCentral Events to stream our programme items. You can watch the live streams of programme items, or watch the recorded videos afterwards. It works on both desktop and mobile.</p>
  <p>Items will be available for catch-up until 1<sup>st</sup> May 2024.</p>
<?php
  if (!current_user_has_permission('see-rce')) {
?>
  <p>Our RingCentral Event will open Friday morning. Please check back then.</p>
<?php
  } else {
?>
  <p><a class="button" target="_blank" href="?invite">Go to Ring Central Events</a></p>
<?php
  }
?>
  <p>The use of RingCentral Events is thanks to the generosity of <a href="https://glasgow2024.org/">Glasgow 2024</a>. This is the same platform as will be used at this year's Worldcon, Glasgow 2024.</p>
  <h4>How to use RingCentral Events</h4>
  <p>In the <a href="https://eastercon2024.co.uk/guide-to-ringcentral-events/">our guide</a> you will find instructions on how to:</p>
  <ul>
    <li>Watch a live stream</li>
    <li>Watch catch up items</li>
    <li>Turn on captions</li>
    <li>Use Picture in Picture</li>
    <li>AirPlay and Cast</li>
  </ul>
  <p><a class="button" href="https://eastercon2024.co.uk/guide-to-ringcentral-events/">Guide to RingCentral Events</a></p>
</article>

<?php
render_footer();
?>