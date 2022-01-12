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
        <header>
            <h2>Partida vs IA</h2> <a class="exitxd" href="index.php?jugador2=<?php echo $_REQUEST['username']?>&accio=buscar_partida">salir</a>
        </header>
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
                if($_REQUEST['turn'] == 1){ ?>
                    <!-- PANTALLA DEL JUGADOR -->
                    <div class="tablero">
                    <div class="board">
                    <?php pintar_taulell($partida); ?>
                    </div>
                    <div class='btn pointer'>
                    <a href="machine.php?turn=2&id=<?php echo $partida;?>&columna=1&username=<?php echo $_REQUEST['username']?>">1</a> 
                    <a href="machine.php?turn=2&id=<?php echo $partida;?>&columna=2&username=<?php echo $_REQUEST['username']?>">2</a>
                    <a href="machine.php?turn=2&id=<?php echo $partida;?>&columna=3&username=<?php echo $_REQUEST['username']?>">3</a>
                    <a href="machine.php?turn=2&id=<?php echo $partida;?>&columna=4&username=<?php echo $_REQUEST['username']?>">4</a>
                    <a href="machine.php?turn=2&id=<?php echo $partida;?>&columna=5&username=<?php echo $_REQUEST['username']?>">5</a>
                    <a href="machine.php?turn=2&id=<?php echo $partida;?>&columna=6&username=<?php echo $_REQUEST['username']?>">6</a> 
                    <a href="machine.php?turn=2&id=<?php echo $partida;?>&columna=7&username=<?php echo $_REQUEST['username']?>">7</a>
                    <div>
                </div> <?php
                }else{  
                    /* INTELIGÉNCIA DE LA MÁQUINA */
                    machineIQ($partida);

                   /*  Header('Location: machine.php?username='.$_REQUEST["username"].'&turn=1&id='.$partida); */
                }   
            }else{
                /* HAY GNADOR */
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
    $columna = mt_rand(1,7);
    $jugador = $_REQUEST['turn'] == 1? 2:1 ;
    $partida = $_REQUEST['id'];
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
        ?>
    </main>
</body>
</html>