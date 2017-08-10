-- Kysymys -taulun testidata

INSERT INTO Kysymys (nimi, kysymysteksti, vastaustyyppi)
 VALUES ('Pääaine','Kirjoita pääaineesi',1);

INSERT INTO Vastaus (vastaaja_id,
  vastausteksti) 
 VALUES (11, 'gooby pls');

INSERT INTO Käyttäjä (nimi,
  käyttöoikeus)
VALUES ('Dolan', 0);

INSERT INTO Käyttäjä (nimi,
  käyttöoikeus)
VALUES ('Gooby', 0);


INSERT INTO Kyselylomake(julkaisuaika,sulkemisaika) VALUES ('2000-11-19','2007-05-13');

INSERT INTO Kurssi (nimi, laitos) VALUES ('johdatus filosofointiin', 'filosofisen filosofian ja sosiaalifilosofian laitos');
INSERT INTO Kurssi (nimi, laitos) VALUES ('johdatus syvään filosofointiin', 'filosofisen filosofian ja sosiaalifilosofian laitos');

INSERT INTO YksityiskohtainenRaportti(laatimisaika,raportti) VALUES ('20000-01-01', 'Deep analysis follows:');

--annetaan dolanille kaksi kurssia pidettäväksi--
INSERT INTO LiitosKäyttäjäKurssi(kurssi_id, käyttäjä_id) VALUES ((SELECT kurssi_id from Kurssi where nimi='johdatus filosofointiin'),
		(SELECT käyttäjä_id from Käyttäjä where nimi='Dolan'));

INSERT INTO LiitosKäyttäjäKurssi(kurssi_id, käyttäjä_id) VALUES ((SELECT kurssi_id from Kurssi where nimi='johdatus syvään filosofointiin'),
		(SELECT käyttäjä_id from Käyttäjä where nimi='Dolan'));

--lisätään gooby toisen näistä luennoijaksi--
INSERT INTO LiitosKäyttäjäKurssi(kurssi_id, käyttäjä_id) VALUES ((SELECT kurssi_id from Kurssi where nimi='johdatus syvään filosofointiin'),
		(SELECT käyttäjä_id from Käyttäjä where nimi='Gooby'));