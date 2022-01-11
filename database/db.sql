
DROP DATABASE IF EXISTS connect4;
CREATE DATABASE connect4;
USE connect4;

CREATE TABLE partides(
  id_partida INT PRIMARY KEY AUTO_INCREMENT,
  data DATE,
  nom_jugador1 VARCHAR(100),
  nom_jugador2 VARCHAR(100),
  torn INT
);

CREATE TABLE moviments(
  id_moviment INT PRIMARY KEY AUTO_INCREMENT,
  hora TIME,
  num_moviment INT,
  jugador INT,
  columna_moviment INT,
  id_partida INT,
  FOREIGN KEY (id_partida) REFERENCES partides(id_partida)
)