<?php

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

$base_path=".";                            
$base_auth = "CIRCULATION_AUTH|ADMINISTRATION_AUTH";  
$base_title = "\$msg[7]";
$class_path= "./classes";
$include_path= "./includes";

define('CONVERSION',(1/25.4));//Conversión inch/mm
define('COLS_PER_PAGE', 3); // Nº columnas por página
define('ROWS_PER_PAGE', 8); // Nº filas por página

define('ALIGN', 'C');
define('LABEL_WIDTH', (70.8*CONVERSION)); // en mm
define('LABEL_HEIGHT', (36.5*CONVERSION));  // en mm
define('H_MARGIN', (7*CONVERSION)); // horizontal (izquierda y derecha) margenes de la pagina, en mm
define('V_MARGIN', (12*CONVERSION)); // vertical (top & bottom) margins of page, in mm

function f_rellena_ceros($as_dato) {
	if(strlen($as_dato)>0 && strlen($as_dato)<9){
		for($i=strlen($as_dato); $i<9; $i++)
			$as_dato="0".$as_dato;}
	
	return $as_dato; 
	
}

function str_squeeze($test) {

    return trim(preg_replace( '~ + ~', '', $test));
}

$codigos=$_GET['codigos'];
$per=$_GET['percentaje_combobox'];
switch ($codigos){

case '':{
	echo "<center><form class='form-admin' name='form2' ENCTYPE=\"multipart/form-data\" method='get' action=\"./tejuelo2.php\"\"><b>$msg[tejuelo_categoria]&nbsp;</b><input name='codigos' accept='text/plain' type='text'  size='80'>";
	echo "<center><b>$msg[porcentaje_tejuelo]</b>
	<select name='percentaje_combobox' >
	<option value='1'>100%</option>
	<option value='0.9'>90%</option>
	<option value='0.8'>80%</option>
	<option value='0.7'>70%</option>
	<option value='0.6'>60%</option>
	<option value='0.5'>50%</option>
	</select></center>";
	echo "<input align='center' type='button' name='continuar' value='Continuar' onclick='form.submit()'></form></center>";
break;}

default:{
$matriz=array();
$base_noheader = 1;

$BARCODE_HEIGHT=(54*$per); // en pixels
$BARCODE_WIDTH=(175*$per); // en pixels
$LABEL_WIDTH2=((70.8*CONVERSION)*$per); // en mm
$LABEL_HEIGHT2=((36.5*CONVERSION)*$per);  // en mm
$font_size1=10*$per;
$font_size2=25*$per;

require_once ("$base_path/includes/init.inc.php");
$codigos=str_squeeze($codigos);
$size = count($matriz);

$barcode_h_margin = (($LABEL_WIDTH2-($BARCODE_WIDTH/72))/2); 
$barcode_v_margin = (($LABEL_HEIGHT2-($BARCODE_HEIGHT/72))/2);

ob_start();
$pdf=new FPDF('P','in','A4');

// Metadata
$pdf->SetAuthor('Lliurex');
$pdf->SetTitle('Tejuelo Lliurex');

//Otras características
$pdf->SetDisplayMode('real'); // Mostramos el zoom al 100%

// Añadimos una pagina al documento PDF
$pdf->AddPage();

// Fijamos los márgenes 
$pdf->SetMargins(H_MARGIN, V_MARGIN);

// Manejamos nosotros cuando la pagina debe acabar 
$pdf->SetAutoPageBreak(false);
$pdf->AddFont('barcode', '', "barcode.php");
$y = V_MARGIN; //Esta variable sigue la posición y (vertical)
$x = H_MARGIN; // Nueva fila, reseteamos x-position


$new_row=1;
$new_col=1;

$q = 'SELECT distinct expl_cote, expl_cb,autor.value
FROM exemplaires, notices_fields_global_index,(SELECT  `id_notice` , value
FROM  `notices_fields_global_index` 
WHERE  `code_champ` =27
AND  `code_ss_champ` =1) as autor
WHERE expl_notice = notices_fields_global_index.id_notice and expl_notice=autor.id_notice 
AND code_champ =25
AND notices_fields_global_index.value LIKE \''.$codigos.'\' 
order by autor.value';


$resultData = @pmb_mysql_query($q, $dbh);
if (@pmb_mysql_num_rows($resultData) != 0) {

	//Recuperamos los datos de cada solicitud confirmada
			
	while ($rowData = pmb_mysql_fetch_array($resultData)) {	
				
	//dato cote
				
	$cote=$rowData['expl_cote'];
	if (is_numeric($exe_cote)) {
		if (is_numeric($rowData['expl_cb'])) {
			if ((int) $exe_cote === (int) $rowData['expl_cb']) $cb=(int)$rowData['expl_cb'];
			else continue;
		} else continue;
	} else $cb=$rowData['expl_cb'];

	if ($new_row > 8) // Nueva pagina, reseteamos x-position
	{
		// Creamos una nueva pagina
		$pdf->AddPage();
		$pdf->SetMargins(H_MARGIN, V_MARGIN);
		$pdf->SetAutoPageBreak(false);
		$y = V_MARGIN;
		$x = H_MARGIN;
		$new_row=1;
		$new_col=1;
		} 

	$matriz2=explode(" ",$cote);	
	$pdf->SetFont('Arial','B',$font_size1);
	$pdf->SetY($y+((LABEL_HEIGHT-$LABEL_HEIGHT2)/3));
	$pdf->SetX($x+((LABEL_WIDTH-$LABEL_WIDTH2)/3));
	if (strlen($matriz2[0])>6) $align="";
	else $align="C";
	//Dibuja la linea superior de la caja  correspndiente al 1º parametro
	$pdf->Cell(LABEL_WIDTH/(5+(1-$per)), ($barcode_v_margin*1),$matriz2[0],"LT", 0, 'L');
	//Dibuja el borde vertica de la caja correspondiente al 1º parametro
	$pdf->Cell($LABEL_WIDTH2-(1-(1-$per)), ($barcode_v_margin*1),"","TR", 0,'C');
	$pdf->Ln();
	$pdf->SetX($x+((LABEL_WIDTH-$LABEL_WIDTH2)/3));
	$pdf->Cell(LABEL_WIDTH/(5+(1-$per)), ($barcode_v_margin*1),$matriz2[1],"L", 0,'L');
        $pdf->setFont('barcode',"",$font_size2);
	$pdf->Cell($LABEL_WIDTH2-(1-(1-$per)), ($barcode_v_margin*1),"*".f_rellena_ceros($cb)."*","R", 0,'C');
	$pdf->SetFont('Arial','B',$font_size1);
	$pdf->Ln();
	$pdf->SetX($x+((LABEL_WIDTH-$LABEL_WIDTH2)/3));
	$pdf->Cell(LABEL_WIDTH/(5+(1-$per)), ($barcode_v_margin*1),$matriz2[2],"LB", 0,'L');
	$pdf->Cell($LABEL_WIDTH2-(1-(1-$per)), ($barcode_v_margin*1),f_rellena_ceros($cb),"BR", 0,'C');

	if (($new_col%COLS_PER_PAGE)==0) {
		$x = H_MARGIN;
		$y += LABEL_HEIGHT-0.03;
		$new_row++;
		$new_col=1;
	} else {
		$new_col++;
		$x += LABEL_WIDTH-0.10;
		}


	}
	@pmb_mysql_free_result($resultData);

}

// Desconexión de la Base de Datos
pmb_mysql_close($dbh);

// Limpiar (ELIMINAR) el búfer de salida y Deshabilitar el Almacenamiento en El Mismo -12/02/2015
ob_end_clean();

$pdf->Output('tejuelo_categoria.pdf', 'D'); // Salida es un pdf descargable 

}


}
?>
