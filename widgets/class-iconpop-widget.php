<?php
namespace IconPop;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Utils;

defined( 'ABSPATH' ) || exit;

class IconPop_Widget extends Widget_Base {

	public function get_name()        { return 'iconpop_widget'; }
	public function get_title()       { return esc_html__( 'IconPop Grid', 'iconpop-elementor' ); }
	public function get_icon()        { return 'eicon-apps'; }
	public function get_categories()  { return [ 'iconpop' ]; }
	public function get_keywords()    { return [ 'icon', 'popup', 'hover', 'grid', 'full width' ]; }
	public function get_style_depends()  { return [ 'iconpop-style' ]; }
	public function get_script_depends() { return [ 'iconpop-script' ]; }

	/* ── Register assets ─────────────────────────────────────────── */

	public function __construct( $data = [], $args = null ) {
		parent::__construct( $data, $args );

		wp_register_style(
			'iconpop-style',
			ICONPOP_URL . 'assets/css/iconpop.css',
			[],
			wp_rand()
		);

		wp_register_script(
			'iconpop-script',
			ICONPOP_URL . 'assets/js/iconpop.js',
			[ 'jquery' ],
			wp_rand(),
			true
		);
	}

	/* ── Controls ────────────────────────────────────────────────── */

	protected function register_controls() {

		/* ── CONTENT: Background ── */
		$this->start_controls_section( 'section_background', [
			'label' => esc_html__( 'Section Background', 'iconpop-elementor' ),
			'tab'   => Controls_Manager::TAB_CONTENT,
		] );

		$this->add_control( 'bg_image', [
			'label'       => esc_html__( 'Background Image', 'iconpop-elementor' ),
			'type'        => Controls_Manager::MEDIA,
			'default'     => [ 'url' => '' ],
			'description' => esc_html__( 'Upload or choose a background image for the full-width section.', 'iconpop-elementor' ),
		] );

		$this->add_control( 'bg_overlay_color', [
			'label'     => esc_html__( 'Overlay Color', 'iconpop-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => 'rgba(0,0,0,0.45)',
			'selectors' => [
				'{{WRAPPER}} .iconpop-bg-overlay' => 'background-color: {{VALUE}};',
			],
		] );

		$this->add_control( 'bg_position', [
			'label'   => esc_html__( 'Background Position', 'iconpop-elementor' ),
			'type'    => Controls_Manager::SELECT,
			'default' => 'center center',
			'options' => [
				'center center' => esc_html__( 'Center Center', 'iconpop-elementor' ),
				'center top'    => esc_html__( 'Center Top', 'iconpop-elementor' ),
				'center bottom' => esc_html__( 'Center Bottom', 'iconpop-elementor' ),
				'left center'   => esc_html__( 'Left Center', 'iconpop-elementor' ),
				'right center'  => esc_html__( 'Right Center', 'iconpop-elementor' ),
			],
		] );

		$this->add_control( 'bg_size', [
			'label'   => esc_html__( 'Background Size', 'iconpop-elementor' ),
			'type'    => Controls_Manager::SELECT,
			'default' => 'cover',
			'options' => [
				'cover'   => esc_html__( 'Cover', 'iconpop-elementor' ),
				'contain' => esc_html__( 'Contain', 'iconpop-elementor' ),
				'auto'    => esc_html__( 'Auto', 'iconpop-elementor' ),
			],
		] );

		$this->end_controls_section();

		/* ── CONTENT: Icons ── */
		$this->start_controls_section( 'section_icons', [
			'label' => esc_html__( 'Icons', 'iconpop-elementor' ),
			'tab'   => Controls_Manager::TAB_CONTENT,
		] );

		$repeater = new Repeater();

		$repeater->add_control( 'item_label', [
			'label'       => esc_html__( 'Label', 'iconpop-elementor' ),
			'type'        => Controls_Manager::TEXT,
			'default'     => 'Icon Label',
			'label_block' => true,
		] );

		$repeater->add_control( 'item_icon_default', [
			'label'       => esc_html__( 'Default Icon Image', 'iconpop-elementor' ),
			'type'        => Controls_Manager::MEDIA,
			'default'     => [ 'url' => '' ],
			'description' => esc_html__( 'Upload the blue/default version of this icon.', 'iconpop-elementor' ),
		] );

		$repeater->add_control( 'item_icon_hover', [
			'label'       => esc_html__( 'Hover Icon Image', 'iconpop-elementor' ),
			'type'        => Controls_Manager::MEDIA,
			'default'     => [ 'url' => '' ],
			'description' => esc_html__( 'Upload the full-colour hover version of this icon.', 'iconpop-elementor' ),
		] );

		$repeater->add_control( 'popup_heading', [
			'label'       => esc_html__( 'Popup Heading', 'iconpop-elementor' ),
			'type'        => Controls_Manager::TEXT,
			'default'     => 'Feature Title',
			'label_block' => true,
			'separator'   => 'before',
		] );

		$repeater->add_control( 'popup_content', [
			'label'   => esc_html__( 'Popup Content', 'iconpop-elementor' ),
			'type'    => Controls_Manager::WYSIWYG,
			'default' => '<p>Describe this feature or service here. You can add headings, images, text, and buttons using the editor above.</p>',
		] );

		$repeater->add_control( 'popup_btn_text', [
			'label'     => esc_html__( 'Button Text', 'iconpop-elementor' ),
			'type'      => Controls_Manager::TEXT,
			'default'   => '',
			'separator' => 'before',
		] );

		$repeater->add_control( 'popup_btn_url', [
			'label'       => esc_html__( 'Button URL', 'iconpop-elementor' ),
			'type'        => Controls_Manager::URL,
			'placeholder' => 'https://',
			'default'     => [ 'url' => '' ],
		] );

		$this->add_control( 'icons', [
			'label'       => esc_html__( 'Icon Items', 'iconpop-elementor' ),
			'type'        => Controls_Manager::REPEATER,
			'fields'      => $repeater->get_controls(),
			'default'     => $this->default_items(),
			'title_field' => '{{{ item_label }}}',
		] );

		$this->end_controls_section();

		/* ── CONTENT: Layout ── */
		$this->start_controls_section( 'section_layout', [
			'label' => esc_html__( 'Layout', 'iconpop-elementor' ),
			'tab'   => Controls_Manager::TAB_CONTENT,
		] );

		$this->add_responsive_control( 'grid_columns', [
			'label'          => esc_html__( 'Columns', 'iconpop-elementor' ),
			'type'           => Controls_Manager::SELECT,
			'default'        => '6',
			'tablet_default' => '4',
			'mobile_default' => '2',
			'options'        => [
				'1' => '1',
				'2' => '2',
				'3' => '3',
				'4' => '4',
				'5' => '5',
				'6' => '6',
			],
			'selectors' => [
				'{{WRAPPER}} .iconpop-layout' => 'grid-template-columns: repeat({{VALUE}}, 1fr);',
			],
		] );

		$this->add_responsive_control( 'grid_gap', [
			'label'          => esc_html__( 'Gap', 'iconpop-elementor' ),
			'type'           => Controls_Manager::SLIDER,
			'size_units'     => [ 'px' ],
			'range'          => [ 'px' => [ 'min' => 0, 'max' => 80, 'step' => 2 ] ],
			'default'        => [ 'unit' => 'px', 'size' => 32 ],
			'tablet_default' => [ 'unit' => 'px', 'size' => 24 ],
			'mobile_default' => [ 'unit' => 'px', 'size' => 16 ],
			'selectors'      => [
				'{{WRAPPER}} .iconpop-layout' => 'gap: {{SIZE}}px;',
			],
		] );

		$this->add_responsive_control( 'section_padding', [
			'label'          => esc_html__( 'Section Padding', 'iconpop-elementor' ),
			'type'           => Controls_Manager::DIMENSIONS,
			'size_units'     => [ 'px', 'em', '%' ],
			'default'        => [ 'top' => '80', 'right' => '40', 'bottom' => '80', 'left' => '40', 'unit' => 'px' ],
			'tablet_default' => [ 'top' => '60', 'right' => '24', 'bottom' => '60', 'left' => '24', 'unit' => 'px' ],
			'mobile_default' => [ 'top' => '40', 'right' => '16', 'bottom' => '40', 'left' => '16', 'unit' => 'px' ],
			'selectors'      => [
				'{{WRAPPER}} .iconpop-inner' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			],
		] );

		$this->end_controls_section();

		/* ── STYLE: Labels ── */
		$this->start_controls_section( 'style_labels', [
			'label' => esc_html__( 'Icon Labels', 'iconpop-elementor' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		] );

		$this->add_control( 'label_color', [
			'label'     => esc_html__( 'Label Color', 'iconpop-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#ffffff',
			'selectors' => [ '{{WRAPPER}} .iconpop-label' => 'color: {{VALUE}};' ],
		] );

		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name'     => 'label_typography',
			'selector' => '{{WRAPPER}} .iconpop-label',
		] );

		$this->end_controls_section();

		/* ── STYLE: Popup ── */
		$this->start_controls_section( 'style_popup', [
			'label' => esc_html__( 'Popup', 'iconpop-elementor' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		] );

		/*
		 * Popup lives at <body> level (JS appends it there so position:fixed
		 * works regardless of any transform/overflow on Elementor wrappers).
		 * These selectors are intentionally global — no {{WRAPPER}} prefix.
		 */
		$this->add_control( 'popup_bg', [
			'label'     => esc_html__( 'Background', 'iconpop-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#ffffff',
			'selectors' => [ '.iconpop-modal-panel' => 'background-color: {{VALUE}};' ],
		] );

		$this->add_control( 'popup_accent', [
			'label'     => esc_html__( 'Accent / Top Border Color', 'iconpop-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#0055b3',
			'selectors' => [ '.iconpop-modal-panel' => 'border-top-color: {{VALUE}};' ],
		] );

		$this->add_control( 'popup_heading_color', [
			'label'     => esc_html__( 'Heading Color', 'iconpop-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#111111',
			'selectors' => [ '.iconpop-modal-panel .iconpop-popup-heading' => 'color: {{VALUE}};' ],
		] );

		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name'     => 'popup_heading_typo',
			'selector' => '.iconpop-modal-panel .iconpop-popup-heading',
		] );

		$this->add_control( 'popup_text_color', [
			'label'     => esc_html__( 'Body Text Color', 'iconpop-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#444444',
			'selectors' => [ '.iconpop-modal-panel .iconpop-popup-body' => 'color: {{VALUE}};' ],
		] );

		$this->add_control( 'popup_btn_bg', [
			'label'     => esc_html__( 'Button Background', 'iconpop-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#0055b3',
			'selectors' => [ '.iconpop-modal-panel .iconpop-popup-btn' => 'background-color: {{VALUE}};' ],
		] );

		$this->add_control( 'popup_btn_color', [
			'label'     => esc_html__( 'Button Text Color', 'iconpop-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#ffffff',
			'selectors' => [ '.iconpop-modal-panel .iconpop-popup-btn' => 'color: {{VALUE}};' ],
		] );

		$this->add_group_control( Group_Control_Box_Shadow::get_type(), [
			'name'     => 'popup_shadow',
			'selector' => '.iconpop-modal-panel',
		] );

		$this->end_controls_section();
	}

	/* ── Default repeater items ──────────────────────────────────── */

	private function default_items() {
		$labels = [
			'Service 1', 'Service 2', 'Service 3', 'Service 4',
			'Service 5', 'Service 6', 'Service 7', 'Service 8',
			'Service 9', 'Service 10', 'Service 11', 'Service 12',
		];
		$items = [];
		foreach ( $labels as $label ) {
			$items[] = [
				'item_label'       => $label,
				'item_icon_default'=> [ 'url' => '' ],
				'item_icon_hover'  => [ 'url' => '' ],
				'popup_heading'    => $label,
				'popup_content'    => '<p>Add your description here. Use the editor to insert headings, images, text and buttons.</p>',
				'popup_btn_text'   => 'Learn More',
				'popup_btn_url'    => [ 'url' => '#' ],
			];
		}
		return $items;
	}

	/* ── Render ──────────────────────────────────────────────────── */

	protected function render() {
		$s       = $this->get_settings_for_display();
		$items   = $s['icons'] ?? [];
		$bg_url  = $s['bg_image']['url'] ?? '';
		$bg_pos  = esc_attr( $s['bg_position'] ?? 'center center' );
		$bg_size = esc_attr( $s['bg_size'] ?? 'cover' );

		$section_style = '';
		if ( $bg_url ) {
			$section_style = sprintf(
				'background-image:url(%s);background-position:%s;background-size:%s;background-repeat:no-repeat;',
				esc_url( $bg_url ),
				$bg_pos,
				$bg_size
			);
		}
		?>
		<div class="iconpop-section" style="<?php echo $section_style; ?>">

			<div class="iconpop-bg-overlay"></div>

			<div class="iconpop-inner">
				<div class="iconpop-layout">
					<?php foreach ( $items as $index => $item ) :
						$default_img = $item['item_icon_default']['url'] ?? '';
						$hover_img   = ! empty( $item['item_icon_hover']['url'] ) ? $item['item_icon_hover']['url'] : $default_img;
					?>
					<div class="iconpop-item iconpop-item-<?php echo ( $index + 1 ); ?> elementor-repeater-item-<?php echo esc_attr( $item['_id'] ); ?>"
					     data-idx="<?php echo (int) $index; ?>"
					     role="button"
					     tabindex="0"
					     aria-haspopup="dialog">

						<div class="iconpop-icon-wrap">
							<?php if ( $default_img ) : ?>
								<img class="iconpop-icon-img iconpop-icon-default"
								     src="<?php echo esc_url( $default_img ); ?>"
								     alt="<?php echo esc_attr( $item['item_label'] ); ?>">
								<img class="iconpop-icon-img iconpop-icon-hover"
								     src="<?php echo esc_url( $hover_img ); ?>"
								     alt="<?php echo esc_attr( $item['item_label'] ); ?>">
							<?php else : ?>
								<div class="iconpop-icon-placeholder iconpop-icon-default"></div>
								<div class="iconpop-icon-placeholder iconpop-icon-hover"></div>
							<?php endif; ?>
						</div>

						<?php if ( ! empty( $item['item_label'] ) ) : ?>
						<span class="iconpop-label"><?php echo esc_html( $item['item_label'] ); ?></span>
						<?php endif; ?>

					</div>
					<?php endforeach; ?>
				</div>
			</div>

			<?php
			/* Popup content stores — hidden, one per item, scoped inside widget */
			foreach ( $items as $index => $item ) :
				$btn_text   = trim( $item['popup_btn_text'] ?? '' );
				$btn_url    = $item['popup_btn_url']['url'] ?? '';
				$btn_target = ( $item['popup_btn_url']['is_external'] ?? '' ) ? '_blank' : '_self';
				$btn_rel    = ( $item['popup_btn_url']['nofollow']    ?? '' ) ? ' rel="nofollow"' : '';
			?>
			<div class="iconpop-popup-store" data-idx="<?php echo (int) $index; ?>" style="display:none;" aria-hidden="true">
				<?php if ( ! empty( $item['popup_heading'] ) ) : ?>
				<h3 class="iconpop-popup-heading"><?php echo esc_html( $item['popup_heading'] ); ?></h3>
				<?php endif; ?>
				<div class="iconpop-popup-body">
					<?php echo wp_kses_post( $item['popup_content'] ); ?>
				</div>
				<?php if ( $btn_text && $btn_url ) : ?>
				<a class="iconpop-popup-btn"
				   href="<?php echo esc_url( $btn_url ); ?>"
				   target="<?php echo esc_attr( $btn_target ); ?>"
				   <?php echo $btn_rel; ?>>
					<?php echo esc_html( $btn_text ); ?>
				</a>
				<?php endif; ?>
			</div>
			<?php endforeach; ?>

		</div><!-- .iconpop-section -->
		<?php
	}

	protected function content_template() {}
}
