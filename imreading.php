<?php
/*
 Plugin Name: Imreading
 Plugin URI: http://blog.biblioeteca.com/widgets-plugins-y-demas/imreading-widget/
 Description: Muestra los libros está leyendo actualmente un usuario biblioEteca
 Author: José Antonio Espinosa
 Version: 1.5
 Author URI: http://www.biblioeteca.com/
 */


error_reporting(E_ALL);
add_action("widgets_init", array('Imreading', 'register'));
register_activation_hook( __FILE__, array('Imreading', 'activate'));
register_deactivation_hook( __FILE__, array('Imreading', 'deactivate'));

class Imreading {


	function activate(){
		$data = array( 'Usuario' => 'usuarioBiblioEteca','Titulo' => 'Estoy leyendo','Noleo' => 'Ahora no estoy leyendo nada');
		if ( ! get_option('configuracion')){
			add_option('configuracion' , $data);
		} else {
			update_option('configuracion' , $data);
		}
	}
	function deactivate(){
		delete_option('configuracion');
	}

	function control(){
		$data = get_option('configuracion');
		?>

<p><label>Usuario biblioEteca<input name="configuracion_option1"
	type="text" value="<?php echo $data['Usuario']; ?>" /></label></p>
<p><label>Titulo/Comentario<input name="configuracion_option2"
	type="text" value="<?php echo $data['Titulo']; ?>" /></label></p>
<p><label>Si no leo<input name="configuracion_option3"
	type="text" value="<?php echo $data['Noleo']; ?>" /></label></p>

		<?php
		if (isset($_POST['configuracion_option1'])){
			$data['Usuario'] = attribute_escape($_POST['configuracion_option1']);
			$data['Titulo'] = attribute_escape($_POST['configuracion_option2']);
			$data['Noleo'] = attribute_escape($_POST['configuracion_option3']);

			update_option('configuracion', $data);
		}
	}



	function widget($args){
		extract($args);
		
		$opciones     = get_option( "configuracion" );

		//con un usuario dado buscamos en su página de "leyendo"
		//el primer link será el del libro que está leyendo actualmente

		//configurable
		$userBiblioeteca=$opciones['Usuario'];
		//configurable
		$server="http://www.biblioeteca.com";
		//configurable
		$comment=$opciones['Titulo'];
		// configurable
		$noleo=$opciones['Noleo'];

		$existe=false;
        $url='/biblioeteca.web/widgets/imreading/';

        $fp=@fopen($server.$url.$userBiblioeteca,"r");
        if($fp){
           //Acciones a realizar si existe
           $existe= true;
        }else{
            //Acciones a realizar en caso de que no exista
           	$existe= false;
	   		@fclose($fp);
        }

	$noleo=false;
        if ($existe){
		
	$source = '';
	while (!@feof($fp)) {
	  $source .= @fread($fp, 8192);
	}
	@fclose($handle);
	echo $before_widget;
	echo $before_title.$comment.$after_title;
			if (strpos($source,"img")>0)
			{
				echo $source;
			}
			else {
				$noleo=true;
        	}
        }
        
		if (!$existe)
		{
				
		        echo "<br/>";
		        echo "Problemas de conectividad con BiblioEteca";
		        echo "<br/>";
		        echo "<a href='http://www.biblioeteca.com'>www.biblioEteca.com</a>";
		}

        if ($noleo)
        {
	        echo "<br/>";
	        echo $noleo;
	        echo "<br/>";
	        echo "<a href='http://www.biblioeteca.com'>www.biblioEteca.com</a>";
        }
        
        echo $after_widget;
	}

	function register(){
		wp_register_sidebar_widget("Imreading","Que estoy leyendo - Imreading", array('Imreading', 'widget'));
		wp_register_widget_control("Imreading","Que estoy leyendo - Imreading", array('Imreading', 'control'));
	}
}
?>
