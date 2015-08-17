<?php
/**
 * Plugin Name: More Post
 * Plugin URI: https://github.com/lucasmiranda5/more-posts-plugin
 * Description: Este plugin permite você utilizar um widget com o Posts relacionados de qualquer post type em single ou archive.
 * Author: Lucas Miranda
 * Author URI: http://lucasmiranda.com.br/
 * Version: 1.0.0
 * License: GPLv2 or later
 * Text Domain: more-posts
 * Domain Path: languages/
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'WC_moreposts' ) ) :

class WC_moreposts extends WP_Widget {

	/**
	 * Registra o widget no WordPress WordPress.
	 */
	public function __construct() {
		parent::__construct(
			'wc_moreposts',
			__( 'More Posts', 'more-posts' ),
			array( 'description' => __( 'This widget includes more post in your blog', 'more-posts' ), )
		);
	}

	/**
	 * Formulário do widget no backend do WordPress.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param  array $instance Dados salvos anteriormente no banco de dados.
	 *
	 * @return string
	 */
	public function form( $instance ) {
		$prefixo         = isset( $instance['prefixo'] ) ? $instance['prefixo'] : '';
		$conteudo_atual         = isset( $instance['conteudo_atual'] ) ? $instance['conteudo_atual'] : '';
		$titulo         = isset( $instance['titulo'] ) ? $instance['titulo'] : '';		

		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'prefixo' ); ?>">
				<?php _e( 'Prefixo:', 'conteudo_atual' ); ?>
				<input id="<?php echo $this->get_field_id( 'prefixo' ); ?>" class="widefat" name="<?php echo $this->get_field_name( 'prefixo' ); ?>" type="text" value="<?php echo esc_attr( $prefixo ); ?>" />
			</label>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'conteudo_atual' ); ?>">
				<?php _e( 'Mostrar Post Atual:', 'more-posts' ); ?>
				<select id="<?php echo $this->get_field_id( 'conteudo_atual' ); ?>" class="widefat" name="<?php echo $this->get_field_name( 'conteudo_atual' ); ?>">
					<option value="sim" <?php selected( 'sim', $conteudo_atual, true ); ?>><?php _e( 'Sim','more-posts' ); ?></option>
					<option value="nao" <?php selected( 'nao', $conteudo_atual, true ); ?>><?php _e( 'Não','more-posts' ); ?></option>
				</select>
			</label>
		</p>
		
		<p>
			<label for="<?php echo $this->get_field_id( 'titulo' ); ?>">
				<?php _e( 'Tipo', 'more-posts' ); ?>
				<select id="<?php echo $this->get_field_id( 'titulo' ); ?>" class="widefat" name="<?php echo $this->get_field_name( 'titulo' ); ?>">
					<option value="ti" <?php selected( 'ti', $titulo, true ); ?>><?php _e( 'Imagem + Titulo','more-posts' ); ?></option>
					<option value="i" <?php selected( 'i', $titulo, true ); ?>><?php _e( 'Imagem','more-posts' ); ?></option>
					<option value="t" <?php selected( 't', $titulo, true ); ?>><?php _e( 'Titulo','more-posts' ); ?></option>
				</select>
			</label>
		</p>
		
		<?php
	}

	/**
	 * Escapa e limpa dados do formulário antes de salvar.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param  array $new_instance Novos valores enviados.
	 * @param  array $old_instance Valores salvos anteriormente no banco de dados.
	 *
	 * @return array               Dados limpos e prontos para serem salvos.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['conteudo_atual']         = ( ! empty( $new_instance['conteudo_atual'] ) ) ? sanitize_text_field( $new_instance['conteudo_atual'] ) : '';
		$instance['prefixo']         = ( ! empty( $new_instance['prefixo'] ) ) ? sanitize_text_field( $new_instance['prefixo'] ) : '';
		$instance['titulo']         = ( ! empty( $new_instance['titulo'] ) ) ? sanitize_text_field( $new_instance['titulo'] ) : '';
		

		return $instance;
	}

	/**
	 * Exibe o conteúdo do widget.
	 *
	 * @param  array  $args      Argumentos do Widget (título e HTML que será exibido antes e depois do widget e do título).
	 * @param  array  $instance  Opções do widget.
	 *
	 * @return string
	 */
	public function widget( $args, $instance ) {
		if(is_single() or is_post_type_archive()){
			$title = apply_filters( 'widget_title', $instance['prefixo'] );
				
				
				$tipo = get_post_type( get_the_ID() );
				if($instance['conteudo_atual'] == 'nao' and is_single())
					$atual = array(get_the_ID());
				$post_type =  get_post_type_object($tipo);
				
				$title = $title." ".$post_type->labels->name;
				if ( ! empty( $title ) ) {
				echo $args['before_title'] . $title . $args['after_title'];
				}
				$saida = '';
				$argss = array( 'post_type' => $tipo,
						'posts_per_page' => 4,
						'post__not_in' => $atual);
		
						$query = get_posts($argss);
						if ( $query ) {
							$x = 1;
							foreach($query as $post){
								if($x == 1)
									
								print "<a href='".get_permalink($post->ID)."' title='".$post->post_title."'>";
								
								if ( get_the_post_thumbnail($post->ID) ) { 
									$img = get_the_post_thumbnail($post->ID,'miniaturas');
								}else{
									$img = '<img src="'.plugin_dir_url( __FILE__ ).'imagens/sem-foto.png" title="'.$post->post_title.'" alt="'.$post->post_title.'">';
								}
								
								if( $instance['titulo'] == 't') 
									print'<p>'.$post->post_title.'</p>';
								elseif( $instance['titulo'] == 'i') 
									print $img."<hr>";
								else
									print $img.'<p align="center">'.$post->post_title.'</p>'."<hr>";
								print '</a>';
								
								if($x == 4){
									
									$x = 1;
								}else
									$x++;
							}
						}
						print'</div>';
			
		
		
		
		}
		
		
		echo $args['before_widget'];
		

		

		echo $args['after_widget'];
	}
}

/**
 * Registra o Widget no WordPress.
 */
function wc_morepost_widget_register() {
	register_widget( 'WC_moreposts' );
}

add_action( 'widgets_init', 'wc_morepost_widget_register' );

/**
 * Carrega o textdomain do plugin.
 *
 * Documentação: https://codex.wordpress.org/Function_Reference/load_plugin_textdomain
 */ 
function wc_morepost_widget_load_textdomain() {
	load_plugin_textdomain( 'more-posts', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}

add_action( 'plugins_loaded', 'wc_morepost_widget_load_textdomain' );

endif;
