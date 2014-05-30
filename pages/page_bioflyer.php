<?php
/*
Template Name: Bioflyer Template
*/
?>
<?php
    /* Redirect if someone tries to come to page with no query string */
    if (!isset($_GET['title'])) { header( 'Location: http://www.uifoundation.org/scholarships/medicine/' ) ; die(); }

     /* Add stylesheet to the page */
    add_action( 'wp_enqueue_scripts', 'safely_add_stylesheet' );
    function safely_add_stylesheet() {
        wp_enqueue_style( 'print-style', "http://www.uifoundation.org/scholarships/wp-content/themes/common_3/stylesheets/bf-print-styles.css" );
    }
?>
<?php get_header(); ?>

<section role="main">
   <!--BEGIN MAIN CONTENT-->
   <div class="row">
    <!--TEN COLUMNS CENTERED-->
    <div class="ten columns centered" id="print_wrapper">
      <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
        <?php
          // open connection with database
          include($_SERVER['DOCUMENT_ROOT'].'/wp-content/themes/common_3/db_connect.php');
          $con = new mysqli($db, $username, $password, 'sandboxuif');
          // error out if connection cannot be established
          if ($con->connect_errno) {
              printf("Connect failed: %s\n", $con->connect_error);
              exit;
          } else {
              $stmt = $con->prepare('SELECT title, body FROM bioflyers WHERE title=? LIMIT 1');
              $stmt->bind_param('s', $_GET['title']);
              $stmt->bind_result($title, $body);

              $stmt->execute();

              $stmt->fetch();
              if (!isset($title)) {
                print "<h4>Oops! Something went wrong.</h4><p>The Bioflyer you are searching for doesn't seem to exist. Please contact an administrator if you need help.";
              } else {
                printf("<div class='eight left bf-title'><h4>%s</h4></div>
                        <div class='four right bf-give' style='text-align:center'><a class='large button' href='http://www.givetoiowa.org/medicine' style='margin:15px; '>Give to Iowa</a></div>
                        %s
                        <p class='congrats-msg'>Congratulations on receiving the %s!</p>", $title, apply_filters('the_content', stripslashes($body)), $title);
              }

              $stmt->free_result();
              $stmt->close();
          }

        ?>
      <?php endwhile; endif; ?>
    <!--END TEN COLUMNS-->
    </div>
  </div>
  <div class="row">
    <div class="ten columns centered">
      <!--STORIES-->
      <?php
        $slug = 'medicine';
        // Load the XML source
        $xml = new DOMDocument;
        $xml->load('http://www.uifoundation.org/stories/feed/');
        $xsl = new DOMDocument;
        $xsl->load('wp-content/themes/common_3/student-TEST.xsl');

        // Configure the transformer
        $proc = new XSLTProcessor;
        $proc->importStyleSheet($xsl); // attach the xsl rules
        //Pass the blog slug to determine which leaders to post
        $xsl = $proc ->setParameter('', 'category', $slug);
        echo $proc->transformToXML($xml);

      ?>
    </div>
  </div>
</section>
<div class='print-footer-wrap'>
  <span class='print-footer'>The University of Iowa Foundation • P.O. Box 4550 <br> Iowa City, Iowa 52244-4550 • (319) 335-3305</span>
</div>

<?php get_sidebar(); ?>
<!--END MAIN WRAP-->
    </div>
<!--END OUTSIDE WRAP-->
</div>
<!--END CONTAINER-->
</div>
<script type="text/javascript">
  // adjust footer position based on content height
  var footer_adjustment, hide_footer;
  var print_content_height = $('#print_wrapper').height();
  console.log(print_content_height);
  if(print_content_height > 540){
    hide_footer = true;
  } else if (print_content_height < 500){
    footer_adjustment= '10px';
    hide_footer = false;
  } else {
    footer_adjustment= Math.floor(print_content_height/-7.4) + 'px';
    hide_footer = false;
  }
  if(hide_footer){
    $('.print-footer-wrap').css('display', 'none'); 
    $('.wp-caption-text').css('margin-top', '-20px');   
  } else {
    $('.print-footer-wrap').css('bottom', footer_adjustment);    
  }
  // only show 
</script>
<?php get_footer(); ?>