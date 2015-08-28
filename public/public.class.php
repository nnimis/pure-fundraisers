<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://purecharity.com
 * @since      1.0.0
 *
 * @package    Purecharity_Wp_Fundraisers
 * @subpackage Purecharity_Wp_Fundraisers/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the dashboard-specific stylesheet and JavaScript.
 *
 * @package    Purecharity_Wp_Fundraisers
 * @subpackage Purecharity_Wp_Fundraisers/public
 * @author     Pure Charity <dev@purecharity.com>
 */
class Purecharity_Wp_Fundraisers_Public {

  /**
   * The Fundraise.
   *
   * @since    1.0.0
   * @access   public
   * @var      string    $fundraiser    The Fundraiser.
   */
  public static $fundraiser;

  /**
   * The Fundraisers collection.
   *
   * @since    1.0.0
   * @access   public
   * @var      string    $fundraisers    The Fundraisers collection.
   */
  public static $fundraisers;

  /**
   * The options.
   *
   * @since    1.0.0
   * @access   public
   * @var      string    $options    The options.
   */
  public static $options;

  /**
   * The ID of this plugin.
   *
   * @since    1.0.0
   * @access   private
   * @var      string    $plugin_name    The ID of this plugin.
   */
  private $plugin_name;

  /**
   * The version of this plugin.
   *
   * @since    1.0.0
   * @access   private
   * @var      string    $version    The current version of this plugin.
   */
  private $version;

  /**
   * Initialize the class and set its properties.
   *
   * @since    1.0.0
   * @var      string    $plugin_name       The name of the plugin.
   * @var      string    $version    The version of this plugin.
   */
  public function __construct( $plugin_name, $version ) {

    $this->plugin_name = $plugin_name;
    $this->version = $version;

  }

  /**
   * Register the stylesheets for the public-facing side of the site.
   *
   * @since    1.0.0
   */
  public function enqueue_styles() {

    wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/public.css', array(), $this->version, 'all' );

  }

  /**
   * Register the stylesheets for the public-facing side of the site.
   *
   * @since    1.0.0
   */
  public function enqueue_scripts() {

    wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/public.js', array( 'jquery' ), $this->version, false );

  }

  /**
   * Not found layout for listing display.
   *
   * @since    1.0.0
   */
  public static function list_not_found($default = true){
    $html = '<p class="fr-not-found" style="'. ( $default ? '' : 'display:none' ) .'">No Fundraisers Found.</p>' . ($default ? Purecharity_Wp_Base_Public::powered_by() : '');
    return $html;
  }

  /**
   * Not found layout for single display.
   *
   * @since    1.0.0
   */
  public static function not_found(){
    return "<p>Fundraiser Not Found.</p>" . Purecharity_Wp_Base_Public::powered_by();;
  }

  /**
   * Live filter for table.
   *
   * @since    1.0.0
   */
  public static function live_search(){

    $options = get_option( 'purecharity_fundraisers_settings' );
    if(isset($options["live_filter"]) && (isset(self::$options['hide_search']) && self::$options['hide_search'] != 'true')){
      $html = '
        <div class="fr-filtering">
          <form method="get">
            <fieldset class="livefilter fr-livefilter">
              <legend>
                <label for="livefilter-input">
                  <strong>Search Fundraisers:</strong>
                </label>
              </legend>
              <input id="livefilter-input" class="fr-livefilter-input" value="'.@$_GET['query'].'" name="query" type="text">
              <button class="fr-filtering-button" type="submit">Filter</button>
              '. (@$_GET['query'] != '' ? '<a href="#" onclick="$(this).prev().prev().val(\'\'); $(this).parents(\'form\').submit(); return false;">Clear filter</a>' : '') .'
            </fieldset>
          </form>
        </div>
      ';
    }else{
      $html = '';
    }
    return $html;
  }

  /**
   * List of Fundraisers, grid option.
   *
   * @since    1.0.0
   */
  public static function listing(){

    $options = get_option( 'purecharity_fundraisers_settings' );

    $html = self::print_custom_styles() ;
    $html .= '
      <div class="fr-list-container">
        '.self::live_search().'
        <table class="fundraiser-table option1">
          <tr>
              <th colspan="2">Fundraiser Name</th>
            </tr>
    ';
    $i = 0;
    foreach(self::$fundraisers->external_fundraisers as $fundraiser){

      $title = $fundraiser->name;
      if(isset(self::$options['title']) && self::$options['title'] == 'owner_name'){
        $title = $fundraiser->owner->name;
      }
      if(isset(self::$options['title']) && self::$options['title'] == 'title_and_owner_name'){
        $title = $fundraiser->name.' by '.$fundraiser->owner->name;
      }

      $class = $i&1 ? 'odd' : 'even';
      $i += 1;
      $html .= '
        <tr class="row '.$class.'  fundraiser_'.$fundraiser->id.'">
            <td>'.$title.'</td>
            <td>
              <a class="fr-themed-link" href="?slug='.$fundraiser->slug.'">More Info</a>
              <a class="donate
              " href="'.Purecharity_Wp_Base_Public::pc_url().'/fundraisers/'.$fundraiser->id.'/fund">Donate Now</a>
          </td>
         </tr>
      ';
    }

      $html .= '
      </table>
        '.self::list_not_found(false).'
      </div>
    ';
    $html .= Purecharity_Wp_Base_Public::powered_by();

    return $html;
  }

  /**
   * List of Fundraisers.
   *
   * @since    1.0.0
   */
  public static function listing_grid(){

    $options = get_option( 'purecharity_fundraisers_settings' );

    $html = self::print_custom_styles() ;
    $html .= '<div class="fr-list-container pure_centered pure_row is-grid">'.self::live_search();

    foreach(self::$fundraisers->external_fundraisers as $fundraiser){

      $title = $fundraiser->name;
      if(isset(self::$options['title']) && self::$options['title'] == 'owner_name'){
        $title = $fundraiser->owner->name;
      }
      if(isset(self::$options['title']) && self::$options['title'] == 'title_and_owner_name'){
        $title = $fundraiser->name.'<br /> by '.$fundraiser->owner->name;
      }

      $funded = self::percent(($fundraiser->funding_goal-$fundraiser->funding_needed) ,$fundraiser->funding_goal);
      $html .= '
        <div class="fr-grid-list-item pure_span_6 pure_col fundraiser_'.$fundraiser->id.'">
          <div class="fr-grid-list-content">';
              if ($fundraiser->images->medium == NULL) {
                $html .= '
                    <div class="fr-listing-avatar-container pure_span24">
                      <div class="fr-listing-avatar" href="#" style="background-image: url('.$fundraiser->images->large.')"></div>
                    </div>
                  ';
                }else{
                  $html .= '
                    <div class="fr-listing-avatar-container pure_span24">
                      <div class="fr-listing-avatar" href="#" style="background-image: url('.$fundraiser->images->medium.')"></div>
                    </div>

                  ';
                }
            $html .='
            <div class="fr-grid-item-content pure_col pure_span_24">
              <div class="fr-grid-title-container">
                <p class="fr-grid-title">'.$title.'</p>
              </div>
              '.self::grid_funding_stats($fundraiser).'
          </div>
          <ul class="fr-list-actions pure_col pure_span_24">
            <li><a class="fr-themed-link" href="?slug='.$fundraiser->slug.'">More Info</a>
            <li><a class="fr-themed-link" target="_blank" href="'.Purecharity_Wp_Base_Public::pc_url().'/fundraisers/'.$fundraiser->id.'/fund">Donate Now</a>
          </ul>
        </div>
        </div>
      ';
    }

    $html .= self::list_not_found(false);
    $html .= '</div>';
    $html .= Purecharity_Wp_Fundraisers_Paginator::page_links(self::$fundraisers->meta);
    $html .= Purecharity_Wp_Base_Public::powered_by();

    return $html;
  }

  /**
   * List of Last Fundraisers.
   *
   * @since    1.0.1
   */
  public static function listing_last_grid(){

    $options = get_option( 'purecharity_fundraisers_settings' );

    $html = self::print_custom_styles() ;
    $html .= '<div class="fr-list-container is-grid">';

    foreach(self::$fundraisers->external_fundraisers as $fundraiser){

      $title = $fundraiser->name;
      if(isset(self::$options['title']) && self::$options['title'] == 'owner_name'){
        $title = $fundraiser->owner->name;
      }
      if(isset(self::$options['title']) && self::$options['title'] == 'title_and_owner_name'){
        $title = $fundraiser->name.'<br /> by '.$fundraiser->owner->name;
      }

      $html .= '
        <div class="fr-grid-list-item fundraiser_'.$fundraiser->id.'">
          <div class="fr-grid-list-content">
            <div class="fr-listing-avatar-container">
                <div class="fr-listing-avatar" href="#" style="background-image: url('.$fundraiser->images->large.')"></div>
              </div>
            <div class="fr-grid-item-content">
            <p class="fr-grid-title">'.$title.'</h4>
            '.selft::grid_funding_stats($fundraiser).'
          </div>
          <ul class="fr-list-actions">
            <li><a class="fr-themed-link" href="?slug='.$fundraiser->slug.'">More Info</a>
            <li><a class="fr-themed-link" target="_blank" href="'.Purecharity_Wp_Base_Public::pc_url().'/fundraisers/'.$fundraiser->id.'/fund">Donate Now</a>
          </ul>
        </div>
        </div>
      ';
    }

    $html .= self::list_not_found(false);
    $html .= '</div>';

    return $html;
  }

  /**
   * Single Fundraisers.
   *
   * @since    1.0.0
   */
  public static function show(){

    $title = self::$fundraiser->name;
    if(isset(self::$options['title']) && self::$options['title'] == 'owner_name'){
      $title = self::$fundraiser->owner->name;
    }
    if(isset(self::$options['title']) && self::$options['title'] == 'title_and_owner_name'){
      $title = self::$fundraiser->name.' by '.self::$fundraiser->owner->name;
    }

    $options = get_option( 'purecharity_fundraisers_settings' );

    $html = self::print_custom_styles() ;
    $html .= '
      <div class="pure_row">
        <div class="fr-top-row pure_col pure_span_24">
          <div class="fr-name pure_col pure_span_18">
            <h3>'.$title.'</h3>
          </div>
          <div class="fr-donate mobile-hidden fr-donate-top pure_col pure_span_6">
            <a class="fr-pure-button" href="'.Purecharity_Wp_Base_Public::pc_url().'/fundraisers/'.self::$fundraiser->id.'/fund">Donate</a>
          </div>
        </div>
        <div class="fr-container pure_col pure_span_24 fundraiser_'.self::$fundraiser->id.'">
          <div class="fr-header pure_col pure_span_24">
            <img src="'.self::$fundraiser->images->large.'">
          </div>
          <div class="fr-middle-row pure_col pure_span_24">
            <div class="fr-avatar-container pure_col pure_span_5">
              <div class="fr-avatar" href="#" style="background-image: url('.self::$fundraiser->images->small.')"></div>
            </div>
            <div class="fr-info pure_col pure_span_13">
              <p class="fr-location">'.self::$fundraiser->country.'</p>
                <p class="fr-organizer">
                  Organized by <a class="fr-themed-link" href="'.Purecharity_Wp_Base_Public::pc_url().'/'.self::$fundraiser->field_partner->slug.'">'.self::$fundraiser->field_partner->name.'</a>
                </p>
            </div>
            <div class="fr-donate pure_col pure_span_6">
              <a class="fr-pure-button" href="'.Purecharity_Wp_Base_Public::pc_url().'/fundraisers/'.self::$fundraiser->id.'/fund">Donate</a>
              '. (isset($options['updates_tab']) ?  '' : '<a class="fr-p2p" href="'.Purecharity_Wp_Base_Public::pc_url().'/'.self::$fundraiser->slug.'/copies/new">Start a Fundraiser for this Cause</a>') .'
            </div>
          </div>
          '. self::single_view_funding_bar() .'
          '.self::single_view_funding_div().'
          '.self::single_view_tabs().'
        </div>
      </div>
    ';
    $html .= Purecharity_Wp_Base_Public::powered_by();
    return $html;
  }

  /**
   * Funding stats for grid listing.
   *
   * @since    1.0.5
   */
  public static function grid_funding_stats($fundraiser){
    if($fundraiser->funding_goal != 'anonymous'){
      $funded = self::percent(($fundraiser->funding_goal-$fundraiser->funding_needed) ,$fundraiser->funding_goal);
      return '
        <div class="fr-grid-status pure_col pure_span_24" title="'.$funded.'">
          <div class="fr-grid-progress" style="width:'.$funded.'%"></div>
        </div>
        <div class="fr-grid-stats pure_col pure_span_24">
          <p>Goal: <strong>$'.number_format($fundraiser->funding_goal, 0, '.', ',').'</strong></p>
          <p>Raised: <strong>$'.number_format(($fundraiser->funding_goal-$fundraiser->funding_needed), 0, '.', ',').'</strong></p>
        </div>
      ';
    }else{
      return '';
    }

  }

  /**
   * Funding bar for single view.
   *
   * @since    1.0.5
   */
  public static function single_view_funding_bar(){
    $funded = self::percent((self::$fundraiser->funding_goal-self::$fundraiser->funding_needed) ,self::$fundraiser->funding_goal);
    if(self::$fundraiser->funding_goal != 'anonymous'){
      return '
        <div class="fr-intro pure_col pure_span_24">
          <div class="fr-single-status-section pure_col pure_span_24">
            <div class="fr-single-status pure_col pure_span_24">
              <div class="fr-single-progress" style="width:'.$funded.'%"></div>
              <div class="fr-raised pure_col pure_span_24">
                <span class="fr-raised-label">Amount Raised</span><span class="fr-raised-amount">$'.number_format((self::$fundraiser->funding_goal-self::$fundraiser->funding_needed), 0, '.', ',').'</span>
              </div>
            </div>
          </div>
        </div>
      ';
    }else{
      return '';
    }
  }

  /**
   * Funding stats for single view.
   *
   * @since    1.0.5
   */
  public static function single_view_funding_div(){
    $start_date = new DateTime(self::$fundraiser->start_date);
    $end_date = new DateTime(self::$fundraiser->end_date);
    $today = new DateTime;
    $date_diff = $today->diff($end_date)->days+1;
    $funded = self::percent((self::$fundraiser->funding_goal-self::$fundraiser->funding_needed) ,self::$fundraiser->funding_goal);
    if(self::$fundraiser->funding_goal != 'anonymous'){
      return '
        <div class="fr-single-info pure_col pure_span_24">
          <ul class="fr-single-stats pure_col pure_span_24">
            <li class="pure_col pure_span_6"><strong>$'.number_format(self::$fundraiser->funding_goal, 0, '.', ',').'</strong><br/> <span class="fr-stat-title">One-time Goal</span></li>
            <li class="pure_col pure_span_6"><strong>$'.number_format(self::$fundraiser->funding_needed, 0, '.', ',').'</strong><br/> <span class="fr-stat-title">Still Needed</span></li>
            <li class="pure_col pure_span_6"><strong>'.$date_diff.'</strong><br/> <span class="fr-stat-title">Days to Go</span></li>
            <li class="pure_col pure_span_6">
            '.Purecharity_Wp_Base_Public::sharing_links(array(), self::$fundraiser->name." Fundraisers").'
            <a target="_blank" href="'.Purecharity_Wp_Base_Public::pc_url().'/'.self::$fundraiser->slug.'"><img src="' . plugins_url( 'images/share-purecharity.png', __FILE__ ) . '" ></a>
            </li>
          </ul>
        </div>
      ';
    }else{
      return '';
    }
  }

  /**
   * Sharing links for single view.
   *
   * @since    1.0.5
   */
  public static function single_view_tabs(){
    $options = get_option( 'purecharity_fundraisers_settings' );
    $funded = self::percent((self::$fundraiser->funding_goal-self::$fundraiser->funding_needed) ,self::$fundraiser->funding_goal);
    if(self::$fundraiser->funding_goal != 'anonymous'){
      return '
        <div class="fr-body pure_span_24 pure_col">
          <div id="fr-tabs" class="pure_col pure_span_24">
             <ul class="fr-tabs-list pure_col pure_span_24">
               <li><a class="fr-themed-link" href="#tab-1">About</a></li>
               '. (isset($options['updates_tab']) ?  '' : '<li><a class="fr-themed-link" href="#tab-2">Updates</a></li>') .'
               '. (isset($options['backers_tab']) ?  '' : '<li><a class="fr-themed-link" href="#tab-3">Backers</a></li>') .'
             </ul>
             <div id="tab-1" class="tab-div pure_col pure_span_24">'.self::$fundraiser->about.'</div>
             <div id="tab-2" class="tab-div pure_col pure_span_24">
                '.self::print_updates().'
             </div>
             <div id="tab-3" class="tab-div pure_col pure_span_24"><!-- we will need to be able check a box to hide this tab / info in the admin of the plugin -->

                '.self::print_backers().'

             </div>
           </div>
        </div>
      ';
    }else{

      $title = self::$fundraiser->name;
      if(isset($options['title']) && $options['title'] == 'owner_name'){
        $title = self::$fundraiser->owner->name;
      }
      if(isset($options['title']) && $options['title'] == 'title_and_owner_name'){
        $title = self::$fundraiser->name.' by '.self::$fundraiser->owner->name;
      }
      return '
        <div class="fr-body pure_span_20 pure_col">
          <div id="fr-tabs" class="pure_col pure_span_24">
             <ul class="fr-tabs-list pure_col pure_span_24">
               <li><a class="fr-themed-link" href="#tab-1">About</a></li>
               '. (isset($options['updates_tab']) ?  '' : '<li><a class="fr-themed-link" href="#tab-2">Updates</a></li>') .'
               '. (isset($options['backers_tab']) ?  '' : '<li><a class="fr-themed-link" href="#tab-3">Backers</a></li>') .'
             </ul>
             <div id="tab-1" class="tab-div pure_col pure_span_24">'.self::$fundraiser->about.'</div>
             <div id="tab-2" class="tab-div pure_col pure_span_24">
                '.self::print_updates().'
             </div>
             <div id="tab-3" class="tab-div pure_col pure_span_24"><!-- we will need to be able check a box to hide this tab / info in the admin of the plugin -->

                '.self::print_backers().'

             </div>
           </div>
        </div>
        <div class="fr-body pure_span_4 pure_col text-centered">
          '.Purecharity_Wp_Base_Public::sharing_links(array(), self::$fundraiser->about, $title, self::$fundraiser->images->large).'
          <a target="_blank" href="'.Purecharity_Wp_Base_Public::pc_url().'/'.self::$fundraiser->slug.'"><img src="' . plugins_url( 'images/share-purecharity.png', __FILE__ ) . '" ></a>
        </div>
      ';
    }
  }



  /**
   * Backers list.
   *
   * @since    1.0.0
   */
  public static function print_backers(){
    if(sizeof(self::$fundraiser->backers) == 0){
      $html = '<p>There are no backers at this time.</p>';
    }else{
      $html = '<ul class="fr-backers pure_col pure_span_24">';
      foreach(self::$fundraiser->backers as $backer){
        $html .= '
          <li class="pure_col pure_span_6">
            <span class="fr-avatar fr-backer-avatar" href="#" style="background-image: url('.$backer->avatar.')"></span>
            <span class="fr-backer-name"><a class="fr-themed-link" href="'.Purecharity_Wp_Base_Public::pc_url().'/'.$backer->slug.'">'.$backer->name.'</a></span>
          </li>
        ';
      }
      $html .= '</ul>';
    }
    return $html;
  }

  /**
   * Updates list.
   *
   * @since    1.0.0
   */
  public static function print_updates(){
    if(sizeof(self::$fundraiser->updates) == 0){
      $html = '<p>There are no updates at this time.</p>';
    }else{
      $html = '<ul class="fr-updates">';
      foreach(self::$fundraiser->updates as $update){
        $html .= '
          <li>
              <h4><a class="fr-themed-link" href="'.$update->url.'">'.$update->title.'</a></h4>
              <p class="date">Posted a week ago</p>
              <p>'.$update->body.'</p>
              <span class="fr-author">
                <p>Posted by:<br/><a class="fr-themed-link" href="'.Purecharity_Wp_Base_Public::pc_url().'/'.$update->author->slug.'">'.$update->author->name.'</a></p>
              </span>
              <span class="fr-read-more">
                <a class="fr-read-more" href="'.$update->url.'">Read More</a><!-- links to update on pure charity -->
              </span>
            </li>
        ';
      }
      $html .= '</ul>';
    }
    return $html;
  }


  public static function print_custom_styles(){
    $base_settings = get_option( 'pure_base_settings' );
    $pf_settings = get_option( 'purecharity_fundraisers_settings' );

    // Default theme color
    if($pf_settings['plugin_color'] == NULL || $pf_settings['plugin_color'] == ''){
      if($base_settings['main_color'] == NULL || $base_settings['main_color'] == ''){
        $color = '#CA663A';
      }else{
        $color = $base_settings['main_color'];
      }
    }else{
      $color = $pf_settings['plugin_color'];
    }

    $html = '<style>';
    $html .= '
      .fundraiser-table a.donate { background: '.$color.' !important; }
      .fr-grid-progress { background: '.$color.' !important; }
      .fr-grid-list-item ul.fr-list-actions li a:hover { background: '.$color.' !important; }
      a.fr-pure-button { background: '.$color.' !important; }
      .fr-single-progress { background: '.$color.' !important; }
      #fr-tabs ul.fr-tabs-list li.active a, #fr-tabs ul.fr-tabs-list li a:hover {border-color: '.$color.' !important;}
      .fr-themed-link { color: '.$color.' !important; }
      .fr-filtering button { background: '.$color.' }
    ';
    $html .= '</style>';

    return $html;
  }


  /**
   * Updates list.
   *
   * @since    1.0.0
   */


  /**
   * Percentage calculator.
   *
   * @since    1.0.0
   */
  public static function percent($num_amount, $num_total) {
    if($num_total == 0){ return 100; }
    return number_format((($num_amount / $num_total) * 100), 0);
  }
}
