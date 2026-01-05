<?php
/**
 * Settings page class
 *
 * @package PeerTube_Video_Manager
 * @since 1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * PT_Settings class for plugin settings page
 *
 * @package PeerTube_Video_Manager
 * @since 1.0.0
 */
class PT_Settings {

	/**
	 * Instance of this class
	 *
	 * @var PT_Settings
	 */
	private static $instance = null;

	/**
	 * Get instance
	 *
	 * @return PT_Settings
	 * @since 1.0.0
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 */
	private function __construct() {
		add_action( 'admin_menu', array( $this, 'add_settings_page' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'admin_init', array( $this, 'save_checkbox_settings' ) );
		add_action( 'admin_init', array( $this, 'handle_settings_save_redirect' ) );
		add_action( 'admin_post_pt_vm_clear_cache', array( $this, 'handle_clear_cache' ) );
		add_action( 'admin_post_pt_vm_create_search_page', array( $this, 'handle_create_search_page' ) );
		add_action( 'admin_post_pt_vm_create_video_page', array( $this, 'handle_create_video_page' ) );
		add_action( 'wp_ajax_pt_vm_test_connection', array( $this, 'ajax_test_connection' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );
	}

	/**
	 * Add settings page to admin menu
	 *
	 * @since 1.0.0
	 */
	public function add_settings_page() {
		add_options_page(
			__( 'PeerTube Video Manager', 'peertube-video-manager' ),
			__( 'PeerTube Videos', 'peertube-video-manager' ),
			'manage_options',
			'pt-video-manager',
			array( $this, 'render_settings_page' )
		);
	}

	/**
	 * Register plugin settings
	 *
	 * @since 1.0.0
	 */
	public function register_settings() {
		register_setting( 'pt_vm_settings', 'pt_vm_base_url', array(
			'type'              => 'string',
			'sanitize_callback' => array( $this, 'sanitize_url' ),
			'default'           => 'https://lokalmedial.de',
		) );

		register_setting( 'pt_vm_settings', 'pt_vm_default_channels', array(
			'type'              => 'string',
			'sanitize_callback' => 'sanitize_textarea_field',
			'default'           => '',
		) );

		register_setting( 'pt_vm_settings', 'pt_vm_cache_time_videos', array(
			'type'              => 'integer',
			'sanitize_callback' => 'absint',
			'default'           => 5,
		) );

		register_setting( 'pt_vm_settings', 'pt_vm_cache_time_config', array(
			'type'              => 'integer',
			'sanitize_callback' => 'absint',
			'default'           => 24,
		) );

		register_setting( 'pt_vm_settings', 'pt_vm_videos_per_page', array(
			'type'              => 'integer',
			'sanitize_callback' => 'absint',
			'default'           => 8,
		) );

		register_setting( 'pt_vm_settings', 'pt_vm_show_views', array(
			'type'              => 'boolean',
			'sanitize_callback' => array( $this, 'sanitize_checkbox' ),
			'default'           => false,
		) );

		register_setting( 'pt_vm_settings', 'pt_vm_search_page_id', array(
			'type'              => 'integer',
			'sanitize_callback' => 'absint',
			'default'           => 0,
		) );

		register_setting( 'pt_vm_settings', 'pt_vm_video_page_id', array(
			'type'              => 'integer',
			'sanitize_callback' => 'absint',
			'default'           => 0,
		) );

		register_setting( 'pt_vm_settings', 'pt_vm_peertube_button_text', array(
			'type'              => 'string',
			'sanitize_callback' => 'sanitize_text_field',
			'default'           => __( 'Auf PeerTube ansehen', 'peertube-video-manager' ),
		) );

		register_setting( 'pt_vm_settings', 'pt_vm_button_color', array(
			'type'              => 'string',
			'sanitize_callback' => 'sanitize_hex_color',
			'default'           => '#1e40af',
		) );

		register_setting( 'pt_vm_settings', 'pt_vm_button_hover_color', array(
			'type'              => 'string',
			'sanitize_callback' => 'sanitize_hex_color',
			'default'           => '#f59e0b',
		) );

		register_setting( 'pt_vm_settings', 'pt_vm_button_text_color', array(
			'type'              => 'string',
			'sanitize_callback' => 'sanitize_hex_color',
			'default'           => '#ffffff',
		) );

		register_setting( 'pt_vm_settings', 'pt_vm_redirect_wp_search', array(
			'type'              => 'boolean',
			'sanitize_callback' => array( $this, 'sanitize_checkbox' ),
			'default'           => false,
		) );

		register_setting( 'pt_vm_settings', 'pt_vm_wp_search_section_title', array(
			'type'              => 'string',
			'sanitize_callback' => 'sanitize_text_field',
			'default'           => __( 'PeerTube Videos', 'peertube-video-manager' ),
		) );

		register_setting( 'pt_vm_settings', 'pt_vm_wp_search_wp_option_text', array(
			'type'              => 'string',
			'sanitize_callback' => 'sanitize_text_field',
			'default'           => __( 'Auf der Webseite suchen', 'peertube-video-manager' ),
		) );

		register_setting( 'pt_vm_settings', 'pt_vm_wp_search_peertube_option_text', array(
			'type'              => 'string',
			'sanitize_callback' => 'sanitize_text_field',
			'default'           => __( 'In Mediathek LokalMedial.de suchen', 'peertube-video-manager' ),
		) );
	}

	/**
	 * Save checkbox settings manually
	 *
	 * @since 1.0.6
	 */
	public function save_checkbox_settings() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		if ( isset( $_POST['option_page'] ) && 'pt_vm_settings' === $_POST['option_page'] ) {
			// Handle checkboxes
			if ( isset( $_POST['pt_vm_show_views'] ) && '1' === $_POST['pt_vm_show_views'] ) {
				update_option( 'pt_vm_show_views', true );
			} else {
				update_option( 'pt_vm_show_views', false );
			}

			if ( isset( $_POST['pt_vm_redirect_wp_search'] ) && '1' === $_POST['pt_vm_redirect_wp_search'] ) {
				update_option( 'pt_vm_redirect_wp_search', true );
			} else {
				update_option( 'pt_vm_redirect_wp_search', false );
			}
		}
	}

	/**
	 * Handle redirect after settings save
	 *
	 * @since 1.1.3
	 */
	public function handle_settings_save_redirect() {
		// Only process if our settings group is being saved
		if ( ! isset( $_POST['option_page'] ) || 'pt_vm_settings' !== $_POST['option_page'] ) {
			return;
		}

		// Check if this is a settings update request
		if ( ! isset( $_POST['submit'] ) || ! current_user_can( 'manage_options' ) ) {
			return;
		}

		// Use filter to modify redirect location
		add_filter( 'wp_redirect', array( $this, 'modify_settings_redirect' ), 10, 2 );
	}

	/**
	 * Modify redirect URL after settings save
	 *
	 * @param string $location Redirect location.
	 * @param int    $status HTTP status code.
	 * @return string Modified redirect location.
	 * @since 1.1.3
	 */
	public function modify_settings_redirect( $location, $status ) {
		// Check if this is a redirect from options.php for our settings
		if ( isset( $_POST['option_page'] ) && 'pt_vm_settings' === $_POST['option_page'] ) {
			// Remove filter to avoid infinite loop
			remove_filter( 'wp_redirect', array( $this, 'modify_settings_redirect' ), 10 );
			
			// Redirect to our settings page instead
			$location = add_query_arg(
				array(
					'page'             => 'pt-video-manager',
					'settings-updated' => 'true',
				),
				admin_url( 'options-general.php' )
			);
		}

		return $location;
	}

	/**
	 * Sanitize checkbox
	 *
	 * @param mixed $value Checkbox value.
	 * @return bool Sanitized boolean value.
	 * @since 1.0.5
	 */
	public function sanitize_checkbox( $value ) {
		return (bool) $value;
	}

	/**
	 * Sanitize URL
	 *
	 * @param string $url URL to sanitize.
	 * @return string Sanitized URL.
	 * @since 1.0.0
	 */
	public function sanitize_url( $url ) {
		$url = esc_url_raw( $url );
		$url = rtrim( $url, '/' );
		
		if ( ! preg_match( '/^https?:\/\/.+/', $url ) ) {
			add_settings_error(
				'pt_vm_base_url',
				'invalid_url',
				__( 'Please enter a valid URL (http:// or https://).', 'peertube-video-manager' ),
				'error'
			);
			return get_option( 'pt_vm_base_url', 'https://video3.cappital.co' );
		}
		
		return $url;
	}

	/**
	 * Render settings page
	 *
	 * @since 1.0.0
	 */
	public function render_settings_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$base_url          = get_option( 'pt_vm_base_url', 'https://video3.cappital.co' );
		$default_channels  = get_option( 'pt_vm_default_channels', '' );
		$cache_time_videos = get_option( 'pt_vm_cache_time_videos', 5 );
		$cache_time_config = get_option( 'pt_vm_cache_time_config', 24 );
		$videos_per_page   = get_option( 'pt_vm_videos_per_page', 8 );
		$show_views        = get_option( 'pt_vm_show_views', false );
		$redirect_wp_search = get_option( 'pt_vm_redirect_wp_search', false );
		$search_page_id    = get_option( 'pt_vm_search_page_id', 0 );
		$video_page_id     = get_option( 'pt_vm_video_page_id', 0 );
		$peertube_button_text = get_option( 'pt_vm_peertube_button_text', __( 'Watch on Media Platform', 'peertube-video-manager' ) );
		$button_color      = get_option( 'pt_vm_button_color', '#1e40af' );
		$button_hover_color = get_option( 'pt_vm_button_hover_color', '#f59e0b' );
		$button_text_color = get_option( 'pt_vm_button_text_color', '#ffffff' );
		$wp_search_section_title = get_option( 'pt_vm_wp_search_section_title', __( 'Media Platform', 'peertube-video-manager' ) );
		$wp_search_wp_option_text = get_option( 'pt_vm_wp_search_wp_option_text', __( 'Search on the website', 'peertube-video-manager' ) );
		$wp_search_peertube_option_text = get_option( 'pt_vm_wp_search_peertube_option_text', __( 'Search in the media library', 'peertube-video-manager' ) );
		?>
		<div class="wrap">
			<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
			
			<?php settings_errors( 'pt_vm_messages' ); ?>
			
			<?php if ( isset( $_GET['settings-updated'] ) ) : ?>
				<div class="notice notice-success is-dismissible">
					<p><?php esc_html_e( 'Settings saved.', 'peertube-video-manager' ); ?></p>
				</div>
			<?php endif; ?>
			
			<form method="post" action="options.php">
				<?php 
				settings_fields( 'pt_vm_settings' );
				// Add hidden field to preserve referer for redirect
				?>
				<input type="hidden" name="_wp_http_referer" value="<?php echo esc_attr( add_query_arg( 'page', 'pt-video-manager', admin_url( 'options-general.php' ) ) ); ?>">
				
				<table class="form-table">
					<tr>
						<th scope="row">
							<label for="pt_vm_base_url">
								<?php esc_html_e( 'Media Platform Instance URL', 'peertube-video-manager' ); ?>
							</label>
						</th>
						<td>
							<input type="url" 
								   id="pt_vm_base_url" 
								   name="pt_vm_base_url" 
								   value="<?php echo esc_attr( $base_url ); ?>" 
								   class="regular-text"
								   required>
							<p class="description">
								<?php esc_html_e( 'The full URL of your Media Platform instance (e.g., https://video3.cappital.co)', 'peertube-video-manager' ); ?>
							</p>
							<p>
								<button type="button" id="pt-test-connection" class="button">
									<?php esc_html_e( 'Test connection', 'peertube-video-manager' ); ?>
								</button>
								<span id="pt-connection-result"></span>
							</p>
						</td>
					</tr>
					
					<tr>
						<th scope="row">
							<label for="pt_vm_default_channels">
								<?php esc_html_e( 'Standard channels', 'peertube-video-manager' ); ?>
							</label>
						</th>
						<td>
							<textarea id="pt_vm_default_channels" 
									  name="pt_vm_default_channels" 
									  rows="5" 
									  class="large-text"><?php echo esc_textarea( $default_channels ); ?></textarea>
							<p class="description">
								<?php esc_html_e( 'List of channel handles, one per line (e.g., ok_dessau, ok_magdeburg). Used for [pt-latest-per-channel] when no channels attribute is specified.', 'peertube-video-manager' ); ?>
							</p>
						</td>
					</tr>
					
					<tr>
						<th scope="row">
							<label for="pt_vm_cache_time_videos">
								<?php esc_html_e( 'Cache time for videos (minutes)', 'peertube-video-manager' ); ?>
							</label>
						</th>
						<td>
							<input type="number" 
								   id="pt_vm_cache_time_videos" 
								   name="pt_vm_cache_time_videos" 
								   value="<?php echo esc_attr( $cache_time_videos ); ?>" 
								   min="1" 
								   max="1440" 
								   class="small-text">
							<p class="description">
								<?php esc_html_e( 'How long video lists are cached (default: 5 minutes)', 'peertube-video-manager' ); ?>
							</p>
						</td>
					</tr>
					
					<tr>
						<th scope="row">
							<label for="pt_vm_cache_time_config">
								<?php esc_html_e( 'Cache time for configuration (hours)', 'peertube-video-manager' ); ?>
							</label>
						</th>
						<td>
							<input type="number" 
								   id="pt_vm_cache_time_config" 
								   name="pt_vm_cache_time_config" 
								   value="<?php echo esc_attr( $cache_time_config ); ?>" 
								   min="1" 
								   max="168" 
								   class="small-text">
							<p class="description">
								<?php esc_html_e( 'How long categories and configurations are cached (default: 24 hours)', 'peertube-video-manager' ); ?>
							</p>
						</td>
					</tr>
					
					<tr>
						<th scope="row">
							<label for="pt_vm_videos_per_page">
								<?php esc_html_e( 'Videos per page', 'peertube-video-manager' ); ?>
							</label>
						</th>
						<td>
							<input type="number" 
								   id="pt_vm_videos_per_page" 
								   name="pt_vm_videos_per_page" 
								   value="<?php echo esc_attr( $videos_per_page ); ?>" 
								   min="1" 
								   max="100" 
								   class="small-text">
							<p class="description">
								<?php esc_html_e( 'Standard number of videos to be displayed (default: 8)', 'peertube-video-manager' ); ?>
							</p>
						</td>
					</tr>
					
					<tr>
						<th scope="row">
							<label for="pt_vm_show_views">
								<?php esc_html_e( 'Show views', 'peertube-video-manager' ); ?>
							</label>
						</th>
						<td>
							<label>
								<input type="checkbox" 
									   id="pt_vm_show_views" 
									   name="pt_vm_show_views" 
									   value="1" 
									   <?php checked( $show_views, true ); ?>>
								<?php esc_html_e( 'Display the number of views in video cards and detail view.', 'peertube-video-manager' ); ?>
							</label>
							<p class="description">
								<?php esc_html_e( 'When enabled, the number of views will be displayed for each video.', 'peertube-video-manager' ); ?>
							</p>
						</td>
					</tr>
					
					<tr>
						<th scope="row">
							<label for="pt_vm_redirect_wp_search">
								<?php esc_html_e( 'Media Platform search in WordPress search form', 'peertube-video-manager' ); ?>
							</label>
						</th>
						<td>
							<label>
								<input type="checkbox" 
									   id="pt_vm_redirect_wp_search" 
									   name="pt_vm_redirect_wp_search" 
									   value="1" 
									   <?php checked( $redirect_wp_search, true ); ?>>
								<?php esc_html_e( 'Add Media Platform search to the standard WordPress search form.', 'peertube-video-manager' ); ?>
							</label>
							<p class="description">
								<?php esc_html_e( 'When enabled, a "Search in Media Platform videos" checkbox will be added to the standard WordPress search form. If checked, the search will be redirected to the Media Platform search page. If unchecked, the standard WordPress search will function as normal.', 'peertube-video-manager' ); ?>
							</p>
						</td>
					</tr>
					
					<tr>
						<th scope="row">
							<label for="pt_vm_wp_search_section_title">
								<?php esc_html_e( 'Title of the Media Platform section', 'peertube-video-manager' ); ?>
							</label>
						</th>
						<td>
							<input type="text" 
								   id="pt_vm_wp_search_section_title" 
								   name="pt_vm_wp_search_section_title" 
								   value="<?php echo esc_attr( $wp_search_section_title ); ?>" 
								   class="regular-text">
							<p class="description">
								<?php esc_html_e( 'Title of the section containing Media Platform videos on the WordPress search results page.', 'peertube-video-manager' ); ?>
							</p>
						</td>
					</tr>
					
					<tr>
						<th scope="row">
							<label for="pt_vm_wp_search_wp_option_text">
								<?php esc_html_e( 'Text for the "Search on the website" option', 'peertube-video-manager' ); ?>
							</label>
						</th>
						<td>
							<input type="text" 
								   id="pt_vm_wp_search_wp_option_text" 
								   name="pt_vm_wp_search_wp_option_text" 
								   value="<?php echo esc_attr( $wp_search_wp_option_text ); ?>" 
								   class="regular-text">
							<p class="description">
								<?php esc_html_e( 'Text for the first option in the search form dropdown menu.', 'peertube-video-manager' ); ?>
							</p>
						</td>
					</tr>
					
					<tr>
						<th scope="row">
							<label for="pt_vm_wp_search_peertube_option_text">
								<?php esc_html_e( 'Text for the option "Search in Media Platform videos"', 'peertube-video-manager' ); ?>
							</label>
						</th>
						<td>
							<input type="text" 
								   id="pt_vm_wp_search_peertube_option_text" 
								   name="pt_vm_wp_search_peertube_option_text" 
								   value="<?php echo esc_attr( $wp_search_peertube_option_text ); ?>" 
								   class="regular-text">
							<p class="description">
								<?php esc_html_e( 'Text for the second option in the search forms dropdown menu.', 'peertube-video-manager' ); ?>
							</p>
						</td>
					</tr>
					
					<tr>
						<th scope="row">
							<label for="pt_vm_search_page_id">
								<?php esc_html_e( 'Search page', 'peertube-video-manager' ); ?>
							</label>
						</th>
						<td>
							<?php
							wp_dropdown_pages( array(
								'name'             => 'pt_vm_search_page_id',
								'id'               => 'pt_vm_search_page_id',
								'selected'         => $search_page_id,
								'show_option_none' => __( '— Select a page —', 'peertube-video-manager' ),
								'option_none_value' => '0',
							) );
							?>
							<p class="description">
								<?php esc_html_e( 'Select the page where the search results should be displayed. This page should contain the shortcodes [pt-search] and [pt-search-results]. If no page is selected, the current page will be used.', 'peertube-video-manager' ); ?>
							</p>
							<?php if ( $search_page_id > 0 ) : ?>
								<?php
								$page = get_post( $search_page_id );
								if ( $page && 'publish' === $page->post_status ) :
								?>
									<p>
										<a href="<?php echo esc_url( get_edit_post_link( $search_page_id ) ); ?>" target="_blank">
											<?php esc_html_e( 'Edit page', 'peertube-video-manager' ); ?>
										</a> |
										<a href="<?php echo esc_url( get_permalink( $search_page_id ) ); ?>" target="_blank">
											<?php esc_html_e( 'View page', 'peertube-video-manager' ); ?>
										</a>
									</p>
								<?php else : ?>
									<p class="description" style="color: #d63638;">
										<?php esc_html_e( 'The selected page no longer exists or has not been published.', 'peertube-video-manager' ); ?>
									</p>
								<?php endif; ?>
							<?php else : ?>
								<p class="description">
									<strong><?php esc_html_e( 'A notice:', 'peertube-video-manager' ); ?></strong>
									<?php esc_html_e( 'Create a page with the shortcodes [pt-search placeholder="Search..."] and [pt-search-results per_page="12"] and select it here.', 'peertube-video-manager' ); ?>
								</p>
								<p>
									<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" style="display: inline;">
										<?php wp_nonce_field( 'pt_vm_create_search_page', 'pt_vm_create_page_nonce' ); ?>
										<input type="hidden" name="action" value="pt_vm_create_search_page">
										<?php submit_button( __( 'Automatically create page', 'peertube-video-manager' ), 'secondary', 'submit', false ); ?>
									</form>
								</p>
							<?php endif; ?>
						</td>
					</tr>
					
					<tr>
						<th scope="row">
							<label for="pt_vm_video_page_id">
								<?php esc_html_e( 'Page for video viewing', 'peertube-video-manager' ); ?>
							</label>
						</th>
						<td>
							<?php
							wp_dropdown_pages( array(
								'name'             => 'pt_vm_video_page_id',
								'id'               => 'pt_vm_video_page_id',
								'selected'         => $video_page_id,
								'show_option_none' => __( '— Select a page —', 'peertube-video-manager' ),
								'option_none_value' => '0',
							) );
							?>
							<p class="description">
								<?php esc_html_e( 'Select the page where videos should be displayed in detail. This page will be used automatically when a video is clicked from the lists.', 'peertube-video-manager' ); ?>
							</p>
							<?php if ( $video_page_id > 0 ) : ?>
								<?php
								$page = get_post( $video_page_id );
								if ( $page && 'publish' === $page->post_status ) :
								?>
									<p>
										<a href="<?php echo esc_url( get_edit_post_link( $video_page_id ) ); ?>" target="_blank">
											<?php esc_html_e( 'Edit page', 'peertube-video-manager' ); ?>
										</a> |
										<a href="<?php echo esc_url( get_permalink( $video_page_id ) ); ?>" target="_blank">
											<?php esc_html_e( 'View page', 'peertube-video-manager' ); ?>
										</a>
									</p>
								<?php else : ?>
									<p class="description" style="color: #d63638;">
										<?php esc_html_e( 'The selected page no longer exists or has not been published.', 'peertube-video-manager' ); ?>
									</p>
								<?php endif; ?>
							<?php else : ?>
								<p class="description">
									<strong><?php esc_html_e( 'Hinweis:', 'peertube-video-manager' ); ?></strong>
									<?php esc_html_e( 'If no page is found when the plugin is activated, a new page will be created automatically.', 'peertube-video-manager' ); ?>
								</p>
								<p>
									<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" style="display: inline;">
										<?php wp_nonce_field( 'pt_vm_create_video_page', 'pt_vm_create_video_page_nonce' ); ?>
										<input type="hidden" name="action" value="pt_vm_create_video_page">
										<?php submit_button( __( 'Automatically create page', 'peertube-video-manager' ), 'secondary', 'submit', false ); ?>
									</form>
								</p>
							<?php endif; ?>
						</td>
					</tr>
					
					<tr>
						<th scope="row">
							<label for="pt_vm_peertube_button_text">
								<?php esc_html_e( 'Text of the Media Platform button', 'peertube-video-manager' ); ?>
							</label>
						</th>
						<td>
							<input type="text" 
								   id="pt_vm_peertube_button_text" 
								   name="pt_vm_peertube_button_text" 
								   value="<?php echo esc_attr( $peertube_button_text ); ?>" 
								   class="regular-text">
							<p class="description">
								<?php esc_html_e( 'Text that is displayed on the "Watch on Media Platform" button.', 'peertube-video-manager' ); ?>
							</p>
						</td>
					</tr>
					
					<tr>
						<th scope="row">
							<label for="pt_vm_button_color">
								<?php esc_html_e( 'Color of the buttons', 'peertube-video-manager' ); ?>
							</label>
						</th>
						<td>
							<input type="text" 
								   id="pt_vm_button_color" 
								   name="pt_vm_button_color" 
								   value="<?php echo esc_attr( $button_color ); ?>" 
								   class="pt-color-picker"
								   data-default-color="#1e40af">
							<p class="description">
								<?php esc_html_e( 'Background color of the buttons (default: dark blue)', 'peertube-video-manager' ); ?>
							</p>
						</td>
					</tr>
					
					<tr>
						<th scope="row">
							<label for="pt_vm_button_hover_color">
								<?php esc_html_e( 'Color of the buttons on hover', 'peertube-video-manager' ); ?>
							</label>
						</th>
						<td>
							<input type="text" 
								   id="pt_vm_button_hover_color" 
								   name="pt_vm_button_hover_color" 
								   value="<?php echo esc_attr( $button_hover_color ); ?>" 
								   class="pt-color-picker"
								   data-default-color="#f59e0b">
							<p class="description">
								<?php esc_html_e( 'Background color of the buttons when hovering (default: orange-yellow)', 'peertube-video-manager' ); ?>
							</p>
						</td>
					</tr>
					
					<tr>
						<th scope="row">
							<label for="pt_vm_button_text_color">
								<?php esc_html_e( 'Text color of the buttons', 'peertube-video-manager' ); ?>
							</label>
						</th>
						<td>
							<input type="text" 
								   id="pt_vm_button_text_color" 
								   name="pt_vm_button_text_color" 
								   value="<?php echo esc_attr( $button_text_color ); ?>" 
								   class="pt-color-picker"
								   data-default-color="#ffffff">
							<p class="description">
								<?php esc_html_e( 'Text color of the buttons (default: white)', 'peertube-video-manager' ); ?>
							</p>
						</td>
					</tr>
				</table>
				
				<?php submit_button( __( 'Save settings', 'peertube-video-manager' ) ); ?>
			</form>
			
			<hr>
			
			<h2><?php esc_html_e( 'Cache-Management', 'peertube-video-manager' ); ?></h2>
			<p><?php esc_html_e( 'Clear your cache to load updated data from PeerTube.', 'peertube-video-manager' ); ?></p>
			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
				<?php wp_nonce_field( 'pt_vm_clear_cache', 'pt_vm_cache_nonce' ); ?>
				<input type="hidden" name="action" value="pt_vm_clear_cache">
				<?php submit_button( __( 'Clear cache', 'peertube-video-manager' ), 'secondary', 'submit', false ); ?>
			</form>
			
			<hr>
			
			<h2><?php esc_html_e( 'Shortcode examples', 'peertube-video-manager' ); ?></h2>
			<ul>
				<li><code>[pt-last-videos count="8"]</code> - <?php esc_html_e( 'Latest videos from this instance', 'peertube-video-manager' ); ?></li>
				<li><code>[pt-last-videos count="8" columns="2"]</code> - <?php esc_html_e( 'Latest videos in 2 columns', 'peertube-video-manager' ); ?></li>
				<li><code>[pt-last-videos count="8" columns="1"]</code> - <?php esc_html_e( 'Latest videos in a column', 'peertube-video-manager' ); ?></li>
				<li><code>[pt-latest-per-channel channels="ok_dessau,ok_magdeburg"]</code> - <?php esc_html_e( 'One video per channel', 'peertube-video-manager' ); ?></li>
				<li><code>[pt-latest-per-channel channels="ok_dessau,ok_magdeburg" columns="3"]</code> - <?php esc_html_e( 'One video per channel in 3 columns.', 'peertube-video-manager' ); ?></li>
				<li><code>[pt-channel-videos channel="okmq" count="6"]</code> - <?php esc_html_e( 'Videos from a channel', 'peertube-video-manager' ); ?></li>
				<li><code>[pt-channel-videos channel="okmq" count="6" columns="2"]</code> - <?php esc_html_e( 'Videos from a channel in 2 columns', 'peertube-video-manager' ); ?></li>
			<li><code>[pt-video id="UUID"]</code> - <?php esc_html_e( 'Single video with details', 'peertube-video-manager' ); ?></li>
			<li><code>[pt-video number="123"]</code> - <?php esc_html_e( 'Video per Video Number', 'peertube-video-manager' ); ?></li>
			<li><code>[pt-channels-ordered channels="ok_dessau,ok_magdeburg"]</code> - <?php esc_html_e( 'Channels with videos in the specified order', 'peertube-video-manager' ); ?></li>
			<li><code>[pt-search placeholder="Suche..."]</code> - <?php esc_html_e( 'Search form (with WordPress integration)', 'peertube-video-manager' ); ?></li>
			<li><code>[pt-search-results per_page="12"]</code> - <?php esc_html_e( 'Search results (supports WordPress parameter "s")', 'peertube-video-manager' ); ?></li>
			<li><code>[pt-peertube-search placeholder="Suche..."]</code> - <?php esc_html_e( 'Search form for Media Platform only', 'peertube-video-manager' ); ?></li>
			<li><code>[pt-peertube-search-results per_page="12"]</code> - <?php esc_html_e( 'Search results only for Media Platform (without WordPress integration)', 'peertube-video-manager' ); ?></li>
			</ul>
			<p class="description">
				<strong><?php esc_html_e( 'A notice:', 'peertube-video-manager' ); ?></strong>
				<?php esc_html_e( 'The "columns" parameter can have values ​​from 1 to 6 or "auto" (default, responsive).  When set to "auto," the number of columns automatically adjusts to the screen size.', 'peertube-video-manager' ); ?>
			</p>
		</div>
		<?php
	}

	/**
	 * Handle cache clearing
	 *
	 * @since 1.0.0
	 */
	public function handle_clear_cache() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have permission to perform this action.', 'peertube-video-manager' ) );
		}

		check_admin_referer( 'pt_vm_clear_cache', 'pt_vm_cache_nonce' );

		$cache = new PT_Cache();
		$count = $cache->flush_all();

		add_settings_error(
			'pt_vm_messages',
			'cache_cleared',
			sprintf(
				/* translators: %d: number of cache entries cleared */
				__( 'Cache cleared! %d entries have been deleted.', 'peertube-video-manager' ),
				$count
			),
			'success'
		);

		set_transient( 'pt_vm_cache_cleared', true, 30 );

		wp_safe_redirect( add_query_arg( 'page', 'pt-video-manager', admin_url( 'options-general.php' ) ) );
		exit;
	}

	/**
	 * Handle search page creation
	 *
	 * @since 1.0.8
	 */
	public function handle_create_search_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have permission to perform this action.', 'peertube-video-manager' ) );
		}

		check_admin_referer( 'pt_vm_create_search_page', 'pt_vm_create_page_nonce' );

		// Create search page
		$search_page_content = __( 'Search in the media library', 'peertube-video-manager' ) . "\n\n" .
			'[pt-search placeholder="' . __( 'Suche...', 'peertube-video-manager' ) . '"]' . "\n\n" .
			'[pt-search-results per_page="12"]';

		$page_data = array(
			'post_title'   => __( 'Media Platform Search', 'peertube-video-manager' ),
			'post_content' => $search_page_content,
			'post_status'  => 'publish',
			'post_type'    => 'page',
			'post_name'    => 'peertube-suche',
		);

		$search_page_id = wp_insert_post( $page_data );
		if ( ! is_wp_error( $search_page_id ) && $search_page_id > 0 ) {
			update_option( 'pt_vm_search_page_id', $search_page_id );

			add_settings_error(
				'pt_vm_messages',
				'search_page_created',
				__( 'The search page has been created successfully!', 'peertube-video-manager' ),
				'success'
			);
		} else {
			add_settings_error(
				'pt_vm_messages',
				'search_page_error',
				__( 'Error creating the search page.', 'peertube-video-manager' ),
				'error'
			);
		}

		set_transient( 'pt_vm_settings_errors', get_settings_errors( 'pt_vm_messages' ), 30 );

		wp_safe_redirect( add_query_arg( 'page', 'pt-video-manager', admin_url( 'options-general.php' ) ) );
		exit;
	}

	/**
	 * Handle video page creation
	 *
	 * @since 1.1.2
	 */
	public function handle_create_video_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have permission to perform this action.', 'peertube-video-manager' ) );
		}

		check_admin_referer( 'pt_vm_create_video_page', 'pt_vm_create_video_page_nonce' );

		// Create video page - the shortcode will be added automatically via auto_display_video filter
		$video_page_content = __( 'Media Platform Video', 'peertube-video-manager' );

		$page_data = array(
			'post_title'   => __( 'Media Platform Video', 'peertube-video-manager' ),
			'post_content' => $video_page_content,
			'post_status'  => 'publish',
			'post_type'    => 'page',
			'post_name'    => 'peertube-video',
		);

		$video_page_id = wp_insert_post( $page_data );
		if ( ! is_wp_error( $video_page_id ) && $video_page_id > 0 ) {
			update_option( 'pt_vm_video_page_id', $video_page_id );

			add_settings_error(
				'pt_vm_messages',
				'video_page_created',
				__( 'Video page was created successfully!', 'peertube-video-manager' ),
				'success'
			);
		} else {
			add_settings_error(
				'pt_vm_messages',
				'video_page_error',
				__( 'Error creating the video page.', 'peertube-video-manager' ),
				'error'
			);
		}

		set_transient( 'pt_vm_settings_errors', get_settings_errors( 'pt_vm_messages' ), 30 );

		wp_safe_redirect( add_query_arg( 'page', 'pt-video-manager', admin_url( 'options-general.php' ) ) );
		exit;
	}

	/**
	 * AJAX handler for testing connection
	 *
	 * @since 1.0.0
	 */
	public function ajax_test_connection() {
		check_ajax_referer( 'pt_vm_test_connection', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array(
				'message' => __( 'No authorization.', 'peertube-video-manager' ),
			) );
		}

		$url = isset( $_POST['url'] ) ? esc_url_raw( $_POST['url'] ) : '';
		
		if ( empty( $url ) ) {
			wp_send_json_error( array(
				'message' => __( 'No URL specified.', 'peertube-video-manager' ),
			) );
		}

		// Temporarily set the URL for testing
		$old_url = get_option( 'pt_vm_base_url' );
		update_option( 'pt_vm_base_url', $url );

		$api    = new PT_API();
		$result = $api->test_connection();

		// Restore old URL
		update_option( 'pt_vm_base_url', $old_url );

		if ( $result['success'] ) {
			wp_send_json_success( $result );
		} else {
			wp_send_json_error( $result );
		}
	}

	/**
	 * Enqueue admin scripts
	 *
	 * @param string $hook Current admin page hook.
	 * @since 1.0.0
	 */
	public function enqueue_admin_scripts( $hook ) {
		if ( 'settings_page_pt-video-manager' !== $hook ) {
			return;
		}

		// Enqueue color picker
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'wp-color-picker' );

		wp_enqueue_script(
			'pt-admin-scripts',
			PT_VM_PLUGIN_URL . 'assets/js/pt-admin.js',
			array( 'jquery', 'wp-color-picker' ),
			PT_VM_VERSION,
			true
		);

		wp_localize_script( 'pt-admin-scripts', 'ptAdmin', array(
			'ajaxUrl' => admin_url( 'admin-ajax.php' ),
			'nonce'   => wp_create_nonce( 'pt_vm_test_connection' ),
		) );
	}
}

