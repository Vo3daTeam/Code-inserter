<?php
/**
 * The template of plugin settings.
 *
 * @package Code_Inserter/admin
 */

?>
<div class="custom_code_inserter_wrap">
	<?php settings_errors(); ?>
	<div class="custom_code_inserter_page">
		<h2 class="title"><?php echo esc_html( get_admin_page_title() ); ?><span
					class="version"><?php echo esc_html( $version ); ?></span></h2>
		<form action="<?php echo esc_url( admin_url( 'options.php' ) ); ?>"
				data-nonce="<?php echo esc_html( wp_create_nonce( 'code_inserter' ) ); ?>" id="code_inserter_form"
				method="post"
				name="options">
			<div class="flex">
				<div class="w-50">
					<div class="vo3da-metabox m-0">
						<div class="vo3da-metabox__head"><?php esc_html_e( 'Choose domain', 'vo3da-code-inserter' ); ?></div>
						<div class="vo3da-metabox__body">
							<?php
							settings_fields( 'code-insert-settings-group' );
							$cookie         = filter_input( INPUT_COOKIE, $this->plugin_name . '_active_domains', FILTER_SANITIZE_STRING );
							$active_domains = ! empty( $cookie ) ? json_decode( str_replace( '\"', '"', $cookie ), true ) : [];
							$current_domain = ! empty( $active_domains ) ? $active_domains[0] : '';
							?>
							<p><?php esc_html_e( 'Select domain', 'vo3da-code-inserter' ); ?></p>
							<label>
								<select name="code_inserter[domains][]" class="code-inserter-select2">
									<?php
									foreach ( $this->mirrors as $mirror ) {
										if ( empty( $current_domain ) ) {
											$current_domain = $mirror;
										}
										$selected = ! empty( $active_domains ) ? selected( in_array( $mirror, $active_domains, true ), true, false ) : selected( $current_domain, $mirror, false )
										?>
										<option value="<?php echo esc_html( $mirror ); ?>" <?php echo esc_html( $selected ); ?>><?php echo esc_html( $mirror ); ?></option>
									<?php } ?>
									<option value="*"><?php esc_html_e( 'Select all', 'vo3da-code-inserter' ); ?></option>
								</select>
							</label>
						</div>
					</div>
				</div>
				<div class="w-50">
					<div class="vo3da-metabox m-0" id="gtm_section">
						<div class="vo3da-metabox__head"><?php esc_html_e( 'Google Tag Manager', 'vo3da-code-inserter' ); ?></div>
						<div class="vo3da-metabox__body">
							<p><?php esc_html_e( 'Insert code for Google Tag Manager', 'vo3da-code-inserter' ); ?></p>
							<div class="input-row">
								<div class="form-section">
									<label class="input-area">
										<input class="vo3da-input form-input" name="code_inserter[gtm]"
												placeholder="<?php esc_html_e( 'GTM-*******', 'vo3da-code-inserter' ); ?>"
												value="<?php echo ! empty( $this->options[ $current_domain ]['gtm']['content'] ) ? esc_html( $this->options[ $current_domain ]['gtm']['content'] ) : ''; ?>">
									</label>
								</div>
								<label for="send_button" id="save_gtm" data-section="gtm_section"
										class="btn-blue send-form"><?php esc_html_e( 'Save', 'vo3da-code-inserter' ); ?></label>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div id="head_section"
					class="vo3da-metabox vo3da-accordion <?php echo empty( $this->options[ $current_domain ]['header_after']['content'] ) && empty( $this->options[ $current_domain ]['header']['content'] ) ? 'close arrow-down' : ''; ?>">
				<div class="vo3da-metabox__head" id="head_section_trigger"
						title="<?php esc_html_e( 'Click to expand', 'vo3da-code-inserter' ); ?>">
					<span><?php esc_html_e( 'Head section', 'vo3da-code-inserter' ); ?></span>
					<span class="vo3da-info" title="<?php esc_html_e( 'More info', 'vo3da-code-inserter' ); ?>"><span
								class="vo3da-info__icon"></span><span
								class="vo3da-info__text"><code>&#60;head&#62;</code> <?php esc_html_e( 'Your code will be here', 'vo3da-code-inserter' ); ?> <code>&#60;/head&#62;</code></span></span>
				</div>
				<div class="vo3da-metabox__body">
					<p><?php esc_html_e( 'Insert code after', 'vo3da-code-inserter' ); ?> <code>&lt;head&gt;</code></p>
					<p><?php esc_html_e( 'Use', 'vo3da-code-inserter' ); ?> <code>&lt;link&gt;</code>, <code>&lt;script&gt;</code>,
						<code>&lt;meta&gt;</code> <?php esc_html_e( 'HTML tags that are valid inside the', 'vo3da-code-inserter' ); ?>
						<code>&#60;head&#62;</code>
					</p>
					<div class="form-section">
						<label class="input-area">
								<textarea class="form-input" name="code_inserter[header_after]" cols="150"
										rows="15"><?php echo ! empty( $this->options[ $current_domain ]['header_after']['content'] ) ? esc_html( $this->options[ $current_domain ]['header_after']['content'] ) : ''; ?></textarea>
						</label>
						<div class="vo3da-checkbox">
							<input type="checkbox" id="code_inserter_header_after_amp"
									name="code_inserter[header_after_amp_disable]" <?php checked( true, $this->options[ $current_domain ]['header_after']['disable_on_amp'], 1 ); ?> >
							<label for="code_inserter_header_after_amp"> <span class="checkbox"></span> <span class="text"><?php esc_html_e( 'Disable on amp', 'vo3da-code-inserter' ); ?> </span></label>
							<span class="vo3da-info vo3da-info--small"><span class="vo3da-info__icon"></span><span class="vo3da-info__text"><?php esc_html_e( 'Only available if there are amp pages', 'vo3da-code-inserter' ); ?></span></span>
						</div>
					</div>
					<p><?php esc_html_e( 'Insert code before', 'vo3da-code-inserter' ); ?> <code>&lt;/head&gt;</code>
					</p>
					<p><?php esc_html_e( 'Use', 'vo3da-code-inserter' ); ?> <code>&lt;link&gt;</code>, <code>&lt;script&gt;</code>, <code>&lt;meta&gt;</code> <?php esc_html_e( 'HTML tags that are valid inside the', 'vo3da-code-inserter' ); ?> <code>&#60;head&#62;</code>
					</p>
					<div class="form-section">
						<label class="input-area">
								<textarea class="form-input" name="code_inserter[header]" cols="150"
										rows="15"><?php echo ! empty( $this->options[ $current_domain ]['header']['content'] ) ? esc_html( $this->options[ $current_domain ]['header']['content'] ) : ''; ?></textarea>
						</label>
						<div class="vo3da-checkbox">
							<input type="checkbox" id="code_inserter_header_amp"
									name="code_inserter[header_amp_disable]" <?php checked( true, $this->options[ $current_domain ]['header']['disable_on_amp'], 1 ); ?> >
							<label for="code_inserter_header_amp"> <span class="checkbox"></span> <span class="text"><?php esc_html_e( 'Disable on amp', 'vo3da-code-inserter' ); ?> </span></label>
							<span class="vo3da-info vo3da-info--small"><span class="vo3da-info__icon"></span><span class="vo3da-info__text"><?php esc_html_e( 'Only available if there are amp pages', 'vo3da-code-inserter' ); ?></span></span>
						</div>
					</div>
					<label for="send_button" id="save_head" data-section="head_section"
							class="btn-blue send-form"><?php esc_html_e( 'Save', 'vo3da-code-inserter' ); ?></label>
				</div>
			</div>
			<div id="body_section"
					class="vo3da-metabox vo3da-accordion <?php echo empty( $this->options[ $current_domain ]['body']['content'] ) && empty( $this->options[ $current_domain ]['body_before']['content'] ) ? 'close arrow-down' : ''; ?>">
				<div class="vo3da-metabox__head" id="body_section_trigger"
						title="<?php esc_html_e( 'Click to expand', 'vo3da-code-inserter' ); ?>">
					<span><?php esc_html_e( 'Body section', 'vo3da-code-inserter' ); ?></span>
					<span class="vo3da-info" title="<?php esc_html_e( 'More info', 'vo3da-code-inserter' ); ?>"><span
								class="vo3da-info__icon"></span><span
								class="vo3da-info__text"><code>&#60;body&#62;</code> <?php esc_html_e( 'Your code will be here', 'vo3da-code-inserter' ); ?> <code>&#60;/body&#62;</code></span></span>
				</div>
				<div class="vo3da-metabox__body">
					<p><?php esc_html_e( 'Insert code after', 'vo3da-code-inserter' ); ?> <code>&#60;body&#62;</code>
					</p>
					<div class="form-section">
						<label class="input-area">
							<textarea class="form-input" name="code_inserter[body]" cols="150"
									rows="15"><?php echo ! empty( $this->options[ $current_domain ]['body']['content'] ) ? esc_html( $this->options[ $current_domain ]['body']['content'] ) : ''; ?></textarea>
						</label>
						<div class="vo3da-checkbox">
							<input type="checkbox" id="code_inserter_body_amp"
									name="code_inserter[body_amp_disable]" <?php checked( true, $this->options[ $current_domain ]['body']['disable_on_amp'], 1 ); ?> >
							<label for="code_inserter_body_amp"> <span class="checkbox"></span> <span class="text"><?php esc_html_e( 'Disable on amp', 'vo3da-code-inserter' ); ?> </span></label>
							<span class="vo3da-info vo3da-info--small"><span class="vo3da-info__icon"></span><span class="vo3da-info__text"><?php esc_html_e( 'Only available if there are amp pages', 'vo3da-code-inserter' ); ?></span></span>
						</div>
					</div>
					<p><?php esc_html_e( 'Insert code before', 'vo3da-code-inserter' ); ?> <code>&#60;/body&#62;</code>
					</p>
					<div class="form-section">
						<label class="input-area">
							<textarea class="form-input" name="code_inserter[body_before]" cols="150"
									rows="15"><?php echo ! empty( $this->options[ $current_domain ]['body_before']['content'] ) ? esc_html( $this->options[ $current_domain ]['body_before']['content'] ) : ''; ?></textarea>
						</label>
						<div class="vo3da-checkbox">
							<input type="checkbox" id="code_inserter_body_before_amp"
									name="code_inserter[body_before_amp_disable]" <?php checked( true, $this->options[ $current_domain ]['body_before']['disable_on_amp'], 1 ); ?>>
							<label for="code_inserter_body_before_amp"> <span class="checkbox"></span> <span class="text"><?php esc_html_e( 'Disable on amp', 'vo3da-code-inserter' ); ?> </span></label>
							<span class="vo3da-info vo3da-info--small"><span class="vo3da-info__icon"></span><span class="vo3da-info__text"><?php esc_html_e( 'Only available if there are amp pages', 'vo3da-code-inserter' ); ?></span></span>
						</div>
					</div>
					<label for="send_button" id="save_body" data-section="body_section"
							class="btn-blue send-form"><?php esc_html_e( 'Save', 'vo3da-code-inserter' ); ?></label>
				</div>
			</div>
			<div id="post_section"
					class="vo3da-metabox vo3da-accordion <?php echo empty( $this->options[ $current_domain ]['title_after_post']['content'] ) ? 'close arrow-down' : ''; ?>">
				<div class="vo3da-metabox__head" id="post_section_trigger"
						title="<?php esc_html_e( 'Click to expand', 'vo3da-code-inserter' ); ?>">
					<span><?php esc_html_e( 'Code after', 'vo3da-code-inserter' ); ?> <code>&#60;h1&#62;</code> <?php esc_html_e( 'Post', 'vo3da-code-inserter' ); ?></span>
					<span class="vo3da-info" title="<?php esc_html_e( 'More info' ); ?>"><span class="vo3da-info__icon"></span><span
								class="vo3da-info__text"><code>&#60;h1&#62;</code>Title<code>&#60;/h1&#62;</code> <?php esc_html_e( 'Your code will be here', 'vo3da-code-inserter' ); ?></span></span>
				</div>
				<div class="vo3da-metabox__body">
					<p><?php esc_html_e( 'Insert code after', 'vo3da-code-inserter' ); ?> <code>&#60;/h1&#62;</code></p>
					<div class="form-section">
						<label class="input-area">
								<textarea class="form-input" name="code_inserter[title_after_post]" cols="150"
										rows="15"><?php echo ! empty( $this->options[ $current_domain ]['title_after_post']['content'] ) ? esc_html( $this->options[ $current_domain ]['title_after_post']['content'] ) : ''; ?></textarea>
						</label>
						<div class="vo3da-checkbox">
							<input type="checkbox" id="code_inserter_title_after_post_amp"
									name="code_inserter[title_after_post_amp_disable]" <?php checked( true, $this->options[ $current_domain ]['title_after_post']['disable_on_amp'], 1 ); ?> >
							<label for="code_inserter_title_after_post_amp"><span class="checkbox"></span> <span class="text"> <?php esc_html_e( 'Disable on amp', 'vo3da-code-inserter' ); ?> </span></label>
							<span class="vo3da-info vo3da-info--small"><span class="vo3da-info__icon"></span><span class="vo3da-info__text"><?php esc_html_e( 'Only available if there are amp pages', 'vo3da-code-inserter' ); ?></span></span>
						</div>
					</div>
					<label for="send_button" id="save_post" data-section="post_section"
							class="btn-blue send-form"><?php esc_html_e( 'Save', 'vo3da-code-inserter' ); ?></label>
				</div>
			</div>
			<div id="page_section"
					class="vo3da-metabox vo3da-accordion <?php echo empty( $this->options[ $current_domain ]['title_after_page']['content'] ) ? 'close arrow-down' : ''; ?>">
				<div class="vo3da-metabox__head" id="page_section_trigger"
						title="<?php esc_html_e( 'Click to expand', 'vo3da-code-inserter' ); ?>">
					<span><?php esc_html_e( 'Code after', 'vo3da-code-inserter' ); ?> <code>&#60;h1&#62;</code> <?php esc_html_e( 'Page', 'vo3da-code-inserter' ); ?></span>
					<span class="vo3da-info" title="<?php esc_html_e( 'More info', 'vo3da-code-inserter' ); ?>"><span
								class="vo3da-info__icon"></span><span
								class="vo3da-info__text"><code>&#60;h1&#62;</code>Title<code>&#60;/h1&#62;</code> <?php esc_html_e( 'Your code will be here', 'vo3da-code-inserter' ); ?></span></span>
				</div>
				<div class="vo3da-metabox__body">
					<p><?php esc_html_e( 'Insert code after', 'vo3da-code-inserter' ); ?> <code>&#60;/h1&#62;</code></p>
					<div class="form-section">
						<label class="input-area">
							<textarea class="form-input" name="code_inserter[title_after_page]" cols="150"
									rows="15"><?php echo ! empty( $this->options[ $current_domain ]['title_after_page']['content'] ) ? esc_html( $this->options[ $current_domain ]['title_after_page']['content'] ) : ''; ?></textarea>
						</label>
						<div class="vo3da-checkbox">
							<input type="checkbox" id="code_inserter_title_after_page_amp"
									name="code_inserter[title_after_page_amp_disable]" <?php checked( true, $this->options[ $current_domain ]['title_after_page']['disable_on_amp'], 1 ); ?> >
							<label for="code_inserter_title_after_page_amp"> <span class="checkbox"></span> <span class="text"><?php esc_html_e( 'Disable on amp', 'vo3da-code-inserter' ); ?> </span></label>
							<span class="vo3da-info vo3da-info--small"><span class="vo3da-info__icon"></span><span class="vo3da-info__text"><?php esc_html_e( 'Only available if there are amp pages', 'vo3da-code-inserter' ); ?></span></span>
						</div>
					</div>
					<label for="send_button" id="save_page" data-section="page_section"
							class="btn-blue send-form"><?php esc_html_e( 'Save', 'vo3da-code-inserter' ); ?></label>
				</div>
			</div>
			<div id="category_section"
					class="vo3da-metabox vo3da-accordion <?php echo empty( $this->options[ $current_domain ]['title_after_category']['content'] ) ? 'close arrow-down' : ''; ?>">
				<div class="vo3da-metabox__head" id="category_section_trigger"
						title="<?php esc_html_e( 'Click to expand', 'vo3da-code-inserter' ); ?>">
					<span><?php esc_html_e( 'Code after', 'vo3da-code-inserter' ); ?> <code>&#60;h1&#62;</code> <?php esc_html_e( 'Category', 'vo3da-code-inserter' ); ?> </span>
					<span class="vo3da-info" title="<?php esc_html( 'More info' ); ?>"><span class="vo3da-info__icon"></span><span
								class="vo3da-info__text"><code>&#60;h1&#62;</code>Title<code>&#60;/h1&#62;</code> <?php esc_html_e( 'Your code will be here', 'vo3da-code-inserter' ); ?></span></span>
				</div>
				<div class="vo3da-metabox__body">
					<p><?php esc_html_e( 'Insert code after', 'vo3da-code-inserter' ); ?> <code>&#60;/h1&#62;</code></p>
					<div class="form-section">
						<label class="input-area">
							<textarea class="form-input" name="code_inserter[title_after_category]" cols="150"
									rows="15"><?php echo ! empty( $this->options[ $current_domain ]['title_after_category']['content'] ) ? esc_html( $this->options[ $current_domain ]['title_after_category']['content'] ) : ''; ?></textarea>
						</label>
						<div class="vo3da-checkbox">
							<input type="checkbox" id="code_inserter_title_after_category_amp"
									name="code_inserter[title_after_category_amp_disable]" <?php checked( true, $this->options[ $current_domain ]['title_after_category']['disable_on_amp'], 1 ); ?> >
							<label for="code_inserter_title_after_category_amp"> <span class="checkbox"></span> <span class="text"><?php esc_html_e( 'Disable on amp', 'vo3da-code-inserter' ); ?> </span></label>
							<span class="vo3da-info vo3da-info--small"><span class="vo3da-info__icon"></span><span class="vo3da-info__text"><?php esc_html_e( 'Only available if there are amp pages', 'vo3da-code-inserter' ); ?></span></span>
						</div>
					</div>
					<label for="send_button" id="save_category" data-section="category_section"
							class="btn-blue send-form"><?php esc_html_e( 'Save', 'vo3da-code-inserter' ); ?></label>
				</div>
			</div>
			<div id="footer_section"
					class="vo3da-metabox vo3da-accordion <?php echo empty( $this->options[ $current_domain ]['footer']['content'] ) ? 'close arrow-down' : ''; ?>">
				<div class="vo3da-metabox__head" id="footer_section_trigger"
						title="<?php esc_html_e( 'Click to expand', 'vo3da-code-inserter' ); ?>">
					<span><?php esc_html_e( 'Footer section', 'vo3da-code-inserter' ); ?></span>
					<span class="vo3da-info" title="More info"><span class="vo3da-info__icon"></span><span
								class="vo3da-info__text"><code>&#60;footer&#62;</code> <?php esc_html_e( 'Your code will be here', 'vo3da-code-inserter' ); ?> <code>&#60;/footer&#62;</code></span></span>
				</div>
				<div class="vo3da-metabox__body">
					<p><?php esc_html_e( 'Insert code in', 'vo3da-code-inserter' ); ?> <code>&#60;footer&#62;</code></p>
					<div class="form-section">
						<label class="input-area">
							<textarea class="form-input" name="code_inserter[footer]" cols="150"
									rows="15"><?php echo ! empty( $this->options[ $current_domain ]['footer']['content'] ) ? esc_html( $this->options[ $current_domain ]['footer']['content'] ) : ''; ?></textarea>
						</label>
						<div class="vo3da-checkbox">
							<input type="checkbox" id="code_inserter_footer_amp"
									name="code_inserter[footer_amp_disable]" <?php checked( true, $this->options[ $current_domain ]['footer']['disable_on_amp'], 1 ); ?> >
							<label for="code_inserter_footer_amp"> <span class="checkbox"></span> <span class="text"><?php esc_html_e( 'Disable on amp', 'vo3da-code-inserter' ); ?> </span></label>
							<span class="vo3da-info vo3da-info--small"><span class="vo3da-info__icon"></span><span class="vo3da-info__text"><?php esc_html_e( 'Only available if there are amp pages', 'vo3da-code-inserter' ); ?></span></span>
						</div>
					</div>
					<label for="send_button" id="save_footer" data-section="footer_section"
							class="btn-blue send-form"><?php esc_html_e( 'Save', 'vo3da-code-inserter' ); ?></label>
				</div>
			</div>
			<div id="content_section"
					class="vo3da-metabox vo3da-accordion <?php echo empty( $this->options[ $current_domain ]['before_content']['content'] ) && empty( $this->options[ $current_domain ]['after_content'] ) ? 'close arrow-down' : ''; ?>">
				<div class="vo3da-metabox__head" id="content_section_trigger"
						title="<?php esc_html_e( 'Click to expand', 'vo3da-code-inserter' ); ?>">
					<span><?php esc_html_e( 'Content section', 'vo3da-code-inserter' ); ?></span>
					<span class="vo3da-info" title="<?php esc_html_e( 'More info', 'vo3da-code-inserter' ); ?>"><span
								class="vo3da-info__icon"></span><span
								class="vo3da-info__text"><?php esc_html_e( 'This code will be added in your the_content() or get_the_content() functions', 'vo3da-code-inserter' ); ?></span></span>
				</div>
				<div class="vo3da-metabox__body">
					<p><?php esc_html_e( 'Insert code before content', 'vo3da-code-inserter' ); ?>:</p>
					<div class="form-section">
						<label class="input-area">
							<textarea class="form-input" name="code_inserter[before_content]" cols="150"
									rows="15"><?php echo ! empty( $this->options[ $current_domain ]['before_content']['content'] ) ? esc_html( $this->options[ $current_domain ]['before_content']['content'] ) : ''; ?></textarea>
						</label>
						<div class="vo3da-checkbox">
							<input type="checkbox" id="code_inserter_before_content_amp"
									name="code_inserter[before_content_amp_disable]" <?php checked( true, $this->options[ $current_domain ]['before_content']['disable_on_amp'], 1 ); ?> >
							<label for="code_inserter_before_content_amp"> <span class="checkbox"></span> <span class="text"><?php esc_html_e( 'Disable on amp', 'vo3da-code-inserter' ); ?> </span></label>
							<span class="vo3da-info vo3da-info--small"><span class="vo3da-info__icon"></span><span class="vo3da-info__text"><?php esc_html_e( 'Only available if there are amp pages', 'vo3da-code-inserter' ); ?></span></span>
						</div>
					</div>
					<div class="form-section">
						<p><?php esc_html_e( 'Insert code after content', 'vo3da-code-inserter' ); ?>:</p>
						<label class="input-area">
							<textarea class="form-input" name="code_inserter[after_content]" cols="150"
									rows="15"><?php echo ! empty( $this->options[ $current_domain ]['after_content']['content'] ) ? esc_html( $this->options[ $current_domain ]['after_content']['content'] ) : ''; ?></textarea>
						</label>
						<div class="vo3da-checkbox">
							<input type="checkbox" id="code_inserter_after_content_amp"
									name="code_inserter[after_content_amp_disable]" <?php checked( true, $this->options[ $current_domain ]['after_content']['disable_on_amp'], 1 ); ?> >
							<label for="code_inserter_after_content_amp"> <span class="checkbox"></span> <span class="text"><?php esc_html_e( 'Disable on amp', 'vo3da-code-inserter' ); ?> </span></label>
							<span class="vo3da-info vo3da-info--small"><span class="vo3da-info__icon"></span><span class="vo3da-info__text"><?php esc_html_e( 'Only available if there are amp pages', 'vo3da-code-inserter' ); ?></span></span>
						</div>
					</div>
					<label for="send_button" id="save_content" data-section="content_section"
							class="btn-blue send-form"><?php esc_html_e( 'Save', 'vo3da-code-inserter' ); ?></label>
				</div>

			</div>
			<input type="submit" class="button button-primary d-none" id="send_button" name="Submit"
					value="<?php esc_html_e( 'Save', 'vo3da-code-inserter' ); ?>"/>
		</form>
		<p class="vo3da-author"><?php esc_html_e( 'Developed with love by', 'vo3da-code-inserter' ); ?> <a
					href="https://vo3da.tech" target="_blank">vo3da.tech</a>
		</p>
	</div>
</div>
