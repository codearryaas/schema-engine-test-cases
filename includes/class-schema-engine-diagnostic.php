<?php
/**
 * Diagnostic page for Schema Engine (Test Cases)
 *
 * Access via: Tools > Schema Engine Diagnostics or via Test Cases menu
 */

if (!defined('ABSPATH')) {
	exit;
}

/**
 * Schema_Engine_Diagnostic class
 */
class Schema_Engine_Diagnostic
{

	/**
	 * Instance
	 */
	private static $instance = null;

	/**
	 * Get instance
	 */
	public static function get_instance()
	{
		if (null === self::$instance) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor
	 */
	private function __construct()
	{
		add_action('admin_menu', array($this, 'register_menu_page'));
	}

	/**
	 * Register menu page
	 */
	public function register_menu_page()
	{
		add_submenu_page(
			'schema-engine-test-cases', // Parent slug
			'Diagnostic Tool',
			'Diagnostics',
			'manage_options',
			'schema-engine-diagnostics',
			array($this, 'render_page')
		);
	}

	/**
	 * Render page
	 */
	public function render_page()
	{
		// Ensure Schema Engine constants are available
		if (!defined('SCHEMA_ENGINE_PLUGIN_DIR')) {
			echo '<div class="notice notice-error"><p>Schema Engine plugin is not active.</p></div>';
			return;
		}
		?>
		<div class="wrap">
			<h1>Schema Engine Diagnostics</h1>

			<div class="card">
				<h2>Plugin Paths</h2>
				<table class="widefat">
					<tr>
						<td><strong>Plugin Directory:</strong></td>
						<td><code><?php echo esc_html(SCHEMA_ENGINE_PLUGIN_DIR); ?></code></td>
					</tr>
					<tr>
						<td><strong>Plugin URL:</strong></td>
						<td><code><?php echo esc_html(SCHEMA_ENGINE_PLUGIN_URL); ?></code></td>
					</tr>
				</table>
			</div>

			<div class="card" style="margin-top: 20px;">
				<h2>Build Files</h2>
				<table class="widefat">
					<thead>
						<tr>
							<th>File</th>
							<th>Status</th>
							<th>Path</th>
						</tr>
					</thead>
					<tbody>
						<?php
						$files = array(
							'Asset File' => 'build/post-metabox/index.asset.php', // Updated path
							'JavaScript' => 'build/post-metabox/index.js',       // Updated path
							'CSS' => 'build/post-metabox/style-index.css', // Updated path
						);

						foreach ($files as $label => $file) {
							$full_path = SCHEMA_ENGINE_PLUGIN_DIR . $file;
							$exists = file_exists($full_path);
							$size = $exists ? size_format(filesize($full_path)) : 'N/A';
							?>
							<tr>
								<td><strong><?php echo esc_html($label); ?></strong></td>
								<td>
									<?php if ($exists): ?>
										<span style="color: green;">✓ EXISTS</span> (<?php echo esc_html($size); ?>)
									<?php else: ?>
										<span style="color: red;">✗ MISSING</span>
									<?php endif; ?>
								</td>
								<td><code><?php echo esc_html($full_path); ?></code></td>
							</tr>
							<?php
						}
						?>
					</tbody>
				</table>
			</div>

			<div class="card" style="margin-top: 20px;">
				<h2>Custom Post Type</h2>
				<?php
				$post_type = get_post_type_object('sm_template');
				if ($post_type):
					?>
					<p style="color: green;"><strong>✓ sm_template is registered</strong></p>
					<table class="widefat">
						<tr>
							<td><strong>Label:</strong></td>
							<td><?php echo esc_html($post_type->label); ?></td>
						</tr>
						<tr>
							<td><strong>Public:</strong></td>
							<td><?php echo esc_html($post_type->public ? 'Yes' : 'No'); ?></td>
						</tr>
						<tr>
							<td><strong>Show UI:</strong></td>
							<td><?php echo esc_html($post_type->show_ui ? 'Yes' : 'No'); ?></td>
						</tr>
						<tr>
							<td><strong>Menu Icon:</strong></td>
							<td><?php echo esc_html($post_type->menu_icon); ?></td>
						</tr>
					</table>
				<?php else: ?>
					<p style="color: red;"><strong>✗ sm_template is NOT registered</strong></p>
					<p>Try deactivating and reactivating the plugin.</p>
				<?php endif; ?>
			</div>

			<div class="card" style="margin-top: 20px;">
				<h2>Asset Dependencies</h2>
				<?php
				$asset_file = SCHEMA_ENGINE_PLUGIN_DIR . 'build/post-metabox/index.asset.php'; // Updated path
				if (file_exists($asset_file)):
					$asset = require $asset_file;
					?>
					<p><strong>Dependencies:</strong></p>
					<ul>
						<?php foreach ($asset['dependencies'] as $dep): ?>
							<li><code><?php echo esc_html($dep); ?></code></li>
						<?php endforeach; ?>
					</ul>
					<p><strong>Version:</strong> <code><?php echo esc_html($asset['version']); ?></code></p>
				<?php else: ?>
					<p style="color: red;">Asset file not found!</p>
				<?php endif; ?>
			</div>

			<div class="card" style="margin-top: 20px;">
				<h2>Template Count</h2>
				<?php
				$templates = get_posts(array(
					'post_type' => 'sm_template',
					'post_status' => 'any',
					'posts_per_page' => -1,
				));
				?>
				<p>Total Templates: <strong><?php echo count($templates); ?></strong></p>
				<?php if (!empty($templates)): ?>
					<ul>
						<?php foreach ($templates as $template): ?>
							<?php
							$schema_data = get_post_meta($template->ID, '_schema_template_data', true);
							$schema_type = isset($schema_data['schemaType']) ? $schema_data['schemaType'] : 'Not set';
							$included_by_default = isset($schema_data['includedByDefault']) ? $schema_data['includedByDefault'] : false;
							$include_conditions = isset($schema_data['includeConditions']) ? $schema_data['includeConditions'] : array();
							?>
							<li style="margin-bottom: 15px; padding: 10px; background: #f9f9f9; border-left: 3px solid #2271b1;">
								<a href="<?php echo esc_url(get_edit_post_link($template->ID)); ?>">
									<strong><?php echo esc_html($template->post_title); ?></strong>
								</a>
								(<?php echo esc_html($template->post_status); ?>)
								<br>
								<small>
									<strong>Type:</strong> <?php echo esc_html($schema_type); ?> |
									<strong>Included by Default:</strong> <?php echo esc_html($included_by_default ? 'Yes' : 'No'); ?>
								</small>
								<?php if (!empty($include_conditions['groups'])): ?>
									<br><small><strong>Conditions:</strong></small>
									<pre
										style="font-size: 11px; background: #fff; padding: 5px; margin: 5px 0; max-height: 150px; overflow: auto;">
					<?php echo esc_html(wp_json_encode($include_conditions, JSON_PRETTY_PRINT)); ?>
														</pre>
								<?php endif; ?>
							</li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
			</div>

			<div class="card" style="margin-top: 20px;">
				<h2>Test Template Matching</h2>
				<?php
				// Test with a sample post
				$sample_post = get_posts(array(
					'post_type' => 'post',
					'posts_per_page' => 1,
					'post_status' => 'publish',
				));

				if (!empty($sample_post)):
					$test_post = $sample_post[0];
					$post_metabox = Schema_Engine_Post_Metabox::get_instance();
					$matching = $post_metabox->get_matching_templates($test_post->ID);
					?>
					<p><strong>Testing with post:</strong> "<?php echo esc_html($test_post->post_title); ?>" (ID:
						<?php echo esc_html($test_post->ID); ?>, Type: <?php echo esc_html($test_post->post_type); ?>)</p>
					<p><strong>Matching Templates:</strong> <?php echo count($matching); ?></p>
					<?php if (!empty($matching)): ?>
						<ul>
							<?php foreach ($matching as $m): ?>
								<li><?php echo esc_html($m['title']); ?> (<?php echo esc_html($m['schemaType']); ?>)</li>
							<?php endforeach; ?>
						</ul>
					<?php else: ?>
						<p style="color: #666;">No templates match this post.</p>
					<?php endif; ?>
				<?php else: ?>
					<p>No published posts to test with.</p>
				<?php endif; ?>

				<?php
				// Test with a sample page
				$sample_page = get_posts(array(
					'post_type' => 'page',
					'posts_per_page' => 1,
					'post_status' => 'publish',
				));

				if (!empty($sample_page)):
					$test_page = $sample_page[0];
					$matching_page = $post_metabox->get_matching_templates($test_page->ID);
					?>
					<hr>
					<p><strong>Testing with page:</strong> "<?php echo esc_html($test_page->post_title); ?>" (ID:
						<?php echo esc_html($test_page->ID); ?>, Type: <?php echo esc_html($test_page->post_type); ?>)</p>
					<p><strong>Matching Templates:</strong> <?php echo count($matching_page); ?></p>
					<?php if (!empty($matching_page)): ?>
						<ul>
							<?php foreach ($matching_page as $m): ?>
								<li><?php echo esc_html($m['title']); ?> (<?php echo esc_html($m['schemaType']); ?>)</li>
							<?php endforeach; ?>
						</ul>
					<?php else: ?>
						<p style="color: #666;">No templates match this page.</p>
					<?php endif; ?>
				<?php endif; ?>
			</div>

			<div class="card" style="margin-top: 20px;">
				<h2>System Information</h2>
				<table class="widefat">
					<tr>
						<td><strong>WordPress Version:</strong></td>
						<td><?php echo esc_html(get_bloginfo('version')); ?></td>
					</tr>
					<tr>
						<td><strong>PHP Version:</strong></td>
						<td><?php echo esc_html(phpversion()); ?></td>
					</tr>
					<tr>
						<td><strong>Active Theme:</strong></td>
						<td><?php echo esc_html(wp_get_theme()->get('Name')); ?></td>
					</tr>
				</table>
			</div>
		</div>
		<?php
	}
}

// Initialize diagnostic
Schema_Engine_Diagnostic::get_instance();
