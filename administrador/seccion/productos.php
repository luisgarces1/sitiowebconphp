<?php include('../template/cabecera.php'); ?>

<?php

$txtID = (isset($_POST['txtID'])) ? $_POST['txtID'] : "";
$txtNombre = (isset($_POST['txtNombre'])) ? $_POST['txtNombre'] : "";
$txtImagen = (isset($_FILES['txtImagen']['name'])) ? $_FILES['txtImagen']['name'] : "";
$accion = (isset($_POST['accion'])) ? $_POST['accion'] : "";

include('../config/bd.php');


switch ($accion) {

    case "Agregar":
        /* ejemplo de un llamado de la base de datos
        $sentenciaSQL= $conexion->prepare ("INSERT INTO `libros` (`ID`, `Nombre`, `Imagen`) VALUES (NULL, 'libro Java', 'imagen1.jpg');"); */

        /* pedir ingresar a la base de datos el nombre y la imagen*/
        $sentenciaSQL = $conexion->prepare("INSERT INTO libros ( Nombre,Imagen) VALUES (:Nombre,:Imagen);");

        /*genera que los datos que se aputen en la casilla nombre vallan a la base de datos*/
        $sentenciaSQL->bindParam(':Nombre', $txtNombre);

        /*crear una fecha para luego adjuntarla a la imagen para que no se repita*/
        $fecha = new DateTime();

        /*crear un nuevo archivo renombrado que junte la fecha con el nombre de la foto*/
        $nombreArchivo = ($txtImagen != "") ? $fecha->getTimestamp() . "_" . $_FILES["txtImagen"]["name"] : "imagen.png";

        /*enuncia que hay un archivo temporal en la casilla de imagen para luego decirle que si hay un archivo en ese archivo lo suba a la carpeta*/
        $tmpImagen = $_FILES["txtImagen"]["tmp_name"];

        /* enunciar como se crea la carpeta base y se como se llamara la carpeta nueva y va a llamarse igual a la imagen*/
        /*$nombre_carpeta = "../../img/libros ingresados/". $nombreArchivo. "";
        
        /* creacion de nueva carperta dentro de la carpeta base*/
        /*if (!is_dir($nombre_carpeta)) {
            if (!mkdir($nombre_carpeta, 0700, true)) {
                die('Fallo al crear las carpetas...');
            }
        }*/
        /*enviar archivo renombrado a carpeta recien creada*/
        /*if($tmpImagen!=""){
            move_uploaded_file($tmpImagen,"$nombre_carpeta/".$nombreArchivo);
        }*/


        /*enviar archivo a la carpeta img es opcion inicial*/
        if ($tmpImagen != "") {
            move_uploaded_file($tmpImagen, "../../img/" . $nombreArchivo);
        }

        /*genera que los datos que se aputen en la casilla imagen vallan a la base de datos*/
        $sentenciaSQL->bindParam(':Imagen', $nombreArchivo);

        /* ejecuta todo el caso anterior de agregar*/
        $sentenciaSQL->execute();

        /* redirecciona a formulario vacio*/
        header("location:productos.php");
        /*cancela el caso*/
        break;

    case "Modificar":
        $sentenciaSQL = $conexion->prepare("UPDATE libros SET Nombre=:Nombre WHERE ID=:ID");
        $sentenciaSQL->bindParam(':Nombre', $txtNombre);
        $sentenciaSQL->bindParam(':ID', $txtID);
        $sentenciaSQL->execute();

        if ($txtImagen != "") {

            /*crear una fecha para luego adjuntarla a la imagen para que no se repita*/
            $fecha = new DateTime();
            /*crear un nuevo archivo renombrado que junte la fecha con el nombre de la foto*/
            $nombreArchivo = ($txtImagen != "") ? $fecha->getTimestamp() . "_" . $_FILES["txtImagen"]["name"] : "imagen.png";
            /*enuncia que hay un archivo temporal en la casilla de imagen para luego decirle que si hay un archivo en ese archivo lo suba a la carpeta*/
            $tmpImagen = $_FILES["txtImagen"]["tmp_name"];
            /*enviar archivo a la carpeta img es opcion inicial*/
            move_uploaded_file($tmpImagen, "../../img/" . $nombreArchivo);

            /*borrar la imagen en el archivo interno*/
            $sentenciaSQL = $conexion->prepare("SELECT Imagen FROM libros WHERE ID=:ID");
            $sentenciaSQL->bindParam(':ID', $txtID);
            $sentenciaSQL->execute();
            $Libro = $sentenciaSQL->fetch(PDO::FETCH_LAZY);

            if (
                isset($Libro["Imagen"]) && ($Libro["Imagen"] != "imagen.png")
            ) {

                if (file_exists("../../img/" . $Libro["Imagen"])) {

                    unlink("../../img/" . $Libro["Imagen"]);
                }
            }



            $sentenciaSQL = $conexion->prepare("UPDATE libros SET Imagen=:Imagen WHERE ID=:ID");
            $sentenciaSQL->bindParam(':Imagen', $nombreArchivo);
            $sentenciaSQL->bindParam(':ID', $txtID);
            $sentenciaSQL->execute();
        }

        /* redirecciona a formulario vacio*/
        header("location:productos.php");

        break;

    case "Cancelar":

        /* redirecciona a formulario vacio*/
        header("location:productos.php");
        break;

    case "Seleccionar":
        $sentenciaSQL = $conexion->prepare("SELECT *FROM libros WHERE ID=:ID");
        $sentenciaSQL->bindParam(':ID', $txtID);
        $sentenciaSQL->execute();
        $Libro = $sentenciaSQL->fetch(PDO::FETCH_LAZY);


        $txtNombre = $Libro['Nombre'];
        $txtImagen = $Libro['Imagen'];

        break;

    case "Borrar":
        /*borrar la imagen en el archivo interno*/
        $sentenciaSQL = $conexion->prepare("SELECT Imagen FROM libros WHERE ID=:ID");
        $sentenciaSQL->bindParam(':ID', $txtID);
        $sentenciaSQL->execute();
        $Libro = $sentenciaSQL->fetch(PDO::FETCH_LAZY);

        if (isset($Libro["Imagen"]) && ($Libro["Imagen"] != "imagen.png")) {

            if (file_exists("../../img/" . $Libro["Imagen"])) {

                unlink("../../img/" . $Libro["Imagen"]);
            }
        }
        /* borrar archivo completo en la base de datos*/
        $sentenciaSQL = $conexion->prepare("DELETE FROM libros WHERE ID=:ID");
        $sentenciaSQL->bindParam(':ID', $txtID);
        $sentenciaSQL->execute();

        /* redirecciona a formulario vacio*/
        header("location:productos.php");

        break;
}

$sentenciaSQL = $conexion->prepare("SELECT *FROM libros");
$sentenciaSQL->execute();
$listaLibros = $sentenciaSQL->fetchAll(PDO::FETCH_ASSOC);


?>


<div class="col-md-5">

    <div class="card">

        <div class="card-header">
            Datos de Libro
        </div>

        <div class="card-body">

            <form method="POST" enctype="multipart/form-data">

                <div class="form-group">
                    <label for="txtID">ID:</label>
                    <input type="tex" required readonly class="form-control" value="<?php echo $txtID; ?>" name="txtID" id="txtID" placeholder="ID">

                </div>

                <div class="form-group">
                    <label for="txtName">Nombre:</label>
                    <input type="tex" required class="form-control" value="<?php echo $txtNombre; ?>" name=" txtNombre" id="txtNombre" placeholder="Nombre">

                </div>

                <div class="form-group">
                    <label for="txtImagen">Imagen:</label>

                    <br>

                    <?php if ($txtImagen != "") {  ?>

                        <img class="img-thumbnail rounded" src="../../img/<?php echo $txtImagen ?>" width="50" alt="">

                    <?php  } ?>


                    <input type="file"  class="form-control" name="txtImagen" id="txtImagen" placeholder="Nombre">

                </div>

                <div class="btn-group" role="group" aria-label="">
                    <button type="submit" name="accion" <?php echo ($accion == "Seleccionar") ? "disabled" : "" ?> value="Agregar" class="btn btn-success">Agregrar</button>
                    <button type="submit" name="accion" <?php echo ($accion != "Seleccionar") ? "disabled" : "" ?> value="Modificar" class="btn btn-warning">Modificar</button>
                    <button type="submit" name="accion" <?php echo ($accion != "Seleccionar") ? "disabled" : "" ?> value="Cancelar" class="btn btn-info">Cancelar</button>
                </div>



            </form>
        </div>

    </div>
</div>


<div class="col-md-7">


    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Imagen</th>
                <th>Acciones</th>
            </tr>
        </thead>

        <tbody>

            <?php foreach ($listaLibros as $libro) { ?>
                <tr>
                    <td><?php echo $libro['ID']; ?></td>
                    <td><?php echo $libro['Nombre']; ?></td>
                    <td>

                        <img class="img-thumbnail rounded" src="../../img/<?php echo $libro['Imagen']; ?>" width="50" alt="">

                    </td>

                    <td>
                       

                        <form method="post">

                            <input type="hidden" name="txtID" id="txtID" value="<?php echo $libro['ID']; ?>" />

                            <input type="submit" name="accion" value="Seleccionar" class="btn btn-primary" />


                            <input type="submit" name="accion" value="Borrar" class="btn btn-danger" />

                        </form>

                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>


</div>


<?php include('../template/pie.php'); ?>