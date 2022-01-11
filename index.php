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
?>