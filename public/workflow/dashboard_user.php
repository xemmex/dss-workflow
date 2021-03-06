<?php
//utilizar session para mantener logueado al usuario, mantener como variables globales el id de usuario y su tipo de usuario
require_once 'conexion.php';

session_start();

if (empty($_SESSION['id_usuario'])) {
    echo "<script>alert('Debes iniciar sesion');</script>";
    header("refresh:0; url=index.php");
    die();
} else {
    $id_tipo = $_SESSION['id_tipo'];
    $id_usuario = $_SESSION['id_usuario'];
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-type" content="text/html; charset=UTF-8">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <title>Panel -Sistema web para el control de flujo de trabajo</title>
</head>
<body>
<div class="container">
    <div class="card card-container">
        <div class="center-align">
            <h3 class="text-uppercase">Sistema web para el control de flujo de trabajo</h3>
            <h4 class="text-uppercase">Flujos de trabajo en ejecución</h4>
        </div>
        <div>
            <h4>Bienvenido <span><?php echo $id_usuario ?></span></h4>
        </div>
        <div class="btn-group">
            <a class="btn btn-default" href="logout.php">Cerrar Sesion</a>
        </div>
        <div class="btn-group">
            <a class="btn btn-default" href="#" id="nueva-instancia">Crear nueva instancia</a>
        </div>

        <div class="well">
            <div class="table-responsive">
                <table class="table table-bordered table-responsive">
                    <thead>
                    <tr>
                        <th>Número</th>
                        <th>Título</th>
                        <th>Fecha Inicio</th>
                        <th>Estado</th>
                    </tr>
                    </thead>
                    <?php
                    //query que busca todos los workflow que esten instanciados creados por el usuario sin ningun estado
                    $query = "select *
	from instancia	
	where instancia.id_usuario='$id_usuario'
	and instancia.id_instancia not in (select proceso.id_instancia from proceso)
	order by fecha_inicio ASC
	";
                    $result = mysqli_query($link, $query);
                    if (mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            echo '<tr>';
                            $id_instancia = $row['id_instancia'];
                            $id_workflow = $row['id_workflow'];
                            echo '<td>' . $id_instancia . '</td>';
                            echo '<td>' . $row['titulo'] . '</td>';
                            echo '<td>' . $row['fecha_inicio'] . '</td>';
                            $query2 = "select estado.id_estado,estado.nombre as estado_nombre
			from estado
			where estado.id_workflow='$id_workflow' and estado.inicial=1";
                            $result2 = mysqli_query($link, $query2);
                            $row2 = mysqli_fetch_assoc($result2);
                            $id_estado = $row2['id_estado'];
                            echo '<td>' . $row2['estado_nombre'] . '</td>';
                            echo '<td><button class="btn btn-success" onclick="cambiar_proceso(' . $id_instancia . ',' . $id_estado . ')">Realizar transiciones</button></td>';
                            echo '</tr>';
                        }
                    }
                    //query que busca todos los workflow que se encuentren en un estado que este asociado al usuario actual
                    $query = "select *
	from instancia
	inner join (select proceso.id_proceso,proceso.id_transicion,proceso.descripcion,proceso.fecha,proceso.id_instancia
	from proceso
	group by proceso.id_instancia DESC) as pro
	on instancia.id_instancia=pro.id_instancia
	inner join transiciones
	on pro.id_transicion=transiciones.id_transicion
	inner join (select estado.id_estado,estado.nombre as estado_nombre,estado.id_tipo from estado) as est
	on transiciones.estado_siguiente=est.id_estado
    inner join (select * from instancia_usuario) as inuser
    on instancia.id_instancia=inuser.id_instancia
	where instancia.fecha_final is NULL
	and (inuser.id_usuario='$id_usuario' or inuser.id_usuario='0') 
    and inuser.id_estado=transiciones.estado_siguiente
    and est.id_tipo='$id_tipo'
    and inuser.realizado=0
    group by instancia.id_instancia
	order by instancia.fecha_inicio ASC
	";
                    $result2 = mysqli_query($link, $query);
                    if (mysqli_num_rows($result2) > 0) {
                        while ($row = mysqli_fetch_assoc($result2)) {
                            echo '<tr>';
                            $id_instancia = $row['id_instancia'];
                            echo '<td>' . $id_instancia . '</td>';
                            echo '<td>' . $row['titulo'] . '</td>';
                            echo '<td>' . $row['fecha_inicio'] . '</td>';
                            $id_estado = $row['id_estado'];
                            echo '<td>' . $row['estado_nombre'] . '</td>';
                            echo '<td><button class="btn btn-success" onclick="cambiar_proceso(' . $id_instancia . ',' . $id_estado . ')">Realizar transiciones</button></td>';
                            echo '</tr>';
                        }
                    }
                    mysqli_close($link);
                    ?>
                </table>
                <?php
                if ((mysqli_num_rows($result) == 0) && (mysqli_num_rows($result2) == 0)):
                    ?>
                    <div class="center-align">
                        <h4>No existen flujos de trabajo en ejecución</h4>
                    </div>

                    <?php
                endif;
                ?>
            </div>

        </div>
    </div>
</div>

<form class="form-signin" id="form-nueva-instancia" method="post" action="nueva_instancia.php">
    <input type="hidden" name="id_tipo" value="<?php echo $id_tipo ?>">
    <input type="hidden" id="id_usuario" name="id_usuario" value="<?php echo $id_usuario ?>">
</form>

<form class="form-signin" name="formulario1" id="formulario1" method="post" action="proceso.php">
    <input type="hidden" id="id_instancia" name="id_instancia">
    <input type="hidden" id="id_estado" name="id_estado">
    <input type="hidden" id="id_usuario" name="id_usuario" value="<?php echo $id_usuario ?>">

</form>

<!-- jQuery -->
<script src="js/jquery-2.1.1.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script>
    function cambiar_proceso(instancia, estado) {
        window.document.getElementById('id_instancia').value = instancia;
        window.document.getElementById('id_estado').value = estado;
        window.document.getElementById('formulario1').submit();
    }

    $("#nueva-instancia").click(function(){
        $("#form-nueva-instancia").submit();
    });
</script>
</body>

</html>