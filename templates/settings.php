<?php
\load_template(
	__DIR__ . '/admin-header.php',
	true,
	array(
		'settings'  => 'active',
		'welcome'   => '',
		'followers' => '',
	)
);
?>

<div class="activitypub-settings activitypub-settings-page hide-if-no-js">
	<form method="post" action="options.php">
		<?php \settings_fields( 'activitypub' ); ?>

		<div class="box">
			<h3><?php \esc_html_e( 'Profiles', 'activitypub' ); ?></h3>
			<table class="form-table">
				<tbody>
					<tr>
						<th scope="row">
							<?php \esc_html_e( 'Enable profiles by type', 'activitypub' ); ?>
						</th>
						<td>
							<p>
								<label>
									<input type="checkbox" name="activitypub_enable_users" id="activitypub_enable_users" value="1" <?php echo \checked( '1', \get_option( 'activitypub_enable_users', '1' ) ); ?> />
									<?php \esc_html_e( 'Enable authors', 'activitypub' ); ?>
								</label>
							</p>
							<p class="description">
								<?php echo \wp_kses( \__( 'Every author on this blog (with the <code>publish_posts</code> capability) gets their own ActivityPub profile.', 'activitypub' ), array( 'code' => array() ) ); ?>
							</p>
							<p>
								<label>
									<input type="checkbox" name="activitypub_enable_blog_user" id="activitypub_enable_blog_user" value="1" <?php echo \checked( '1', \get_option( 'activitypub_enable_blog_user', '0' ) ); ?> />
									<?php \esc_html_e( 'Enable blog', 'activitypub' ); ?>
								</label>
							</p>
							<p class="description">
								<?php \esc_html_e( 'Your blog becomes an ActivityPub profile.', 'activitypub' ); ?>
							</p>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<?php \esc_html_e( 'Change blog profile ID', 'activitypub' ); ?>
						</th>
						<td>
							<label for="activitypub_blog_user_identifier">
								<input class="blog-user-identifier" name="activitypub_blog_user_identifier" id="activitypub_blog_user_identifier" type="text" value="<?php echo esc_attr( \get_option( 'activitypub_blog_user_identifier', \Activitypub\Model\Blog_User::get_default_username() ) ); ?>" />
								@<?php echo esc_html( \wp_parse_url( \home_url(), PHP_URL_HOST ) ); ?>
							</label>
							<p class="description">
								<?php \esc_html_e( 'This profile name will federate all posts written on your blog, regardless of the author who posted it.', 'activitypub' ); ?>
							</p>
						</td>
					</tr>
				</tbody>
			</table>

			<?php \do_settings_fields( 'activitypub', 'user' ); ?>
		</div>

		<div class="box">
			<h3><?php \esc_html_e( 'Activities', 'activitypub' ); ?></h3>
			<table class="form-table">
				<tbody>
					<tr>
						<th scope="row">
							<?php \esc_html_e( 'Post content', 'activitypub' ); ?>
						</th>
						<td>
							<p>
								<label for="activitypub_post_content_type_title_link">
									<input type="radio" name="activitypub_post_content_type" id="activitypub_post_content_type_title_link" value="title" <?php echo \checked( 'title', \get_option( 'activitypub_post_content_type', 'content' ) ); ?> />
									<?php \esc_html_e( 'Title and link', 'activitypub' ); ?>
									-
									<span class="description">
										<?php \esc_html_e( 'Only the title and a link.', 'activitypub' ); ?>
									</span>
								</label>
							</p>
							<p>
								<label for="activitypub_post_content_type_excerpt">
									<input type="radio" name="activitypub_post_content_type" id="activitypub_post_content_type_excerpt" value="excerpt" <?php echo \checked( 'excerpt', \get_option( 'activitypub_post_content_type', 'content' ) ); ?> />
									<?php \esc_html_e( 'Excerpt', 'activitypub' ); ?>
									-
									<span class="description">
										<?php \esc_html_e( 'A content summary, shortened to 400 characters and without markup.', 'activitypub' ); ?>
									</span>
								</label>
							</p>
							<p>
								<label for="activitypub_post_content_type_content">
									<input type="radio" name="activitypub_post_content_type" id="activitypub_post_content_type_content" value="content" <?php echo \checked( 'content', \get_option( 'activitypub_post_content_type', 'content' ) ); ?> />
									<?php \esc_html_e( 'Content (default)', 'activitypub' ); ?>
									-
									<span class="description">
										<?php \esc_html_e( 'The full content.', 'activitypub' ); ?>
									</span>
								</label>
							</p>
							<p>
								<label for="activitypub_post_content_type_custom">
									<input type="radio" name="activitypub_post_content_type" id="activitypub_post_content_type_custom" value="custom" <?php echo \checked( 'custom', \get_option( 'activitypub_post_content_type', 'content' ) ); ?> />
									<?php \esc_html_e( 'Custom', 'activitypub' ); ?>
									-
									<span class="description">
										<?php \esc_html_e( 'Use the text area below, to customize your activities.', 'activitypub' ); ?>
									</span>
								</label>
							</p>
							<p>
								<textarea name="activitypub_custom_post_content" id="activitypub_custom_post_content" rows="10" cols="50" class="large-text" placeholder="<?php echo wp_kses( ACTIVITYPUB_CUSTOM_POST_CONTENT, 'post' ); ?>"><?php echo wp_kses( \get_option( 'activitypub_custom_post_content', ACTIVITYPUB_CUSTOM_POST_CONTENT ), 'post' ); ?></textarea>
								<details>
									<summary><?php esc_html_e( 'See a list of ActivityPub Template Tags.', 'activitypub' ); ?></summary>
									<div class="description">
										<ul>
											<li><code>[ap_title]</code> - <?php \esc_html_e( 'The post\'s title.', 'activitypub' ); ?></li>
											<li><code>[ap_content]</code> - <?php \esc_html_e( 'The post\'s content.', 'activitypub' ); ?></li>
											<li><code>[ap_excerpt]</code> - <?php \esc_html_e( 'The post\'s excerpt (default 400 chars).', 'activitypub' ); ?></li>
											<li><code>[ap_permalink]</code> - <?php \esc_html_e( 'The post\'s permalink.', 'activitypub' ); ?></li>
											<li><code>[ap_shortlink]</code> - <?php echo \wp_kses( \__( 'The post\'s shortlink. I can recommend <a href="https://wordpress.org/plugins/hum/" target="_blank">Hum</a>.', 'activitypub' ), 'default' ); ?></li>
											<li><code>[ap_hashtags]</code> - <?php \esc_html_e( 'The post\'s tags as hashtags.', 'activitypub' ); ?></li>
										</ul>
										<p><?php \esc_html_e( 'You can find the full list with all possible attributes in the help section on the top-right of the screen.', 'activitypub' ); ?></p>
									</div>
								</details>
							</p>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<?php \esc_html_e( 'Number of images', 'activitypub' ); ?>
						</th>
						<td>
							<input value="<?php echo esc_attr( \get_option( 'activitypub_max_image_attachments', ACTIVITYPUB_MAX_IMAGE_ATTACHMENTS ) ); ?>" name="activitypub_max_image_attachments" id="activitypub_max_image_attachments" type="number" min="0" />
							<p class="description">
								<?php
								echo \wp_kses(
									\sprintf(
										// translators:
										\__( 'The number of images to attach to posts. Default: <code>%s</code>', 'activitypub' ),
										\esc_html( ACTIVITYPUB_MAX_IMAGE_ATTACHMENTS )
									),
									'default'
								);
								?>
							</p>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<?php \esc_html_e( 'Activity-Object-Type', 'activitypub' ); ?>
						</th>
						<td>
							<p>
								<label for="activitypub_object_type_note">
									<input type="radio" name="activitypub_object_type" id="activitypub_object_type_note" value="note" <?php echo \checked( 'note', \get_option( 'activitypub_object_type', 'note' ) ); ?> />
									<?php \esc_html_e( 'Note (default)', 'activitypub' ); ?>
									-
									<span class="description">
										<?php \esc_html_e( 'Should work with most platforms.', 'activitypub' ); ?>
									</span>
								</label>
							</p>
							<p><strong><?php \esc_html_e( 'Please note that the following "Activity-Object-Type" options may cause your texts to be displayed differently on each platform and/or parts may be completely ignored. Mastodon, for example, displays all content that is not of the "Note" type as links only.', 'activitypub' ); ?></strong></p>
							<p>
								<label for="activitypub_object_type_article">
									<input type="radio" name="activitypub_object_type" id="activitypub_object_type_article" value="article" <?php echo \checked( 'article', \get_option( 'activitypub_object_type', 'note' ) ); ?> />
									<?php \esc_html_e( 'Article', 'activitypub' ); ?>
									-
									<span class="description">
										<?php \esc_html_e( 'The presentation of the "Article" might change on different platforms.', 'activitypub' ); ?>
									</span>
								</label>
							</p>
							<p>
								<label>
									<input type="radio" name="activitypub_object_type" id="activitypub_object_type" value="wordpress-post-format" <?php echo \checked( 'wordpress-post-format', \get_option( 'activitypub_object_type', 'note' ) ); ?> />
									<?php \esc_html_e( 'WordPress Post-Format', 'activitypub' ); ?>
									-
									<span class="description">
										<?php \esc_html_e( 'Maps the WordPress Post-Format to the ActivityPub Object Type.', 'activitypub' ); ?>
									</span>
								</label>
							</p>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php \esc_html_e( 'Supported post types', 'activitypub' ); ?></th>
						<td>
							<fieldset>
								<?php \esc_html_e( 'Enable ActivityPub support for the following post types:', 'activitypub' ); ?>

								<?php $post_types = \get_post_types( array( 'public' => true ), 'objects' ); ?>
								<?php $support_post_types = \get_option( 'activitypub_support_post_types', array( 'post', 'page' ) ) ? \get_option( 'activitypub_support_post_types', array( 'post', 'page' ) ) : array(); ?>
								<ul>
								<?php // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited ?>
								<?php foreach ( $post_types as $post_type ) { ?>
									<li>
										<input type="checkbox" id="activitypub_support_post_type_<?php echo \esc_attr( $post_type->name ); ?>" name="activitypub_support_post_types[]" value="<?php echo \esc_attr( $post_type->name ); ?>" <?php echo \checked( \in_array( $post_type->name, $support_post_types, true ) ); ?> />
										<label for="activitypub_support_post_type_<?php echo \esc_attr( $post_type->name ); ?>"><?php echo \esc_html( $post_type->label ); ?></label>
									</li>
								<?php } ?>
								</ul>
							</fieldset>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<?php \esc_html_e( 'Hashtags (beta)', 'activitypub' ); ?>
						</th>
						<td>
							<p>
								<label><input type="checkbox" name="activitypub_use_hashtags" id="activitypub_use_hashtags" value="1" <?php echo \checked( '1', \get_option( 'activitypub_use_hashtags', '1' ) ); ?> /> <?php echo wp_kses( \__( 'Add hashtags in the content as native tags and replace the <code>#tag</code> with the tag link. <strong>This feature is experimental! Please disable it, if you find any HTML or CSS errors.</strong>', 'activitypub' ), 'default' ); ?></label>
							</p>
						</td>
					</tr>
				</tbody>
			</table>

			<?php \do_settings_fields( 'activitypub', 'activity' ); ?>
		</div>

		<div class="box">
			<h3><?php \esc_html_e( 'Server', 'activitypub' ); ?></h3>
			<table class="form-table">
				<tbody>
					<tr>
						<th scope="row">
							<?php \esc_html_e( 'Blocklist', 'activitypub' ); ?>
						</th>
						<td>
							<p class="description">
								<?php
								echo \wp_kses(
									\sprintf(
										// translators: %s is a URL.
										\__( 'To block servers, add the host of the server to the "<a href="%s">Disallowed Comment Keys</a>" list.', 'activitypub' ),
										\esc_attr( \admin_url( 'options-discussion.php#disallowed_keys' ) )
									),
									'default'
								);
								?>
							</p>
						</td>
					</tr>
				</tbody>
			</table>

			<?php \do_settings_fields( 'activitypub', 'server' ); ?>
		</div>
		<?php \do_settings_sections( 'activitypub' ); ?>

		<?php \submit_button(); ?>
	</form>
</div>
