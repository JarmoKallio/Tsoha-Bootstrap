-- Lisää CREATE TABLE lauseet tähän tiedostoon

CREATE TABLE Kayttaja(
  kayttaja_id SERIAL PRIMARY KEY,
  nimi VARCHAR (100),
  salasana VARCHAR (100),
  kayttooikeus INTEGER

);

CREATE TABLE Kurssi(
  kurssi_id SERIAL PRIMARY KEY,
  nimi VARCHAR (100),
  laitos VARCHAR (100),
  julkaistu BOOLEAN,
  suljettu BOOLEAN

);


CREATE TABLE LiitosKayttajaKurssi(
  kurssi_id INTEGER REFERENCES Kurssi(kurssi_id),
  kayttaja_id INTEGER REFERENCES Kayttaja(kayttaja_id)

);

CREATE TABLE Kysymys(
  kysymys_id SERIAL PRIMARY KEY,
  kurssi_id INTEGER REFERENCES Kurssi(kurssi_id),
  nimi VARCHAR (100),
  kysymysteksti VARCHAR (500),
  vastaustyyppi VARCHAR (20)

);

CREATE TABLE Vastaus(
  vastaus_id SERIAL PRIMARY KEY,
  kysymys_id INTEGER REFERENCES Kysymys(kysymys_id),
  vastaaja_id INTEGER,
  vastausteksti VARCHAR (500),
  likert_vastaus INTEGER

);
