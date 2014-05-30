<?php
/*
Template Name: Bioflyer Directory
*/
?>
<?php get_header(); ?>

<section role="main">
   <!--BEGIN MAIN CONTENT-->
   <div class="row">
    <!--TWELVE COLUMNS CENTERED-->
    <div class="twelve columns centered">
      <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
        <h3><?php the_title(); ?></h3>
        <?php the_content(); ?>
        <?php
          // open connection with database
          include($_SERVER['DOCUMENT_ROOT'].'/wp-content/themes/common_3/db_connect.php');
          $con = new mysqli($db, $username, $password, 'sandboxuif');
          // error out if connection cannot be established
          if ($con->connect_errno) {
              printf("Connect failed: %s\n", $con->connect_error);
              exit;
          } else {
        ?>
              <ul class="pagination" data-magellan-expedition="fixed">
                <li data-magellan-arrival="A"><a href="#A">A</a></li>
                <li data-magellan-arrival="B"><a href="#B">B</a></li>
                <li data-magellan-arrival="C"><a href="#C">C</a></li>
                <li data-magellan-arrival="D"><a href="#D">D</a></li>
                <li data-magellan-arrival="E"><a href="#E">E</a></li>
                <li data-magellan-arrival="F"><a href="#F">F</a></li>
                <li data-magellan-arrival="G"><a href="#G">G</a></li>
                <li data-magellan-arrival="H"><a href="#H">H</a></li>
                <li data-magellan-arrival="I"><a href="#I">I</a></li>
                <li data-magellan-arrival="J"><a href="#J">J</a></li>
                <li data-magellan-arrival="K"><a href="#K">K</a></li>
                <li data-magellan-arrival="L"><a href="#L">L</a></li>
                <li data-magellan-arrival="M"><a href="#M">M</a></li>
                <li data-magellan-arrival="N"><a href="#N">N</a></li>
                <li data-magellan-arrival="O"><a href="#O">O</a></li>
                <li data-magellan-arrival="P"><a href="#P">P</a></li>
                <li data-magellan-arrival="Q"><a href="#Q">Q</a></li>
                <li data-magellan-arrival="R"><a href="#R">R</a></li>
                <li data-magellan-arrival="S"><a href="#S">S</a></li>
                <li data-magellan-arrival="T"><a href="#T">T</a></li>
                <li data-magellan-arrival="U"><a href="#U">U</a></li>
                <li data-magellan-arrival="V"><a href="#V">V</a></li>
                <li data-magellan-arrival="W"><a href="#W">W</a></li>
                <li data-magellan-arrival="Y"><a href="#X">X</a></li>
                <li data-magellan-arrival="X"><a href="#Y">Y</a></li>
                <li data-magellan-arrival="Z"><a href="#Z">Z</a></li>
              </ul>
        <?php
              // initialize alphabetical associative array
              $alph_arr = array();
              foreach (range('A', 'Z') as $l) {
                $alph_arr[$l] = array();
              }

              $meta = get_post_meta(get_the_ID());

              // make sure a directory was provided in a custom field, otherwise error out gracefully
              if (empty($meta['directory'])) {
                echo "<em>No bioflyer directory name has been provided. Please contact an administrator.</em>";
              } else {

                if ($result = $con->query("SELECT title, file_under FROM bioflyers ORDER BY file_under ASC")) {
                  while ($row = $result->fetch_assoc()) {
                    $letter = strtoupper($row['file_under']);
                    array_push($alph_arr[$letter], $row['title']);
                  }

                  // free result set
                  $result->close();
                }

                // output all our sections in alphabetical order
                foreach ($alph_arr as $letter => $arr) {
                  echo '<div data-magellan-destination="'.$letter.'">
                        <a name="'.$letter.'"></a>
                        <h5>'.$letter.'</h5>
                        <ul class="block-grid two-up mobile-one-up">';
                  if (empty($arr)) {
                    echo '<li><em>No listings available</em></li>';
                  } else {
                    foreach ($arr as $title) {
                      echo '<li><strong>'.$title.'</strong><br />
                            <a href="/scholarships/medicine/bf?title='.$title.'">Learn more</a></li>';
                    }
                  }
                  echo '</div>';
                }
              }
          }
        ?>
      <?php endwhile; endif; ?>
    <!--END TWELVE COLUMNS-->
    </div>
  </div>
</section>

<?php get_sidebar(); ?>
<!--END MAIN WRAP-->
    </div>
<!--END OUTSIDE WRAP-->
</div>
<!--END CONTAINER-->
</div>
<?php get_footer(); ?>