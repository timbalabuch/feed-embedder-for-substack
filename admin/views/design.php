<?php
/**
 * Design tab.
 *
 * @package Feed_Embedder_For_Substack
 * @var array $design Design settings (provided by FEFS_Admin::render_page()).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<h3><?php esc_html_e( 'Layout', 'feed-embedder-for-substack' ); ?></h3>
<table class="form-table" role="presentation">
	<tr>
		<th scope="row"><?php esc_html_e( 'Orientation', 'feed-embedder-for-substack' ); ?></th>
		<td>
			<fieldset>
				<label>
					<input type="radio" name="fefs_design[orientation]" value="horizontal" <?php checked( $design['orientation'], 'horizontal' ); ?> />
					<?php esc_html_e( 'Horizontal (image beside text)', 'feed-embedder-for-substack' ); ?>
				</label><br />
				<label>
					<input type="radio" name="fefs_design[orientation]" value="vertical" <?php checked( $design['orientation'], 'vertical' ); ?> />
					<?php esc_html_e( 'Vertical / stacked (grid of cards)', 'feed-embedder-for-substack' ); ?>
				</label>
			</fieldset>
		</td>
	</tr>
	<tr>
		<th scope="row">
			<label for="fefs-columns"><?php esc_html_e( 'Columns (vertical layout)', 'feed-embedder-for-substack' ); ?></label>
		</th>
		<td>
			<select id="fefs-columns" name="fefs_design[columns]">
				<?php foreach ( array( 1, 2, 3 ) as $fefs_cols ) : ?>
					<option value="<?php echo esc_attr( $fefs_cols ); ?>" <?php selected( (int) $design['columns'], $fefs_cols ); ?>><?php echo esc_html( $fefs_cols ); ?></option>
				<?php endforeach; ?>
			</select>
		</td>
	</tr>
	<tr>
		<th scope="row">
			<label for="fefs-gap"><?php esc_html_e( 'Gap between cards', 'feed-embedder-for-substack' ); ?></label>
		</th>
		<td><input type="text" id="fefs-gap" name="fefs_design[gap]" class="small-text" value="<?php echo esc_attr( $design['gap'] ); ?>" /></td>
	</tr>
	<tr>
		<th scope="row">
			<label for="fefs-card-padding"><?php esc_html_e( 'Card padding', 'feed-embedder-for-substack' ); ?></label>
		</th>
		<td><input type="text" id="fefs-card-padding" name="fefs_design[card_padding]" class="small-text" value="<?php echo esc_attr( $design['card_padding'] ); ?>" /></td>
	</tr>
</table>

<h3><?php esc_html_e( 'Card', 'feed-embedder-for-substack' ); ?></h3>
<table class="form-table" role="presentation">
	<tr>
		<th scope="row">
			<label for="fefs-card-bg"><?php esc_html_e( 'Background', 'feed-embedder-for-substack' ); ?></label>
		</th>
		<td><input type="text" id="fefs-card-bg" name="fefs_design[card_bg]" class="fefs-color" value="<?php echo esc_attr( $design['card_bg'] ); ?>" /></td>
	</tr>
	<tr>
		<th scope="row">
			<label for="fefs-card-bg-hover"><?php esc_html_e( 'Background on hover', 'feed-embedder-for-substack' ); ?></label>
		</th>
		<td>
			<input type="text" id="fefs-card-bg-hover" name="fefs_design[card_bg_hover]" class="fefs-color" value="<?php echo esc_attr( $design['card_bg_hover'] ); ?>" />
			<p class="description"><?php esc_html_e( 'Leave empty for no change on hover.', 'feed-embedder-for-substack' ); ?></p>
		</td>
	</tr>
	<tr>
		<th scope="row">
			<label for="fefs-card-radius"><?php esc_html_e( 'Border radius', 'feed-embedder-for-substack' ); ?></label>
		</th>
		<td><input type="text" id="fefs-card-radius" name="fefs_design[card_radius]" class="small-text" value="<?php echo esc_attr( $design['card_radius'] ); ?>" /></td>
	</tr>
	<tr>
		<th scope="row">
			<label for="fefs-card-border"><?php esc_html_e( 'Border', 'feed-embedder-for-substack' ); ?></label>
		</th>
		<td>
			<input type="text" id="fefs-card-border" name="fefs_design[card_border]" class="regular-text" value="<?php echo esc_attr( $design['card_border'] ); ?>" />
			<p class="description"><?php esc_html_e( 'Any CSS border shorthand, e.g. 1px solid #e5e7eb.', 'feed-embedder-for-substack' ); ?></p>
		</td>
	</tr>
	<tr>
		<th scope="row"><?php esc_html_e( 'Box shadow on hover', 'feed-embedder-for-substack' ); ?></th>
		<td>
			<label>
				<input type="checkbox" name="fefs_design[card_shadow_hover]" value="1" <?php checked( ! empty( $design['card_shadow_hover'] ) ); ?> />
				<?php esc_html_e( 'Add a soft shadow when hovering a card', 'feed-embedder-for-substack' ); ?>
			</label>
		</td>
	</tr>
</table>

<h3><?php esc_html_e( 'Image', 'feed-embedder-for-substack' ); ?></h3>
<table class="form-table" role="presentation">
	<tr>
		<th scope="row"><?php esc_html_e( 'Show image', 'feed-embedder-for-substack' ); ?></th>
		<td>
			<label>
				<input type="checkbox" name="fefs_design[show_image]" value="1" <?php checked( ! empty( $design['show_image'] ) ); ?> />
				<?php esc_html_e( 'Display the post image', 'feed-embedder-for-substack' ); ?>
			</label>
		</td>
	</tr>
	<tr>
		<th scope="row">
			<label for="fefs-img-width"><?php esc_html_e( 'Width (horizontal layout)', 'feed-embedder-for-substack' ); ?></label>
		</th>
		<td><input type="text" id="fefs-img-width" name="fefs_design[img_width]" class="small-text" value="<?php echo esc_attr( $design['img_width'] ); ?>" /></td>
	</tr>
	<tr>
		<th scope="row">
			<label for="fefs-img-height"><?php esc_html_e( 'Height (horizontal layout)', 'feed-embedder-for-substack' ); ?></label>
		</th>
		<td><input type="text" id="fefs-img-height" name="fefs_design[img_height]" class="small-text" value="<?php echo esc_attr( $design['img_height'] ); ?>" /></td>
	</tr>
	<tr>
		<th scope="row">
			<label for="fefs-img-height-v"><?php esc_html_e( 'Height (vertical/grid layout)', 'feed-embedder-for-substack' ); ?></label>
		</th>
		<td>
			<input type="text" id="fefs-img-height-v" name="fefs_design[img_height_v]" class="small-text" value="<?php echo esc_attr( $design['img_height_v'] ); ?>" />
			<p class="description"><?php esc_html_e( 'Use "auto" for a 16:9 aspect ratio.', 'feed-embedder-for-substack' ); ?></p>
		</td>
	</tr>
	<tr>
		<th scope="row">
			<label for="fefs-img-radius"><?php esc_html_e( 'Image border radius', 'feed-embedder-for-substack' ); ?></label>
		</th>
		<td><input type="text" id="fefs-img-radius" name="fefs_design[img_radius]" class="small-text" value="<?php echo esc_attr( $design['img_radius'] ); ?>" /></td>
	</tr>
</table>

<h3><?php esc_html_e( 'Typography', 'feed-embedder-for-substack' ); ?></h3>
<table class="form-table" role="presentation">
	<tr>
		<th scope="row">
			<label for="fefs-title-color"><?php esc_html_e( 'Title color', 'feed-embedder-for-substack' ); ?></label>
		</th>
		<td><input type="text" id="fefs-title-color" name="fefs_design[title_color]" class="fefs-color" value="<?php echo esc_attr( $design['title_color'] ); ?>" /></td>
	</tr>
	<tr>
		<th scope="row">
			<label for="fefs-title-color-hover"><?php esc_html_e( 'Title color on hover', 'feed-embedder-for-substack' ); ?></label>
		</th>
		<td>
			<input type="text" id="fefs-title-color-hover" name="fefs_design[title_color_hover]" class="fefs-color" value="<?php echo esc_attr( $design['title_color_hover'] ); ?>" />
			<p class="description"><?php esc_html_e( 'Leave empty for no change on hover.', 'feed-embedder-for-substack' ); ?></p>
		</td>
	</tr>
	<tr>
		<th scope="row">
			<label for="fefs-title-size"><?php esc_html_e( 'Title font size', 'feed-embedder-for-substack' ); ?></label>
		</th>
		<td><input type="text" id="fefs-title-size" name="fefs_design[title_size]" class="small-text" value="<?php echo esc_attr( $design['title_size'] ); ?>" /></td>
	</tr>
	<tr>
		<th scope="row">
			<label for="fefs-title-weight"><?php esc_html_e( 'Title font weight', 'feed-embedder-for-substack' ); ?></label>
		</th>
		<td>
			<select id="fefs-title-weight" name="fefs_design[title_weight]">
				<?php foreach ( array( '400', '500', '600', '700' ) as $fefs_weight ) : ?>
					<option value="<?php echo esc_attr( $fefs_weight ); ?>" <?php selected( (string) $design['title_weight'], $fefs_weight ); ?>><?php echo esc_html( $fefs_weight ); ?></option>
				<?php endforeach; ?>
			</select>
		</td>
	</tr>
	<tr>
		<th scope="row">
			<label for="fefs-title-lh"><?php esc_html_e( 'Title line height', 'feed-embedder-for-substack' ); ?></label>
		</th>
		<td><input type="text" id="fefs-title-lh" name="fefs_design[title_line_height]" class="small-text" value="<?php echo esc_attr( $design['title_line_height'] ); ?>" /></td>
	</tr>
	<tr>
		<th scope="row">
			<label for="fefs-title-ls"><?php esc_html_e( 'Title letter spacing', 'feed-embedder-for-substack' ); ?></label>
		</th>
		<td><input type="text" id="fefs-title-ls" name="fefs_design[title_spacing]" class="small-text" value="<?php echo esc_attr( $design['title_spacing'] ); ?>" /></td>
	</tr>
	<tr>
		<th scope="row"><?php esc_html_e( 'Show date', 'feed-embedder-for-substack' ); ?></th>
		<td>
			<label>
				<input type="checkbox" name="fefs_design[show_date]" value="1" <?php checked( ! empty( $design['show_date'] ) ); ?> />
				<?php esc_html_e( 'Display the post date', 'feed-embedder-for-substack' ); ?>
			</label>
		</td>
	</tr>
	<tr>
		<th scope="row">
			<label for="fefs-date-color"><?php esc_html_e( 'Date color', 'feed-embedder-for-substack' ); ?></label>
		</th>
		<td><input type="text" id="fefs-date-color" name="fefs_design[date_color]" class="fefs-color" value="<?php echo esc_attr( $design['date_color'] ); ?>" /></td>
	</tr>
	<tr>
		<th scope="row">
			<label for="fefs-date-size"><?php esc_html_e( 'Date font size', 'feed-embedder-for-substack' ); ?></label>
		</th>
		<td><input type="text" id="fefs-date-size" name="fefs_design[date_size]" class="small-text" value="<?php echo esc_attr( $design['date_size'] ); ?>" /></td>
	</tr>
	<tr>
		<th scope="row">
			<label for="fefs-date-format"><?php esc_html_e( 'Date format', 'feed-embedder-for-substack' ); ?></label>
		</th>
		<td>
			<input type="text" id="fefs-date-format" name="fefs_design[date_format]" class="small-text" value="<?php echo esc_attr( $design['date_format'] ); ?>" />
			<p class="description">
				<a href="https://wordpress.org/documentation/article/customize-date-and-time-format/" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Documentation on date and time formatting.', 'feed-embedder-for-substack' ); ?></a>
			</p>
		</td>
	</tr>
	<tr>
		<th scope="row"><?php esc_html_e( 'Show excerpt', 'feed-embedder-for-substack' ); ?></th>
		<td>
			<label>
				<input type="checkbox" name="fefs_design[show_excerpt]" value="1" <?php checked( ! empty( $design['show_excerpt'] ) ); ?> />
				<?php esc_html_e( 'Display the post excerpt', 'feed-embedder-for-substack' ); ?>
			</label>
		</td>
	</tr>
	<tr>
		<th scope="row">
			<label for="fefs-excerpt-length"><?php esc_html_e( 'Excerpt length (characters)', 'feed-embedder-for-substack' ); ?></label>
		</th>
		<td><input type="number" id="fefs-excerpt-length" name="fefs_design[excerpt_length]" class="small-text" min="1" max="1000" value="<?php echo esc_attr( $design['excerpt_length'] ); ?>" /></td>
	</tr>
</table>

<h3><?php esc_html_e( 'Mobile', 'feed-embedder-for-substack' ); ?></h3>
<table class="form-table" role="presentation">
	<tr>
		<th scope="row">
			<label for="fefs-breakpoint"><?php esc_html_e( 'Mobile breakpoint (px)', 'feed-embedder-for-substack' ); ?></label>
		</th>
		<td><input type="number" id="fefs-breakpoint" name="fefs_design[mobile_breakpoint]" class="small-text" min="1" max="3000" value="<?php echo esc_attr( $design['mobile_breakpoint'] ); ?>" /></td>
	</tr>
	<tr>
		<th scope="row"><?php esc_html_e( 'Stack on mobile', 'feed-embedder-for-substack' ); ?></th>
		<td>
			<label>
				<input type="checkbox" name="fefs_design[mobile_stack]" value="1" <?php checked( ! empty( $design['mobile_stack'] ) ); ?> />
				<?php esc_html_e( 'Stack cards vertically below the breakpoint', 'feed-embedder-for-substack' ); ?>
			</label>
		</td>
	</tr>
	<tr>
		<th scope="row">
			<label for="fefs-mobile-title-size"><?php esc_html_e( 'Title font size on mobile', 'feed-embedder-for-substack' ); ?></label>
		</th>
		<td><input type="text" id="fefs-mobile-title-size" name="fefs_design[mobile_title_size]" class="small-text" value="<?php echo esc_attr( $design['mobile_title_size'] ); ?>" /></td>
	</tr>
</table>
