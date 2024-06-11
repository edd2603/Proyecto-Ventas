<?php
require '../Classes/PHPExcel.php';

$host = 'localhost:3307';
$username = 'root';
$password = 'Zaq1xsw2*';
$database = 'ventas';

$mysqli = new mysqli($host, $username, $password, $database);
if ($mysqli->connect_error) {
    die('Error de conexión: ' . $mysqli->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['fecha_inicio'], $_POST['fecha_fin'])) {
    $fechaInicio = $_POST['fecha_inicio'];
    $fechaFin = $_POST['fecha_fin'];

$sql = "SELECT vd.venta_detalle_id, vd.venta_detalle_cantidad, vd.venta_detalle_descripcion, vd.venta_detalle_total, p.producto_nombre, p.producto_precio_venta, v.venta_codigo, v.venta_fecha, v.venta_hora, p.producto_modelo, p.producto_precio_compra
        FROM venta_detalle AS vd
        INNER JOIN producto AS p ON vd.producto_id = p.producto_id
        INNER JOIN venta AS v ON v.venta_id = vd.venta_detalle_id
        WHERE v.venta_fecha BETWEEN ? AND ?";

    $stmt = $mysqli->prepare($sql);
    if (!$stmt) {
        die('Error preparando la consulta: ' . $mysqli->error);
    }
    $stmt->bind_param("ss", $fechaInicio, $fechaFin);
    $stmt->execute();
    $resultado = $stmt->get_result();

    $objPHPExcel = new PHPExcel();
    $objPHPExcel->getProperties()->setCreator("Edward Ospina")->setDescription("Ventas");
    $objPHPExcel->setActiveSheetIndex(0);
    $objPHPExcel->getActiveSheet()->setTitle("General");

    // Configura estilos y columnas aquí...
    $objDrawing = new PHPExcel_Worksheet_MemoryDrawing();
    /*$objDrawing->setName('Logotipo');
    $objDrawing->setDescription('Logotipo');
    $objDrawing->setImageResource($gdImage);*/
    $objDrawing->setRenderingFunction(PHPExcel_Worksheet_MemoryDrawing::RENDERING_PNG);
    $objDrawing->setMimeType(PHPExcel_Worksheet_MemoryDrawing::MIMETYPE_DEFAULT);
    $objDrawing->setHeight(110);
    $objDrawing->setCoordinates('A1');
    $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());

    $estiloTituloReporte = array(
    'font' => array(
    'name'      => 'Arial',
    'bold'      => true,
    'italic'    => false,
    'strike'    => false,
    'size' =>13
    ),
    'fill' => array(
    'type'  => PHPExcel_Style_Fill::FILL_SOLID
    ),
    'borders' => array(
    'allborders' => array(
    'style' => PHPExcel_Style_Border::BORDER_NONE
    )
    ),
    'alignment' => array(
    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
    )
    );
    
    $estiloTituloColumnas = array(
    'font' => array(
    'name'  => 'Arial',
    'bold'  => true,
    'size' =>10,
    'color' => array(
    'rgb' => 'FFFFFF'
    )
    ),
    'fill' => array(
    'type' => PHPExcel_Style_Fill::FILL_SOLID,
    'color' => array('rgb' => '538DD5')
    ),
    'borders' => array(
    'allborders' => array(
    'style' => PHPExcel_Style_Border::BORDER_THIN
    )
    ),
    'alignment' =>  array(
    'horizontal'=> PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
    'vertical'  => PHPExcel_Style_Alignment::VERTICAL_CENTER
    )
    );
    
    $estiloInformacion = new PHPExcel_Style();
    $estiloInformacion->applyFromArray( array(
    'font' => array(
    'name'  => 'Arial',
    'color' => array(
    'rgb' => '000000'
    )
    ),
    'fill' => array(
    'type'  => PHPExcel_Style_Fill::FILL_SOLID
    ),
    'borders' => array(
    'allborders' => array(
    'style' => PHPExcel_Style_Border::BORDER_THIN
    )
    ),
    'alignment' =>  array(
    'horizontal'=> PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
    'vertical'  => PHPExcel_Style_Alignment::VERTICAL_CENTER
    )
    ));

    $bueno = array(
    'font' => array(
    'name'  => 'Arial',
    'bold'  => true,
    'size' =>10,
    'color' => array(
    'rgb' => 'FFFFFF'
    )
    ),
    'fill' => array(
    'type' => PHPExcel_Style_Fill::FILL_SOLID,
    'color' => array('rgb' => 'A3F485')
    ),
    'borders' => array(
    'allborders' => array(
    'style' => PHPExcel_Style_Border::BORDER_THIN
    )
    ),
    'alignment' =>  array(
    'horizontal'=> PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
    'vertical'  => PHPExcel_Style_Alignment::VERTICAL_CENTER
    )
    );
    
    $objConditional1 = new PHPExcel_Style_Conditional();
    $objPHPExcel->getActiveSheet()->getStyle('A1:R1')->applyFromArray($estiloTituloColumnas);
    $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(15);
    $objPHPExcel->getActiveSheet()->setCellValue('A1', 'Tipo Producto');
    $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
    $objPHPExcel->getActiveSheet()->setCellValue('B1', 'Producto');
    $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
    $objPHPExcel->getActiveSheet()->setCellValue('C1', 'Precion Venta');
    $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(10);
    $objPHPExcel->getActiveSheet()->setCellValue('D1', 'Venta total');
    $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(10);
    $objPHPExcel->getActiveSheet()->setCellValue('E1', 'Codigo Venta');
    $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(10);
    $objPHPExcel->getActiveSheet()->setCellValue('F1', 'Fecha');
    $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(10);
    $objPHPExcel->getActiveSheet()->setCellValue('G1', 'Hora');
    $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(10);
    $objPHPExcel->getActiveSheet()->setCellValue('H1', 'Precio Compra');
    $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(10);
    $objPHPExcel->getActiveSheet()->setCellValue('I1', 'Cantidad');
    $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(10);

    $fila = 2;

    while ($row = $resultado->fetch_assoc()) {
        $objPHPExcel->getActiveSheet()->setCellValueExplicit('A'.$fila, $row['producto_modelo'], PHPExcel_Cell_DataType::TYPE_STRING);
        $objPHPExcel->getActiveSheet()->setCellValue('B'.$fila, $row['producto_nombre']);
        $objPHPExcel->getActiveSheet()->setCellValue('C'.$fila, $row['producto_precio_venta']);
        $objPHPExcel->getActiveSheet()->setCellValue('D'.$fila, $row['venta_detalle_total']);
        $objPHPExcel->getActiveSheet()->setCellValue('E'.$fila, $row['venta_codigo']);
        $objPHPExcel->getActiveSheet()->setCellValue('F'.$fila, $row['venta_fecha']);
        $objPHPExcel->getActiveSheet()->setCellValue('G'.$fila, $row['venta_hora']);
        $objPHPExcel->getActiveSheet()->setCellValue('H'.$fila, $row['producto_precio_compra']);
        $objPHPExcel->getActiveSheet()->setCellValue('I'.$fila, (string)$row['venta_detalle_cantidad']);

        // Asegúrate de que todos los valores asignados están correctamente formateados y son del tipo esperado
        $fila++;
    }

    header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
    header('Content-Disposition: attachment;filename="ventas_' . date('Y-m-d') . '.xlsx"');
    header('Cache-Control: max-age=0');

    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    $objWriter->save('php://output');
    exit;
} else {
    echo "Por favor, especifica un rango de fechas válido.";
}
?>
