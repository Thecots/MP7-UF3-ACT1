<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connect 4 - PVE</title>
    <link rel="icon" href="./img/logo.png">
    <link rel="stylesheet" href="./css/global.css">
    <link rel="stylesheet" href="./css/buscarPartida.css">
</head>
<body>
    <main class="buscarPartida">
        
        <?php
            /* TURNO JUGADOR = 1, TURNO MÁQUINA = 2 */

            if(isset($_REQUEST['start'])){
                $con = mysqli_connect("localhost","daw_user","P@ssw0rd","connect4") or exit(mysqli_connect_error());
                $sql = "INSERT INTO partides VALUES (null,'".date("Y-m-d")."','".$_REQUEST['jugador1']."',3,1,NULL)";
                $result=mysqli_query($con, $sql) or exit(mysqli_error($con));
                $id=mysqli_insert_id($con);
                Header('Location: machine.php?username='.$_REQUEST["username"].'&turn=1&id='.$id);
            }

            $jugador=$_REQUEST['username'];
            $partida=$_REQUEST['id'];
            $con = mysqli_connect("localhost","daw_user","P@ssw0rd","connect4") or exit(mysqli_connect_error());
            $sql = "SELECT * FROM partides WHERE id_partida = $partida";
            $result=mysqli_query($con, $sql) or exit(mysqli_error($con));
            $reg=mysqli_fetch_array($result);

            /* GUARDA MOVIMIENTO */
            if(isset($_REQUEST['columna'])){
                $columna = $_REQUEST['columna'];
                $partida = $_REQUEST['id'];
                $con = mysqli_connect("localhost","daw_user","P@ssw0rd","connect4") or exit(mysqli_connect_error());
                $sql = "INSERT INTO moviments VALUES (NULL,'".date("H:i:s")."',NULL,1,$columna,$partida)";
                $result=mysqli_query($con, $sql) or exit(mysqli_error($con));
            }

            if(isset($_REQUEST['turn'])){
                /* checkWinner($_REQUEST['id']); */
                $cw = checkWinner($_REQUEST['id']);
                if($cw == 1){
                    Header('Location: machine.php?winner=1&id='.$partida.'&username='.$_REQUEST["username"]);
                }else if($cw == 2){
                    Header('Location: machine.php?winner=2&id='.$partida.'&username='.$_REQUEST["username"]);
                }else{
                    if($_REQUEST['turn'] == 1){ ?>
                        <!-- PANTALLA DEL JUGADOR -->
                        <header>
                            <h2>Partida vs IA</h2> <a class="exitxd" href="index.php?jugador2=<?php echo $_REQUEST['username']?>&accio=buscar_partida">salir</a>
                        </header>
                        <div class="tablero">
                        <div class="board">
                        <?php pintar_taulell($partida); ?>
                        </div>
                        <div class='btn pointer'>
                            <?php buttons($_REQUEST['id']) ?>
                        <div>
                    </div> <?php
                    }else{  
                        /* INTELIGÉNCIA DE LA MÁQUINA */
                        machineIQ($partida);
    
                       /*  Header('Location: machine.php?username='.$_REQUEST["username"].'&turn=1&id='.$partida); */
                    }   
                }
            }else{
                /* HAY GNADOR */
                if($_REQUEST['winner'] == 1){ ?>
                    <header>
                        <h2>Partida vs IA - Has ganado</h2> <a class="exitxd" href="index.php?jugador2=<?php echo $_REQUEST['username']?>&accio=buscar_partida">salir</a>
                    </header>
                    <?php
                }else{?>
                    <header>
                        <h2>Partida vs IA - Has perdido</h2> <a class="exitxd" href="index.php?jugador2=<?php echo $_REQUEST['username']?>&accio=buscar_partida">salir</a>
                    </header>
                    <?php
                };
                ?>
                    <div class="tablero">
                        <div class="board">
                            <?php pintar_taulell($partida); ?>
                        </div>
                    </div>
                <?php
            }


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


  /* Máquina */

  function machineIQ(){  
    $jugador = $_REQUEST['turn'] == 1? 2:1 ;
    $partida = $_REQUEST['id'];
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
  
      $columna = mt_rand(1,7);
      if($taulell[0][$columna] != 0){
        machineIQ();
      }

      $con = mysqli_connect("localhost","daw_user","P@ssw0rd","connect4") or exit(mysqli_connect_error());
      $sql = "INSERT INTO moviments VALUES (NULL,'".date("H:i:s")."',NULL,2,$columna,$partida)";
      $result=mysqli_query($con, $sql) or exit(mysqli_error($con));
      Header('Location: machine.php?turn=1&id='.$partida.'&username='.$_REQUEST["username"]);
    
};


  /* Checkea quien ha ganado */
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


function buttons($id){
    $con = mysqli_connect("localhost","daw_user","P@ssw0rd","connect4") or exit(mysqli_connect_error());
    $sql = "SELECT * FROM moviments WHERE id_partida=$id";
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
  
    /* 1 */
    if($taulell[0][0] == 0){
      echo  "<a href='machine.php?turn=2&id=".$_REQUEST['id']."&username=".$_REQUEST['username']."&columna=1&username=".$_REQUEST['username']."' >1</a>"; 
    }else{
      echo "<a class='dissabled'>1</a>";
    }
  
    /* 2 */
    if($taulell[0][1] == 0){
      echo  "<a href='machine.php?turn=2&id=".$_REQUEST['id']."&username=".$_REQUEST['username']."&columna=2&username=".$_REQUEST['username']."' >2</a>"; 
    }else{
      echo "<a class='dissabled'>2</a>";
    }
  
    /* 3 */
    if($taulell[0][2] == 0){
      echo  "<a href='machine.php?turn=2&id=".$_REQUEST['id']."&username=".$_REQUEST['username']."&columna=3&username=".$_REQUEST['username']."' >3</a>"; 
    }else{
      echo "<a class='dissabled'>3</a>";
    }
  
    /* 4 */
    if($taulell[0][3] == 0){
      echo  "<a href='machine.php?turn=2&id=".$_REQUEST['id']."&username=".$_REQUEST['username']."&columna=4&username=".$_REQUEST['username']."' >4</a>"; 
    }else{
      echo "<a class='dissabled'>4</a>";
    }
  
    /* 5 */
    if($taulell[0][4] == 0){
      echo  "<a href='machine.php?turn=2&id=".$_REQUEST['id']."&username=".$_REQUEST['username']."&columna=5&username=".$_REQUEST['username']."' >5</a>"; 
    }else{
      echo "<a class='dissabled'>5</a>";
    }
  
    /* 6 */
    if($taulell[0][5] == 0){
      echo  "<a href='machine.php?turn=2&id=".$_REQUEST['id']."&username=".$_REQUEST['username']."&columna=6&username=".$_REQUEST['username']."' >6</a>"; 
    }else{
      echo "<a class='dissabled'>6</a>";
    }
  
     /* 7 */
     if($taulell[0][6] == 0){
      echo  "<a href='machine.php?turn=2&id=".$_REQUEST['id']."&username=".$_REQUEST['username']."&columna=7&username=".$_REQUEST['username']."' >7</a>"; 
    }else{
      echo "<a class='dissabled'>7</a>";
    }
  }
  ?>
    
    </main>
</body>
</html>

                  