-- Lisää CREATE TABLE lauseet tähän tiedostoon
CREATE TABLE Kysymys(
  kysymys_id SERIAL PRIMARY KEY,
  nimi VARCHAR (100),
  kysymysteksti VARCHAR (500),
  vastaustyyppi INTEGER,
  välin_alku INTEGER,
  välin_loppu INTEGER

);

CREATE TABLE Vastaus(
  kysymys_id INTEGER REFERENCES Kysymys(kysymys_id),
  vastaaja_id INTEGER ,
  vastausteksti VARCHAR (500),
  num_vastaus INTEGER,
  likert_vastaus INTEGER

);

CREATE TABLE Käyttäjä(
  käyttäjä_id SERIAL PRIMARY KEY,
  nimi VARCHAR (100),
  käyttöoikeus INTEGER

);

CREATE TABLE Kurssi(
  kurssi_id SERIAL PRIMARY KEY,
  nimi VARCHAR (100),
  laitos VARCHAR (100)

);



CREATE TABLE Kyselylomake(
  lomake_id SERIAL PRIMARY KEY,
  kurssi_id INTEGER REFERENCES Kurssi(kurssi_id),
  julkaisuaika DATE,
  sulkemisaika DATE

);

CREATE TABLE LiitosLomakeKysymys(
  kysymys_id INTEGER REFERENCES Kysymys(kysymys_id),
  kyselylomake_id INTEGER REFERENCES Kyselylomake(lomake_id)

);

CREATE TABLE LiitosKäyttäjäKurssi(
  kurssi_id INTEGER REFERENCES Kurssi(kurssi_id),
  käyttäjä_id INTEGER REFERENCES Käyttäjä(käyttäjä_id)

);

CREATE TABLE YksityiskohtainenRaportti(
  laatija INTEGER REFERENCES Käyttäjä(käyttäjä_id),
  laatimisaika DATE,
  raportti VARCHAR (5000)

);