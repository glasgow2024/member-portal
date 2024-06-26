<?php
$_REQUEST['allow_implicit_anonymous'] = true;
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/template.php');
require_once(getenv('CONFIG_LIB_DIR') . '/clyde_service.php');

function render_rce_wizard($title, $url) {
  if (array_key_exists('seen-rce-invite', $_COOKIE)) {
    header('Location: ' . $url);
    exit;
  }

  render_header(
    $title,
    'Link to an item in the Glasgow 2024 RingCentral Events event.',
    ['Home' => '/', 'Log in to RingCentral Events']
  );

  $clyde = new ClydeService();
  $_SESSION['oauth2redirect'] = $_SERVER['REQUEST_URI'];

  ?>
    <article>
      <h2>Log in to RingCentral Events</h2>
      <p>Before we can take you to the item in RingCentral Events, you'll need to first visit the landing page of RingCentral Events on this device.</p>
      <p>Once you have done this once on this device, you will be able to go directly to the item in the future.</p>

      <div class="wizard">
        <section id="step1">
          <header class="<?php echo is_logged_in() ? 'complete' : 'active' ?>"><h3>Step 1 - Log in to the member portal</h3></header>
          <?php
          if (!is_logged_in()) {
          ?>
            <div class="content">
              <a href="<?php echo $clyde->authorize_url(); ?>" class="button login">Log in with Glasgow Registration</a>
              <p><a href="/login-instructions" target="_blank">Log in instructions</a>
              <p>If you are having trouble logging in, please e-mail <a href="mailto:<?php echo SUPPORT_EMAIL; ?>?subject=Trouble logging in to member portal"><?php echo SUPPORT_EMAIL; ?></a>.</p>
          </div>
          <?php
          }
          ?>
        </section>
        <section id="step2">
          <header class="<?php echo is_logged_in() ? "active": "pending"; ?>"><h3>Step 2 - Visit the RingCentral Events landing page</h3></header>
          <div class="content">
            <p><a class="button <?php echo is_logged_in() ? "" : "disabled"; ?>" target="_blank" <?php echo is_logged_in() ? 'href="/stream?invite"' : ''; ?>>Go to RingCentral Events landing page</a></p>
          </div>
        </section>
        <section id="step3">
          <header class="pending"><h3>Step 3 - Go to the item</h3></header>
          <div class="content">
            <p><a id="deeplink-button" class="button disabled">Go to the item in RingCentral Events</a></p>
          </div>
        </section>
      </div>
    </article>
    <script>
      function checkSeenInvite() {
        if (document.cookie.indexOf('seen-rce-invite=') !== -1) {
          const $step2 = document.getElementById('step2');
          const $step2Content = $step2.getElementsByClassName('content')[0];
          const $step2Header = $step2.getElementsByTagName('header')[0];
          $step2.removeChild($step2Content);
          $step2Header.classList.remove('active');
          $step2Header.classList.add('complete');

          const $step3 = document.getElementById('step3');
          const $step3Header = $step3.getElementsByTagName('header')[0];
          $step3Header.classList.remove('pending');
          $step3Header.classList.add('active');

          const deepLinkButton = document.getElementById('deeplink-button');
          deepLinkButton.classList.remove('disabled');
          deepLinkButton.href = '<?php echo $url; ?>';
          deepLinkButton.title = '';
        } else {
          setTimeout(checkSeenInvite, 100);
        }
      }
      checkSeenInvite();
    </script>
  <?php
  render_footer();
}

?>