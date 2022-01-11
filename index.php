<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Connect 4 - dcots</title>
  <link rel="icon" href="./img/logo.png">
  <link rel="stylesheet" href="./css/global.css">
  <link rel="stylesheet" href="./css/home.css">
  <link rel="stylesheet" href="./css/buscarPartida.css">
</head>
<body>
<?php
  /* HOME -> Introduce nombre de usuario */
  if(!isset($_REQUEST['accio'])){?>
    <main class="home">
      <img src="./img/fondo.jpg" alt="">
      <section>
        <div class="logo">
          <h1>CONNECT 4</h1>
          <h1>ONLINE</h1>
        </div>
        <form action="index.php">
          <div class="text-field">
              <input id="titulo" required autocomplete="off" name="jugador2">
              <label class="label">Nombre de usuario</label>
          </div>
          <input hidden name="accio" value="buscar_partida">
              <div class="btn-box">
                <input type="submit" value="Buscar partida">
              </div>
        </form>
      </section>
    </main>
    <?php
  };

  
  if(isset($_REQUEST['accio']) && $_REQUEST['accio'] == 'buscar_partida'){
      $con = mysqli_connect("localhost","daw_user","P@ssw0rd","connect4") or exit(mysqli_connect_error());
      $sql = "SELECT * FROM partides WHERE ISNULL(nom_jugador2)";
      $result=mysqli_query($con, $sql) or exit(mysqli_error($con));
      ?>

    <main class="buscarPartida">
      <header>
          <h1>Buscando partidas</h1>
          <form action="index.php">
            <input hidden name="jugador1" value="<?php echo $_REQUEST['jugador2']?>">
            <input hidden name="accio" value="crear_partida">
            <input type="submit" value="Crear partida">
          </form> 
      </header>
      <section>
        <?php
          $x = true;
          while($reg = mysqli_fetch_array($result)){
            $x = false;
            ?>
            <div>
              <span>
                <h3>Partida <?php echo $reg['id_partida'] ?></h3>
                <h3>Creada por: <?php echo $reg['nom_jugador1']?></h3><h3>-</h3>
                <h3><?php echo $reg['data'] ?></h3>
              </span>
              <a href="index.php?accio=connectar_a_partida&jugador2=<?php echo $_REQUEST['jugador2']?>&partida=<?php echo $reg['id_partida']?>">JUGAR</a>
          </div>
            <?php
           
          }
          if($x){
            echo '<h1>NO HAY NINGUNA PARTIDA DISPONIBLE :(</h1>';
          };
          
          ?>
      </section>
    </main>
      <script>
        setTimeout(() => {
          window.location.href="index.php?jugador2=<?php echo $_REQUEST['jugador2']?>&accio=buscar_partida"
        }, 5000);
      </script>
      <?php
  }






  if(isset($_REQUEST['accio']) && $_REQUEST['accio']=="crear_partida"){
    /* Crear la partida en la base de datos */
    $con = mysqli_connect("localhost","daw_user","P@ssw0rd","connect4") or exit(mysqli_connect_error());
    $sql = "INSERT INTO partides VALUES (null,'".date("Y-m-d")."','".$_REQUEST['jugador1']."',null,1)";
    $result=mysqli_query($con, $sql) or exit(mysqli_error($con));
    $id=mysqli_insert_id($con);
    ?>
    <h3>Partida creada num <?php echo $id; ?></h3>
    <h4>hola Esperant contrincant...</h4>
    <script>
      setTimeout(() => {
        window.location.href = "index.php?contador=1&accio=comprovar_partida&jugador=1&partida=<?php echo $id?>";
      }, 1000);
    </script>
    <?php
  }





  if(isset($_REQUEST['accio']) && $_REQUEST['accio'] == 'comprovar_partida'){
    /* jugador 1 esperando para jugador 2 */
    $contador=$_REQUEST['contador']+1;
    $jugador=$_REQUEST['jugador'];
    $partida=$_REQUEST['partida'];
    ?>
    <h3>Partida creada num <?php echo $partida?></h3>;
    <?php
    $con = mysqli_connect("localhost","daw_user","P@ssw0rd","connect4") or exit(mysqli_connect_error());
    $sql = "SELECT * FROM partides WHERE id_partida = $partida";
    $result=mysqli_query($con, $sql) or exit(mysqli_error($con));
    $reg=mysqli_fetch_array($result);
    if($reg['nom_jugador2'] != ''){
      /* jugador 2 encontrado */
      ?>
        <h1>Tenemos rival!</h1>
        <script>
          setTimeout(() => {
            window.location.href = "index.php?accio=moviment_partida&jugador=1&partida=<?php echo $partida?>";
          }, 1000);
        </script>
      <?php
    }else{
        $contador = $_REQUEST['contador'];
        $contador += 1;
      ?>
        <h4>Esperant contrincant <?php echo $_REQUEST['contador']?><h4>
        <script>
          setTimeout(() => {
            window.location.href = "index.php?contador=<?php echo $_REQUEST['contador']+1?>&accio=comprovar_partida&jugador=1&partida=<?php echo $partida?>";
          }, 1000);
        </script>
      <?php
    };
  }
    





  if(isset($_REQUEST['accio']) && $_REQUEST['accio'] == 'moviment_partida'){
    $jugador=$_REQUEST['jugador'];
    $partida=$_REQUEST['partida'];
    /* mirar turno */
    $con = mysqli_connect("localhost","daw_user","P@ssw0rd","connect4") or exit(mysqli_connect_error());
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
        <input type="number" name="columna" min="1" max="7" require>
        <input hidden name="accio" value="enviar_moviment">
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
            window.location.href = "index.php?accio=moviment_partida&jugador=<?php echo $jugador ?>&partida=<?php echo $partida ?>";
        }, 1000);
        </script>
      <?php
    }
  }








  if(isset($_REQUEST['accio']) && $_REQUEST['accio'] == 'connectar_a_partida'){
    $jugador=$_REQUEST['jugador2'];
    $partida=$_REQUEST['partida'];

    $con = mysqli_connect("localhost","daw_user","P@ssw0rd","connect4") or exit(mysqli_connect_error());
    $sql = "UPDATE partides SET nom_jugador2 = '$jugador' WHERE id_partida=".$_REQUEST['partida'];
    $result=mysqli_query($con, $sql) or exit(mysqli_error($con));
    ?>
    <h3>CONECTAT A LA PARTIDA <?php echo $_REQUEST['partida'] ?></h3>
    <h4>Esperant moviment del jugador 1...</h4>
    <script>
          setTimeout(() => {
            window.location.href = "index.php?accio=moviment_partida&jugador=2&partida=<?php echo $partida ?>";
        }, 1000);
    </script>
    <?php
  }




  /* GUARDA EL MOVIMENTO, CAMBIA DE TURNO */
  if(isset($_REQUEST['accio']) && $_REQUEST['accio'] == 'enviar_moviment'){
    $jugador=$_REQUEST['jugador'];
    $partida=$_REQUEST['partida'];
    $columna=$_REQUEST['columna'];
    $con = mysqli_connect("localhost","daw_user","P@ssw0rd","connect4") or exit(mysqli_connect_error());
    $sql = "INSERT INTO moviments VALUES (NULL,'".date("H:i:s")."',NULL,$jugador,$columna,$partida)";
    echo $sql;
    $result=mysqli_query($con, $sql) or exit(mysqli_error($con));

    $sql = "UPDATE partides SET torn = IF(torn=1,2,1) WHERE id_partida=$partida";
    $result=mysqli_query($con, $sql) or exit(mysqli_error($con));
    ?>
    <h3>Aquí va el taulell</h3>
    <h4>Moviment gravat</h4>
      <script>
        setTimeout(() => {
          window.location.href = "index.php?accio=moviment_partida&jugador=<?php echo $jugador ?>&partida=<?php echo $partida ?>";
      }, 1000);
        </script>
    <?php
  }



  function pintar_taulell($partida){
    $con = mysqli_connect("localhost","daw_user","P@ssw0rd","connect4") or exit(mysqli_connect_error());
    $sql = "SELECT * FROM moviments WHERE id_partida=$partida";
    $result=mysqli_query($con, $sql) or exit(mysqli_error($con));
    $taulell = [
      [0,0,0,0,0,0,0],
      [0,0,0,0,0,0,0],
      [0,0,0,0,0,0,0],
      [0,0,0,0,0,0,0],
      [0,0,0,0,0,0,0],
      [0,0,0,0,0,0,0],
    ];

    while($reg=mysqli_fetch_array($result)){
      $num_col=$reg["columna_moviment"];
      $jugador=$reg["jugador"];
      $num_col--;
      $c = 5;
      while($taulell[$c][$num_col] != 0){
        $c--;
      };
      $taulell[$c][$num_col] = $jugador;
    };
    /* pintar taulell */
    for($t = 0; $t < 6; $t++){
      for($tt = 0; $tt < 6; $tt++){
        echo "|".$taulell[$t][$tt];
      };
      echo "|<br>"; 
    };
  };

?>
</body>
</html>