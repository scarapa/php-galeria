<?php
ini_set("display_errors", "0");
error_reporting(E_ERROR);

$aux = substr( $_SERVER['REQUEST_URI'], strlen('/'));
if( substr( $aux, -1) == '/'){ $aux=substr( $aux, 0, -1); }
$link =explode( '/', $aux);
	
DEFINE("DOMINIO","FOTOS");
$main = buscarMain($link);
$breadCrumb = buscarBreadCrumb($link);
$diretorio = urldecode($_SERVER['DOCUMENT_ROOT']."fotos/".$main);
$retorno = lerPasta($diretorio);
//echo "<pre>"; print_r($retorno); echo "</pre>";
//die;
if($retorno['arquivo']['imagem']>=300){
	$salto = 50;
}elseif( ($retorno['arquivo']['imagem']>=100) && ($retorno['arquivo']['imagem']<300) ){
	$salto = 25;
}elseif( ($retorno['arquivo']['imagem']>=50) && ($retorno['arquivo']['imagem']<100) ){
	$salto = 5;	
}else{
	$salto = "";
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>GALERIA</title>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">		
		<link rel="stylesheet" href="css/bootstrap-4.1.3.css">
		<link rel="stylesheet" href="css/style.css">

		<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
		<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>

		<script>
		$(document).ready(function() {
			const totalFotos = $("#tela > div").length
			init();
			$("#anterior").click(function(){
				navegar('-1');
			});

			$("#proximo").click(function(){
				navegar('+1');
			});

			$(".salto").click(function(){
				let salto = $(this).attr('rel');
				navegar(salto)
			})

			function init(){
				atualizarDisplay()
			}
			function atualizarDisplay(){
				atualizarDisplayFotoAtual('');
				atualizarDisplayFotoTotal();
			}	
			function atualizarDisplayFotoAtual( indice ){
				if(indice == ''){
					indice = pad(1,totalFotos.toString().length)
				}else{
					indice = pad(indice,totalFotos.toString().length)
				}
				$("#displayFotoAtual").html(indice)
			}
			function atualizarDisplayFotoTotal(){
				$("#displayTotalFotos").html( totalFotos )
			}			
			function buscarIndiceImagemAtiva(){
				return $("#tela > div.ativa").attr('data-indice');
			}
			
			function navegar(ordenacao){
				let indice = parseInt(buscarIndiceImagemAtiva());
				
				desativarIndice(indice)

				novoIndice = parseInt(indice) + parseInt(ordenacao);
				if(novoIndice > totalFotos) {
					novoIndice = 1;
				}else if (novoIndice == 0) {
					novoIndice = totalFotos;
				}
				ativarIndice(novoIndice)
				atualizarDisplayFotoAtual(novoIndice);
			}

			function desativarIndice(indice){
				$("#tela > div[data-indice="+indice+"]").removeClass('ativa')
				$("#tela > div[data-indice="+indice+"]").addClass('desativa')
			}

			function ativarIndice(indice){
				$("#tela > div[data-indice="+indice+"]").removeClass('desativa')
				$("#tela > div[data-indice="+indice+"]").addClass('ativa')
			}

			function pad(num, size) {
				var s = "000000000" + num;
				return s.substr(s.length-size);
			}
		});		
		</script>
	</head>
	<body>

		<div class="text-center">
			<?php
			foreach($breadCrumb AS $item){
				echo " // <a href='?$item[link]'>".$item['title']."</a>";
			}
			?>
		</div>

		<div id="wrapper" class="d-flex justify-content-center row">

				<div id="anterior" class="col-md-1 col-1 text-center d-flex align-items-center"> << </div>

				<div id="tela" class="col-md-8 col-sm-8 col-7">
					<?php 
					foreach($retorno['arquivo']['imagem'] AS $key => $imagem){
						$indice = $key + 1;
					?>
					<div class="item <?=($key==0)?"ativa":"desativa";?> h-100" data-indice="<?php echo $indice;?>"  data-key="<?php echo $key;?>">
						<img src="galeria/<?=$imagem;?>" class="img-fluid h-100 d-inline-block"/>
					</div>
					<?php
					}
					?>						
				</div>

				<div id="proximo" class="col-md-1 col-1 d-flex align-items-center"> >> </div>

			</div>		

			<div id="display" class="d-flex justify-content-center">
				<?php echo ($salto!="")? "<i class='glyphicon glyphicon-fast-backward salto' rel='-$salto'></i> &nbsp;":""; ?>
				<span id="displayFotoAtual">xx</span>&nbsp;
				<?php echo ($salto!="")? "<i class='glyphicon glyphicon-fast-forward salto' rel='+$salto'></i> &nbsp;":""; ?>				
				de&nbsp; <span id="displayTotalFotos">yy</span>

			</div>

		</div>
		

		<div class="row">
			<div class="col-md-3"><a href="?">RAIZ</a></div>
			<?php
			foreach($retorno['pasta'] AS $pasta){
			?>
				<div class="col-md-3">
					<a href="?/galeria<?=$pasta;?>/"><?=$pasta;?>/</a>
				</div>
			<?php
			}
			?>
			</ul>

		</div>
	</body>
<?php	
function buscarMain($link){
	if(in_array("?",$link)){
		$posicao = array_search("?",$link);
		for($c=$posicao+1;$c<count($link);$c++){
			$linkNovo .= $link[$c]."/" ;
		}
	}
	if(empty($linkNovo)) $linkNovo = "galeria";
	return $linkNovo;
}
	
function buscarBreadCrumb($link){
	if(in_array("?",$link)){
		$posicao = array_search("?",$link);
		for($c=$posicao+1;$c<count($link);$c++){
			$linkNovo[$c]['title'] = $link[$c];
			$linkCompleto = $linkCompleto."/".$link[$c];
			$linkNovo[$c]['link'] = $linkCompleto;
		}
	}
	if(empty($linkNovo)) $linkNovo = "galeria";
	return $linkNovo;
}
	
	
function lerPasta($dir){
	$files = scandir($dir);
	$retorno = array();
	foreach($files as $key => $value){
        $path = realpath($dir.DIRECTORY_SEPARATOR.$value);
        if(!is_dir($path)) {
			if(arquivoDetectarTipoImagem($path)){
            	$retorno['arquivo']['imagem'][] = caminhoVirtual($path);
			} elseif(arquivoDetectarTipoVideo($path)) {
            	$retorno['arquivo']['video'][] = $path;				
			}else{
            	$retorno['arquivo']['outros'][] = $path;
			}

        } else if($value != "." && $value != "..") {
            $retorno['pasta'][] = caminhoVirtual($path);
        }
    }
	return $retorno;
}

function caminhoVirtual($caminho){
	$link = explode("/",$caminho);
	//print_r($link);
	$posicao = array_search(DOMINIO,$link);
	for($c=$posicao+1;$c<count($link);$c++){
		$linkNovo .= "/".$link[$c] ;
	}
	return $linkNovo;
}	
	
function arquivoDetectarTipoImagem($caminho){
	$arrayExtensoes = array("jpg","jpeg","png","bmp","gif");
	$flag = false; 
	foreach($arrayExtensoes AS $tipo){
		if (strpos(strtolower($caminho),$tipo) !== false) {
			$flag = true; 
			break;
		}
	}
	return $flag;
}

function arquivoDetectarTipoVideo($caminho){
	$arrayExtensoes = array("mp4","wav","3gp");
	$flag = false; 
	foreach($arrayExtensoes AS $tipo){
		if (strpos(strtolower($caminho),$tipo) !== false) {
			$flag = true; 
			break;
		}
	}
	return $flag;
}		
		
/*
function getDirContents($dir, &$results = array(),&$c=1){
	echo $c++;
	echo "DIR: ".$dir."<br>";
    $files = scandir($dir);

    foreach($files as $key => $value){
        $path = realpath($dir.DIRECTORY_SEPARATOR.$value);
        if(!is_dir($path)) {
            $results[] = $path;
        } else if($value != "." && $value != "..") {
            getDirContents($path, $results,$c);
			
            $results[] = $path;
        }
    }

    return $results;
}
//echo "<pre>"; print_r($_SERVER); echo "</pre>";
//ECHO $_SERVER['DOCUMENT_ROOT']."fotos/";
echo "<pre>"; print_r(getDirContents($_SERVER['DOCUMENT_ROOT']."fotos/"),0); echo "</pre>";
*/
?>
		