<?php
if(!isset($_REQUEST['accio'])){
  /* Página sin parametros */
  ?>
  <h1>Conect4 online</h1>
  <h3>Crear Partida</h3>
  <!-- p1 -->
  <form action="index.php">
    <label for="jugador1">Jugador 1 <small>(Host)</small>:</label>
    <input type="text" name="jugador1">
    <input hidden name="accio" value="crear_partida">
    <input type="submit" value="Crear partida">
  </form>
  <!-- p2 -->
  <form action="index.php">
    <label for="jugador2">Jugador 2 <small>(Guest)</small>:</label>
    <input type="text" name="jugador2">
    <input hidden name="accio" value="buscar_partida">
    <input type="submit" value="Buscar partida">
  </form>
  <?php
}





if($_REQUEST['accio']=="crear_partida"){
  /* Crear la partida en la base de datos */
  $con = mysqli_connect("localhost","root","root","connect4") or exit(mysqli_connect_error());
  echo "Connectió Correcta <br>";
  $sql = "INSERT INTO partides VALUES (null,'".date("Y-m-d")."','".$_REQUEST['jugador1']."',null,1)";
  echo $sql."<br>";
  $result=mysqli_query($con, $sql) or exit(mysqli_error($con));
  $id=mysqli_insert_id($con);
  ?>
  <h3>Partida creada num <?php echo $id ?></h3>
  <h4>Esperant contrincant...</h4>
  <script>
    setTimeout(() => {
      window.location.href = "index.php?contador=1&accio=comprovar_partida&jugador=1&partida=<?php echo $id?>";
    }, 1000);
  </script>
  <?php
}





if($_REQUEST['accio'] == 'comprovar_partida'){
  /* jugador 1 esperando para jugador 2 */
  $contador=$_REQUEST['contador']+1;
  $jugador=$_REQUEST['jugador'];
  $partida=$_REQUEST['partida'];
  ?>
  <h3>Partida creada num <?php echo $partida?></h3>;
  <?php
   $con = mysqli_connect("localhost","root","root","connect4") or exit(mysqli_connect_error());
   $sql = "SELECT * FROM partides WHERE id_partida = $partida";
   $result=mysqli_query($con, $sql) or exit(mysqli_error($con));
   $reg=mysqli_fetch_array($result);
   if($reg['nom_jugador2'] != ''){
     /* jugador 2 encontrado */
     ?>
      <h1>Tenemos rival!</h1>
      <script>
        setTimeout(() => {
          window.location.href = "index.php?accio=moviment_partida&jugador=1&partida=<?php echo $id?>";
        }, 1000);
      </script>
     <?php
   }else{
     ?>
      <h4>Esperant contrincant...</h4>
      <script>
        setTimeout(() => {
          window.location.href = "index.php?contador=1&accio=comprovar_partida&jugador=1&partida=<?php echo $id?>";
        }, 1000);
      </script>
    <?php
   };
}
  





if($_REQUEST['accio'] == 'moviment_partida'){
  $jugador=$_REQUEST['jugador'];
  $partida=$_REQUEST['partida'];
  /* mirar turno */
  $con = mysqli_connect("localhost","root","root","connect4") or exit(mysqli_connect_error());
  $sql = "SELECT * FROM partides WHERE id_partida = $partida";
  $result=mysqli_query($con, $sql) or exit(mysqli_error($con));
  $reg=mysqli_fetch_array($result);

  echo "<h3>TAULELL</h3>";
  pintar_taulell($partida);

  if($reg['torn'] == $jugador){
    /* Turno jugador 1 */ 
    ?>
    <form action="index.php">
      <label for="columna">Columna: </label>
      <input type="number" min="1" max="7" require>
      <input hidden name="jugador" value="<?php echo $jugador; ?>">
      <input hidden name="partida" value="<?php echo $partida; ?>">
      <input type="submit" value="Enviar moviment">
    </form>
    <?php
  }else{
    ?>
      <h4>Esperant moviemnt del jugador <?php echo $jugador == 1 ? "2" : "1";?> </h4>
      <script>
        setTimeout(() => {
          window.location.href = "index.php?contador=1&accio=comprovar_partida&jugador=<?php echo $jugador ?>&partida=<? echo $partida ?>";
      }, 1000);
      </script>
    <?php
  }
}





if($_REQUEST['accio'] == 'buscar_partida'){
  ?>
  <h3>Partides disponibles</h3>
  <?php
    $con = mysqli_connect("localhost","root","root","connect4") or exit(mysqli_connect_error());
    $sql = "SELECT * FROM partides WHERE ISNULL(nom_jugador2)";
    $result=mysqli_query($con, $sql) or exit(mysqli_error($con));
    while( $reg=mysqli_fetch_array($result)){
      ?>
      <span>Partida <?php echo $reg['id_partida'] ?> creada el <?php echo $reg['data'] ?> per  <?php $reg['nom_jugador']?> </span>
      <a href="index.php?accio=connectar_a_partida&jugador2=<?php echo $_REQUEST['jugador2']?>&partida=<?php echo $reg['id']?>">Connectar a partida</a><br><?php
    }
}




if($_REQUEST['accio'] == 'connectar_a_partida'){
  $jugador=$_REQUEST['jugador2'];
  $partida=$_REQUEST['partida'];

  $con = mysqli_connect("localhost","root","root","connect4") or exit(mysqli_connect_error());
  $sql = "UPDATE partides SET nom_jugador2 = '$jugador2' WHERE id_partida=".$_REQUEST['partida'];
  $result=mysqli_query($con, $sql) or exit(mysqli_error($con));
  ?>
  <h3>CONNECTAT A LA PARTIDA <?php echo $_REQUEST['partida'] ?></h3>
  <h4>Esperant moviment del jugador 1...</h4>
  <script>
        setTimeout(() => {
          window.location.href = "index.php?accio=moviment_partida&jugador=2&partida=<? echo $partida ?>";
      }, 1000);
  </script>
  <?php
}






function pintar_taulell($partida){
  $con = mysqli_connect("localhost","root","root","connect4") or exit(mysqli_connect_error());
  $sql = "SELECT * FROM moviments WHERE id_partida=$partida"
  $result=mysqli_query($con, $sql) or exit(mysqli_error($con));
  $taulell = [
    [0,0,0,0,0,0,0],
    [0,0,0,0,0,0,0],
    [0,0,0,0,0,0,0],
    [0,0,0,0,0,0,0],
    [0,0,0,0,0,0,0],
    [0,0,0,0,0,0,0],
  ]
  while($reg=mysqli_fetch_array($result)){
    $num_col=$reg["columna_moviment"];
    $jugador=$reg["jugador"];
    $num_col--;
    while($taulell[$c][$num_col] != 0){
      $c--;
    };
    $taulell[$c][$num_col] = $jugador
  }
  /* pintar taulell */
  for($t = 0; $t < 6; $t++){
    for($tt = 0; $tt < 6; $tt++){
      echo "|".$taulell[$t][$tt];
    }
    echo "|<br>"; 
  }
}


?>