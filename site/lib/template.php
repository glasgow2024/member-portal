<?php
  require_once('config.php');
  require_once('auth_functions.php');

  function get_internal_url($script) {
    return $script . '?v=' . hash_file('md5', getenv('CONFIG_WEB_DIR') . $script);
  }

  function render_header() {
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo CON_NAME; ?> Member Portal</title>
    <script src="https://unpkg.com/htmx.org@1.9.10" integrity="sha384-D1Kt99CQMDuVetoL1lrYwg5t+9QdHe7NLX/SoJYkXDFfX37iInKRy5xLSi8nO7UC" crossorigin="anonymous"></script>
    <script src="https://unpkg.com/htmx.org/dist/ext/loading-states.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Gruppo&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">
    <link rel="shortcut icon" href="/resources/favicon.png">
    <link rel="stylesheet" href="<?php echo get_internal_url("/resources/reset.css"); ?>">
    <link rel="stylesheet" href="<?php echo get_internal_url("/resources/style.css"); ?>">
  </head>
  <body>
    <header>
      <div class="container">
        <div class="site-branding-container">
          <div class="site-logo">
					  <a href="/" rel="home"><img width="36" height="36" src="resources/logo.png"></a>
				  </div>
          <div class="site-branding">
						<h1><a href="/" rel="home"><?php echo CON_SHORT_NAME; ?></a></h1>
            <h2><a href="/" rel="home">Member portal</a></h2>
				  </div>
        </div>
        <?php
        if (isset($_SESSION['username'])) {
        ?>
          <p class="username">
            <?php echo $_SESSION['username']; ?>
            |
            <?php
            if (is_anonymous()) {
            ?>
              <a href="<?php echo make_login_link(); ?>">Log in</a>
            <?php
            } else {
            ?>
              <a href="/logout">Log out</a>
            <?php
            }
            ?>
          </p>
        <?php
        }
        ?>
      </div>
    </header>
    <main>
<?php
  }

  function render_footer() {
?>
    </main>
    <footer>
      <div class="container">
        <p>&copy; <?php echo date('Y'); ?> <?php echo CON_SHORT_NAME; ?></p>
        <ul>
          <li><a href="https://glasgow2024.org/cookie-policy-eu/">Cookie Policy (EU)</a></li>
          <li><a href="https://glasgow2024.org/about/policies/code-of-conduct/">Code of Conduct</a></li>
          <li><a href="https://glasgow2024.org/about/policies/">All Policies</a></li>
        </ul>
      </div>
    </footer>
  </body>
</html>
<?php
  }

  function render_404() {
    http_response_code(404);
    render_header();
?>
    <a href="/" class="back">&lt; Back to member portal</a>
    <article>
      <h3>Page not found</h3>
      <p>The page you are looking for does not exist.</p>
    </article>
<?php
    render_footer();
    exit;
  }
?>