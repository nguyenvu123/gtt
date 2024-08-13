			<tr>
				<th scope="row"><?php _e('Custom post types format:', 'fgd2wpp'); ?></th>
				<td>
					<input id="cpt_format_acf" name="cpt_format" type="radio" value="acf" <?php checked($data['cpt_format'], 'acf'); ?> /> <label for="cpt_format_acf" ?><?php _e('ACF', 'fgd2wpp'); ?></label>&nbsp;&nbsp;<small><?php printf(__('The <a href="%s" target="_blank">ACF plugin</a> is required.', 'fgd2wpp'), 'https://wordpress.org/plugins/advanced-custom-fields/'); ?></small><br />
					<input id="cpt_format_pods" name="cpt_format" type="radio" value="pods" <?php checked($data['cpt_format'], 'pods'); ?> /> <label for="cpt_format_pods" ?><?php _e('Pods', 'fgd2wpp'); ?></label>&nbsp;&nbsp;<small><?php printf(__('The <a href="%s" target="_blank">Pods plugin</a> is required.', 'fgd2wpp'), 'https://wordpress.org/plugins/pods/'); ?></small><br />
					<input id="cpt_format_toolset" name="cpt_format" type="radio" value="toolset" <?php checked($data['cpt_format'], 'toolset'); ?> /> <label for="cpt_format_toolset" ?><?php _e('Toolset Types', 'fgd2wpp'); ?></label>&nbsp;&nbsp;<small><?php printf(__('The <a href="%s" target="_blank">Toolset Types plugin</a> is required.', 'fgd2wpp'), 'https://www.fredericgilles.net/toolset-types'); ?></small>
				</td>
			</tr>
			
			<tr>
				<th scope="row"><?php _e('Users:', 'fgd2wpp'); ?></th>
				<td>
					<input id="unicode_usernames" name="unicode_usernames" type="checkbox" value="1" <?php checked($data['unicode_usernames'], 1); ?> /> <label for="unicode_usernames" ><?php _e("Allow Unicode characters in the usernames", 'fgd2wpp'); ?></label>
				</td>
			</tr>
			
			<tr>
				<th scope="row"><?php _e('Links:', 'fgd2wpp'); ?></th>
				<td>
					<?php _e('Import the links:', 'fgd2wpp'); ?>
					<input id="links_as_links" name="links" type="radio" value="as_links" <?php checked($data['links'], 'as_links'); ?> /><label for="links_as_links" ?><?php _e('as links', 'fgd2wpp'); ?></label>&nbsp;&nbsp;
					<input id="links_as_url" name="links" type="radio" value="as_urls" <?php checked($data['links'], 'as_urls'); ?> /><label for="links_as_url" ?><?php _e('as URLs', 'fgd2wpp'); ?></label
				</td>
			</tr>
			
			<tr>
				<th scope="row"><?php _e('SEO:', 'fgd2wpp'); ?></th>
				<td>
					<input id="url_redirect" name="url_redirect" type="checkbox" value="1" <?php checked($data['url_redirect'], 1); ?> /> <label for="url_redirect" ><?php _e("Redirect the Drupal URLs", 'fgd2wpp'); ?></label>
				</td>
			</tr>
