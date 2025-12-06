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
		add_action('admin_menu', array($this, 'register_menu_page'), 101);
	}

	/**
	 * Register menu page
	 */
	public function register_menu_page()
	{
		add_submenu_page(
			'schema-engine-test-dashboard', // Parent slug - Schema Tests menu
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
				<h2>Interactive Condition Tester</h2>
				<p>Test template conditions against any post, page, or user context.</p>

				<form method="get" action="" style="margin-bottom: 20px;">
					<input type="hidden" name="page" value="schema-engine-diagnostics">

					<table class="form-table">
						<tr>
							<th scope="row"><label for="test_post_id">Test Post/Page ID:</label></th>
							<td>
								<input type="number" id="test_post_id" name="test_post_id"
									value="<?php echo esc_attr(isset($_GET['test_post_id']) ? $_GET['test_post_id'] : ''); ?>"
									placeholder="Enter post ID" style="width: 200px;">
								<p class="description">Enter a post or page ID to test template matching</p>
							</td>
						</tr>
						<tr>
							<th scope="row"><label for="simulate_context">Simulate Context:</label></th>
							<td>
								<select id="simulate_context" name="simulate_context" style="width: 200px;">
									<option value="">Normal (Current User)</option>
									<option value="author_archive" <?php selected(isset($_GET['simulate_context']) && $_GET['simulate_context'] === 'author_archive'); ?>>Author Archive</option>
									<option value="logged_out" <?php selected(isset($_GET['simulate_context']) && $_GET['simulate_context'] === 'logged_out'); ?>>Logged Out</option>
								</select>
								<p class="description">Simulate different viewing contexts</p>
							</td>
						</tr>
					</table>

					<p class="submit">
						<input type="submit" class="button button-primary" value="Test Conditions">
					</p>
				</form>

				<?php
				if (isset($_GET['test_post_id']) && !empty($_GET['test_post_id'])):
					$test_post_id = intval($_GET['test_post_id']);
					$test_post = get_post($test_post_id);

					if ($test_post):
						?>
						<div style="background: #f0f0f1; padding: 15px; border-left: 4px solid #2271b1; margin-bottom: 20px;">
							<h3 style="margin-top: 0;">Testing Results</h3>
							<p><strong>Post:</strong> "<?php echo esc_html($test_post->post_title); ?>"
								(ID: <?php echo esc_html($test_post->ID); ?>, Type: <?php echo esc_html($test_post->post_type); ?>)</p>
							<p><strong>Context:</strong> <?php
							$context = isset($_GET['simulate_context']) ? $_GET['simulate_context'] : 'normal';
							echo esc_html(ucwords(str_replace('_', ' ', $context)));
							?></p>
						</div>

						<?php
						// Get matching templates using the loader
						if (class_exists('Schema_Template_Loader')) {
							$loader = Schema_Template_Loader::get_instance();
							$matching = $loader->get_templates_for_post($test_post_id);
							?>
							<h3>Matching Templates: <?php echo count($matching); ?></h3>
							<?php if (!empty($matching)): ?>
								<table class="widefat striped">
									<thead>
										<tr>
											<th>Template</th>
											<th>Schema Type</th>
											<th>Conditions</th>
										</tr>
									</thead>
									<tbody>
										<?php foreach ($matching as $m):
											$template_post = get_post($m['id']);
											$schema_data = get_post_meta($m['id'], '_schema_template_data', true);
											$conditions = isset($schema_data['includeConditions']) ? $schema_data['includeConditions'] : array();
											?>
											<tr>
												<td>
													<a href="<?php echo esc_url(get_edit_post_link($m['id'])); ?>">
														<strong><?php echo esc_html($m['title']); ?></strong>
													</a>
												</td>
												<td><?php echo esc_html($m['schemaType']); ?></td>
												<td>
													<?php if (!empty($conditions['groups'])): ?>
														<details>
															<summary style="cursor: pointer; color: #2271b1;">View Conditions</summary>
															<pre
																style="font-size: 11px; background: #fff; padding: 10px; margin: 5px 0; max-height: 200px; overflow: auto;"><?php echo esc_html(wp_json_encode($conditions, JSON_PRETTY_PRINT)); ?></pre>
														</details>
													<?php else: ?>
														<em>No conditions</em>
													<?php endif; ?>
												</td>
											</tr>
										<?php endforeach; ?>
									</tbody>
								</table>
							<?php else: ?>
								<p style="color: #d63638; padding: 10px; background: #fcf0f1; border-left: 4px solid #d63638;">
									<strong>No templates match this post.</strong> Check your template conditions.
								</p>
							<?php endif; ?>
						<?php } else { ?>
							<p style="color: #d63638;">Schema_Template_Loader class not found.</p>
						<?php } ?>
					<?php else: ?>
						<p style="color: #d63638; padding: 10px; background: #fcf0f1; border-left: 4px solid #d63638;">
							<strong>Post ID <?php echo esc_html($test_post_id); ?> not found.</strong>
						</p>
					<?php endif; ?>
				<?php else: ?>
					<p style="color: #666; font-style: italic;">Enter a post ID above and click "Test Conditions" to see which
						templates match.</p>
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
