<?php
require_once('../../includes/config.php');
require_once('../../includes/session_auth.php');
require_once('../../includes/template.php');

if (!current_user_has_permission('see-vote')) {
  header('Location: /');
  exit;
}

render_header();
?>

<a href="/" class="back">&lt; Back to member portal</a>

<article>
  <h3>Doc Weir</h3>
  <p>The Doc Weir Award celebrates the unsung heroes in fandom, those who do the work behind the scenes every year. You can vote by clicking the button below, or find Claire Brialey at the fan funds table in the Dealers room for a paper ballot.</p>
  <p><a target="_blank" class="button" href="https://forms.gle/7xDLsFmHQJpve7V16">Vote for the Doc Weir Award</a></p>

  <h3>BSFA Awards</h3>
  <p>The BSFA Awards are voted on by members of the British Science Fiction Association and the Eastercon. Vote by noon on Saturday online (there are no paper ballots). The winners will be announced on Saturday at the BSFA Awards Ceremony.</p>
  <p><a target="_blank" class="button" href="javascript:alert('todo')">Vote in the BSFA Awards</a></p>
</article>

<?php
render_footer();
?>