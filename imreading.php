<?php
/*
 Plugin Name: Imreading
 Plugin URI: http://blog.biblioeteca.com/widgets-plugins-y-demas/imreading-widget/
 Description: Muestra que libro está leyendo actualmente un usuario biblioEteca
 Author: Ramón López
 Version: 1.3
 Author URI: http://www.biblioeteca.com/
 */


error_reporting(E_ALL);
add_action("widgets_init", array('Imreading', 'register'));
register_activation_hook( __FILE__, array('Imreading', 'activate'));
register_deactivation_hook( __FILE__, array('Imreading', 'deactivate'));

class Imreading {


	function activate(){
		$data = array( 'Usuario' => 'usuarioBiblioEteca','Titulo' => 'Estoy leyendo');
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

		<?php
		if (isset($_POST['configuracion_option1'])){
			$data['Usuario'] = attribute_escape($_POST['configuracion_option1']);
			$data['Titulo'] = attribute_escape($_POST['configuracion_option2']);

			update_option('configuracion', $data);
		}
	}



	function widget($args){
		$opciones     = get_option( "configuracion" );

		//con un usuario dado buscamos en su página de "leyendo"
		//el primer link será el del libro que está leyendo actualmente

		//configurable
		$userBiblioeteca=$opciones['Usuario'];
		//configurable
		$server="http://www.biblioeteca.com";
		//configurable
		$comment=$opciones['Titulo'];

		$existe=false;
        $url='/biblioeteca.web/libros/leyendo/';

        $fp=@fopen($server.$url.$userBiblioeteca,"r");
        if($fp){
           //Acciones a realizar si existe
           $existe= true;
        }else{
            //Acciones a realizar en caso de que no exista
           $existe= false;
        }
        @fclose($fp);


		$noleo=false;
        if ($existe){
		

			$source = file_get_contents($server.$url.$userBiblioeteca)or die('se ha producido un error');
			if (!strpos($source,"No tiene libros"))
			{
				$libros = stristr($source,"<div id=\"biblioeteca\">");
				$linkLibro = stristr($libros,"<a href=\"");
				$linkLibro = substr($linkLibro,9,stripos($linkLibro,"\">")-9);
				$linkLibro=$server.$linkLibro;
		
				//cogemos datos como la imagen a mostrar en nuestro widget
				$otherSource = file_get_contents($linkLibro)or die('se ha producido un error');
				$imagenes = stristr($otherSource,"<div id=\"libro_portada\">");
				$linkImagen = stristr($imagenes,"<img src=\"");
				$linkImagen = substr($linkImagen,10,stripos($linkImagen,"\" class=\"portada\"")-10);
		
				$titulo=stristr($otherSource,"<title>");
				$titulo=substr($titulo,7,stripos($titulo,"- BiblioEteca")-7);
		
				echo "<li class='widget-container widget_text sidebox'>";
				echo "<h3 class='widget-title'>".$comment.":</h3>";
				echo "<br/>";
				echo "<a href ='".$linkLibro."'>".$titulo."</a>";
				echo "<br/>";
				echo "<a href ='".$linkLibro."'><img src='".$linkImagen."'/></a>";
				echo "<br/>";
				echo "<a href='http://www.biblioeteca.com'>www.biblioEteca.com</a>";
				echo "</li>";
			}
			else {
				$noleo=true;
        	}
        }
        if (!$existe || $noleo)
        
        {
	        echo "<li class='widget-container widget_text sidebox'>";
			echo "<h3 class='widget-title'>".$comment.":</h3>";
	        echo "<br/>";
	        echo "En este momentno no estoy leyendo nada.";
	        echo "<br/>";
	        echo "<a href='http://www.biblioeteca.com'>www.biblioEteca.com</a>";
	        echo "</li>";
        }
	}

	function register(){
		wp_register_sidebar_widget("Imreading","Que estoy leyendo - Imreading", array('Imreading', 'widget'));
		wp_register_widget_control("Imreading","Que estoy leyendo - Imreading", array('Imreading', 'control'));
	}
}
?>
