-- Kysymys -taulun testidata

INSERT INTO Kysymys (nimi, kysymysteksti, vastaustyyppi)
 VALUES ('Pääaine','Kirjoita pääaineesi',1);

INSERT INTO Vastaus (vastaaja_id,
  vastausteksti) 
 VALUES (11, 'gooby pls');

INSERT INTO Käyttäjä (nimi,
  käyttöoikeus)
VALUES ('Dolan', 1);

INSERT INTO Kyselylomake(julkaisuaika,sulkemisaika) VALUES (2000,2007);

INSERT INTO Kurssi (nimi, laitos) VALUES ('johdatus filosofointiin', 'filosofisen filosofian ja sosiaalifilosofian laitos');

INSERT INTO YksityiskohtainenRaportti(laatimisaika,raportti) VALUES (20000, 'Deep analysis follows:');



