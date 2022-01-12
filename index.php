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
  /* HOME + INTRODUCIR NOMBRE DE USUARIO */
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

  /* BUSCAR PARTIDA + BOTÓN CREAR PARTIDA */
  if(isset($_REQUEST['accio']) && $_REQUEST['accio'] == 'buscar_partida'){
      $con = mysqli_connect("localhost","daw_user","P@ssw0rd","connect4") or exit(mysqli_connect_error());
      $sql = "SELECT * FROM partides WHERE ISNULL(nom_jugador2)";
      $result=mysqli_query($con, $sql) or exit(mysqli_error($con));
      ?>

    <main class="buscarPartida">
      <header>
          <h1>Buscando partidas</h1>
          <span>
          <form action="machine.php">
            <input hidden name="username" value="<?php echo $_REQUEST['jugador2']?>">
            <input hidden name="turn" value="1">
            <input hidden name="start" value="1">
            <input type="submit" value="Jugar contra IA">
          </form>
          <form action="index.php">
            <input hidden name="jugador1" value="<?php echo $_REQUEST['jugador2']?>">
            <input hidden name="accio" value="crear_partida">
            <input type="submit" value="Crear partida">
          </form> 
          </span>
          
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
              <a href="index.php?accio=connectar_a_partida&jugador2=<?php echo $_REQUEST['jugador2']?>&partida=<?php echo $reg['id_partida']?>&username=<?php echo $_REQUEST['jugador2']?>">JUGAR</a>
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
        }, 2500);
      </script>
      <?php
  };

  /* CREAR PARTIDA */
  if(isset($_REQUEST['accio']) && $_REQUEST['accio']=="crear_partida"){
    $con = mysqli_connect("localhost","daw_user","P@ssw0rd","connect4") or exit(mysqli_connect_error());
    $sql = "INSERT INTO partides VALUES (null,'".date("Y-m-d")."','".$_REQUEST['jugador1']."',null,1,NULL)";
    $result=mysqli_query($con, $sql) or exit(mysqli_error($con));
    $id=mysqli_insert_id($con);
    ?>
    <script>
     window.location.href = "index.php?contador=1&accio=comprovar_partida&jugador=1&partida=<?php echo $id?>&username=<?php echo $_REQUEST['jugador1']; ?>";
    </script>
    <?php
  };

  /* PARTIDA CREADA, ESPERANDO/ENCONTRADO JUGADOR 2  */
  if(isset($_REQUEST['accio']) && $_REQUEST['accio'] == 'comprovar_partida'){
    $jugador=$_REQUEST['jugador'];
    $partida=$_REQUEST['partida'];
    $con = mysqli_connect("localhost","daw_user","P@ssw0rd","connect4") or exit(mysqli_connect_error());
    $sql = "SELECT * FROM partides WHERE id_partida = $partida";
    $result=mysqli_query($con, $sql) or exit(mysqli_error($con));
    $reg=mysqli_fetch_array($result);
    ?>
    <main class="buscarPartida">
     
      <?php
        if($reg['nom_jugador2'] != ''){
          ?>
           <header>
            <h2>Partida Creada - Rival Encontrado! (3)</h2>
            </header>
          <?php
          pintar_taulellNull();
          echo "<div class='btn'><a>1</a><a>2</a><a>3</a><a>4</a><a>5</a><a>6</a><a>7</a></div>";
          echo "</div>";
          ?>
            <script>
              let x = document.querySelector('header h2');

              setTimeout(() => {
                x.innerText  = 'Partida Creada - Rival Encontrado! (2)';
              }, 1000);
              setTimeout(() => {
                x.innerText  = 'Partida Creada - Rival Encontrado! (1)';
              }, 2000);
              setTimeout(() => {
                window.location.href = "index.php?accio=moviment_partida&jugador=1&partida=<?php echo $partida?>&username=<?php echo $_REQUEST['username']?>";
              }, 3000);
            </script>
          <?php
        }else{
          ?>
           <header>
            <h1>Partida Creada - Esperando rival</h1>
            </header>
          <?php
          pintar_taulellNull();
          echo "<div class='btn'><a>1</a><a>2</a><a>3</a><a>4</a><a>5</a><a>6</a><a>7</a></div></div>";
          ?>
        </main>
            <script>
              setTimeout(() => {
                window.location.href = "index.php?accio=comprovar_partida&jugador=1&partida=<?php echo $partida?>&username=<?php echo $_REQUEST['username']?>";
              }, 2500);
            </script>
          <?php
        };
  };
  
  /* PARTIDA */
  if(isset($_REQUEST['accio']) && $_REQUEST['accio'] == 'moviment_partida'){
    $jugador=$_REQUEST['jugador'];
    $partida=$_REQUEST['partida'];
    $con = mysqli_connect("localhost","daw_user","P@ssw0rd","connect4") or exit(mysqli_connect_error());
    $sql = "SELECT * FROM partides WHERE id_partida = $partida";
    $result=mysqli_query($con, $sql) or exit(mysqli_error($con));
    $reg=mysqli_fetch_array($result);


    if($reg['winner'] == NULL){
      $winner = checkWinner($partida);
      if($winner == 1){
        $sql = "UPDATE partides SET winner = 1 WHERE id_partida=".$_REQUEST['partida'];
        $result=mysqli_query($con, $sql) or exit(mysqli_error($con));
      }else if($winner == 2){
        $sql = "UPDATE partides SET winner = 2 WHERE id_partida=".$_REQUEST['partida'];
        $result=mysqli_query($con, $sql) or exit(mysqli_error($con));
      };
    }

    
    
      ?>
   <main class="buscarPartida">
     <?php
    
    
    
    if($reg['winner'] != NULL){?>
            <header>
              <h2>Partida <?php echo $partida?> - <?php echo $reg['winner'] == $jugador ? "Has ganado la partida!": "El jugador ".$reg['winner']." ha ganado!" ?></h2>
            </header>
            <div class="tablero">
              <div class="board">
                <?php pintar_taulell($partida); ?>
              </div>
              <div class='btn'>
                <a>1</a> 
                <a>2</a>
                <a>3</a>
                <a>4</a>
                <a>5</a>
                <a>6</a> 
                <a>7</a>
              <div>
            </div>
            </div>
            <div class="finishgame">
              <a href="index.php?jugador2=<?php echo $_REQUEST['username']; ?>&accio=buscar_partida">Salir</a>
            </div>
           
            
            <?php
    }else if($reg['torn'] == $jugador){?>
        <header>
          <h2>Partida <?php echo $partida?> - Tú turno!</h2>
        </header>
        <div class="tablero">
          <div class="board">
            <?php pintar_taulell($partida); ?>
          </div>
          <div class='btn pointer'>
            <a href="index.php?accio=enviar_moviment&jugador=<?php echo $jugador;?>&partida=<?php echo $partida;?>&columna=1&username=<?php echo $_REQUEST['username']?>">1</a> 
            <a href="index.php?accio=enviar_moviment&jugador=<?php echo $jugador;?>&partida=<?php echo $partida;?>&columna=2&username=<?php echo $_REQUEST['username']?>">2</a>
            <a href="index.php?accio=enviar_moviment&jugador=<?php echo $jugador;?>&partida=<?php echo $partida;?>&columna=3&username=<?php echo $_REQUEST['username']?>">3</a>
            <a href="index.php?accio=enviar_moviment&jugador=<?php echo $jugador;?>&partida=<?php echo $partida;?>&columna=4&username=<?php echo $_REQUEST['username']?>">4</a>
            <a href="index.php?accio=enviar_moviment&jugador=<?php echo $jugador;?>&partida=<?php echo $partida;?>&columna=5&username=<?php echo $_REQUEST['username']?>">5</a>
            <a href="index.php?accio=enviar_moviment&jugador=<?php echo $jugador;?>&partida=<?php echo $partida;?>&columna=6&username=<?php echo $_REQUEST['username']?>">6</a> 
            <a href="index.php?accio=enviar_moviment&jugador=<?php echo $jugador;?>&partida=<?php echo $partida;?>&columna=7&username=<?php echo $_REQUEST['username']?>">7</a>
          <div>
        </div>
        <?php
        }else{ ?>
            <header>
              <h1>Partida <?php echo $partida?> - Esperando movimento del jugador <?php echo $jugador == 1? 2:1; ?></h1>
            </header>
            <div class="tablero">
              <div class="board">
                <?php pintar_taulell($partida); ?>
              </div>
              <div class='btn'>
                <a>1</a> 
                <a>2</a>
                <a>3</a>
                <a>4</a>
                <a>5</a>
                <a>6</a> 
                <a>7</a>
              <div>
            </div>
            <script>
              setTimeout(() => {
                window.location.href = "index.php?accio=moviment_partida&jugador=<?php echo $jugador ?>&partida=<?php echo $partida ?>&username=<?php echo $_REQUEST['username']?>";
              }, 1000);
            </script>
          <?php
        }; ?>
      </main><?php
    };


  /* UNIRSE A UNA PARTIDA */
  if(isset($_REQUEST['accio']) && $_REQUEST['accio'] == 'connectar_a_partida'){
    $partida=$_REQUEST['partida'];
    $con = mysqli_connect("localhost","daw_user","P@ssw0rd","connect4") or exit(mysqli_connect_error());
    $sql = "UPDATE partides SET nom_jugador2 = 2 WHERE id_partida=".$_REQUEST['partida'];
    $result=mysqli_query($con, $sql) or exit(mysqli_error($con));
    ?>
    <script>
      window.location.href = "index.php?accio=moviment_partida&jugador=2&partida=<?php echo $partida ?>&username=<?php echo $_REQUEST['username']?>";
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
      <script>
          window.location.href = "index.php?accio=moviment_partida&jugador=<?php echo $jugador ?>&partida=<?php echo $partida ?>&username=<?php echo $_REQUEST['username']?>";
        </script>
    <?php
  };

  /* PINTAR TABLERO CON FICHAS DE LA PARTIDA */
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
    for($t = 0; $t < 6; $t++){
      for($tt = 0; $tt < 7; $tt++){
        echo "<span class='circle".$taulell[$t][$tt]."'></span>";
      };
    };
  };

  /* PINTAR TABLERO SIN FICHAS */
  function pintar_taulellNull(){
    $taulell = [
      [0,0,0,0,0,0,0],
      [0,0,0,0,0,0,0],
      [0,0,0,0,0,0,0],
      [0,0,0,0,0,0,0],
      [0,0,0,0,0,0,0],
      [0,0,0,0,0,0,0],
    ];

    echo "<div class='tablero'><div class='board'>";
    for($t = 0; $t < 6; $t++){
      for($tt = 0; $tt < 7; $tt++){
        echo "<span class='circle".$taulell[$t][$tt]."'></span>";
      };
    };
    echo "</div>";
  }

  function checkWinner($partida){
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


    for($t = 0; $t < 6; $t++){
        $n_uns=0;
        for($tt =0;$tt<7;$tt++){
            if($taulell[$t][$tt]==1){
                $n_uns++;
                if($n_uns == 4){
                    return 1;
                }
            }else{
                $n_uns=0;
            }
        }
    };
    for($t = 0; $t < 6; $t++){
        $n_uns=0;
        for($tt =0;$tt<7;$tt++){
            if($taulell[$t][$tt]==2){
                $n_uns++;
                if($n_uns == 4){
                    return 2;
                }
            }else{
                $n_uns=0;
            }
        }
    };
    for($t = 0; $t < 7; $t++){
        $n_uns=0;
        for($tt =0;$tt<6;$tt++){
            if($taulell[$tt][$t]==1){
                $n_uns++;
                if($n_uns == 4){
                    return 1;
                }
            }else{
                $n_uns=0;
            }
        }
    };
    for($t = 0; $t < 7; $t++){
        $n_uns=0;
        for($tt =0;$tt<6;$tt++){
            if($taulell[$tt][$t]==2){
                $n_uns++;
                if($n_uns == 4){
                    return 2;
                }
            }else{
                $n_uns=0;
            }
        }
    };
    for($t = -3; $t < 3; $t++){
        $n_uns = 0;
        for($tt=0;$tt < 7; $tt++){
            if(($t+$tt)>=0 && ($t+$tt)<6 && $tt>=0 &&$tt<7){
                if($taulell[$t+$tt][$tt] == 1){
                    $n_uns++;
                    if($n_uns >= 4) return 1;
                }else{
                    $n_uns = 0;
                }
            }
        }
    }
    for($t = 3; $t <= 8; $t++){
        $n_uns = 0;
        for($tt=0;$tt < 7; $tt++){
            if(($t-$tt)>=0 && ($t-$tt)<6 && $tt>=0 &&$tt<7){
                if($taulell[$t-$tt][$tt] == 1){
                    $n_uns++;
                    if($n_uns >= 4) return 1;
                }else{
                    $n_uns = 0;
                }
            }
        }
    }
    for($t = -3; $t < 3; $t++){
        $n_uns = 0;
        for($tt=0;$tt < 7; $tt++){
            if(($t+$tt)>=0 && ($t+$tt)<6 && $tt>=0 &&$tt<7){
                if($taulell[$t+$tt][$tt] == 2){
                    $n_uns++;
                    if($n_uns >= 4) return 2;
                }else{
                    $n_uns = 0;
                }
            }
        }
    }
    for($t = 3; $t <= 8; $t++){
        $n_uns = 0;
        for($tt=0;$tt < 7; $tt++){
            if(($t-$tt)>=0 && ($t-$tt)<6 && $tt>=0 &&$tt<7){
                if($taulell[$t-$tt][$tt] == 2){
                    $n_uns++;
                    if($n_uns >= 4) return 2;
                }else{
                    $n_uns = 0;
                }
            }
        }
    }
    return 3;
}


?>
</body>
</html>