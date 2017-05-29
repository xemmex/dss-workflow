<html>
<head>
    <meta charset="UTF-8">
<meta http-equiv="Content-type" content="text/html; charset=UTF-8">
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <title>Workflow</title>

</head>

<body>

    <div class="container">
        <div class="table-responsive">
    

<?php
    require_once 'conexion.php';
    $id_instancia = intval($_GET['q']);
	//datos de la creacion
	$query="select *
	from instancia
	where id_instancia='$id_instancia'";
	$result= mysqli_query($link,$query);
	$row=mysqli_fetch_assoc($result);
	echo '<h3>'.$row['titulo'].'</h3>';
	echo '<p>Descripcion: '.$row['descripcion'].'</p>';
	echo '<p>Iniciado por: '.$row['id_usuario'].', Fecha: '.$row['fecha_inicio'].'</p>';
	
	//historial de transiciones
	$query="select id_usuario,descripcion,fecha,nombre,nombre_estado_asociado,nombre_estado_siguiente
	from proceso
	inner join (select transicion.id_transicion,transicion.nombre,transicion.estado_asociado,transicion.estado_siguiente from transicion) as transi
	on proceso.id_transicion=transi.id_transicion
	inner join (select estado.id_estado, estado.nombre as nombre_estado_asociado from estado) as esta
	on transi.estado_asociado=esta.id_estado
	inner join (select estado.id_estado, estado.nombre as nombre_estado_siguiente from estado) as est
	on transi.estado_siguiente=est.id_estado
	where proceso.id_instancia='$id_instancia'
	order by id_proceso DESC";
	$result=mysqli_query($link,$query);
	if (mysqli_num_rows($result)>0)
	{
        echo '<table class="table table-striped table-bordered">';
        echo '<tr>';
        echo '<th class="table-header">Estados</th>';
        echo '<th class="table-header">Transición</th>';
        echo '<th class="table-header">Descripción</th>';
        echo '<th class="table-header">Usuario</th>';
        echo '<th class="table-header">Fecha</th>';
        echo '</tr>';
		echo '<p><strong>Historial</strong> </p>';
		while($row=mysqli_fetch_assoc($result)){
            echo '<tr class="table-row">';
			echo '<td>'.$row['nombre_estado_asociado'].' -----> '.$row['nombre_estado_siguiente'].' </td>';
			echo '<td>'.$row['nombre'].' </td>';
			echo '<td>'.$row['descripcion'].' </td>';
			echo '<td>'.$row['id_usuario'].'</td>';
            echo '<td>'.$row['fecha'].'</td>';
			echo '</tr>';
		}
        
	}
	echo '</table>';
	mysqli_close($link);
?>
        </div>
    </div>
</body>
</html>