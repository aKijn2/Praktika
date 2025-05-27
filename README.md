
# ALAIKTO MUGI - TAXI ENPRESA

Proiektu hau klaseko lana da, Iker Hernández Navas eta Achraf Allach Chahboun ikasleen egina, Tolosaldea Lanbide Heziketa Institutuko Lehenengo Mailako Aplikazioen Garapeneko ikasleak gara.



## Autoreak

- [@ikerherna28](https://github.com/ikerherna28)
- [@aKjin2](https://github.com/aKjin2)


## Proiektuaren deskribapena

AlaiktoMUGI taxi enpresa bat da, eta aplikazio hau enpresaren administratzaileentzako garatu da. Aplikazioaren helburua gidariak eta bidaiak modu erraz eta profesional batean kudeatzea da, bai bidaiak sortu eta editatuz, bai gidariak kudeatuz.

Aplikazioak aukera ematen du:

    - Administratzaileak gidariak sortu, aldatu, ezabatu eta kontsultatzeko.

    - Bidaiak sortu, editatu, egoera aldatu eta bidaiaren informazioa ikus daiteke.

    - Bidaiak gidari zehatz bati esleitu eta bidaiaren egoera kudeatu.

    - Pantaila nagusian bidai eta gidarien informazio orokorra ikustea.

    - Den-dena euskaraz eta profesionaltasunarekin.


## Proeiktua testatu/Entornoa sortu


Hasteko, ziurtatu Docker instalatuta duzula zure sisteman, eta gero klonatu repositorio hau.

Ondoren, nabigatu zure terminalean klonatu zenuen direktorioraino eta jarri martxan web zerbitzarirako edukiontziak `.docker-compose up -d --build app` exekutatuz.

Amaitu ondoren, jarraitu [README.md](https://github.com/ikerherna28/pakaggarraioak-pakete-kudeaketa?tab=readme-ov-file#pakaggarraioak-pakete-kudeaketa) fitxategiaren urratsei proiektua gauzatu ahal izateko.


*Oharra:* MySQL datu-basearen host izena `mysql` da, ez localhost. Nahiz eta izena localhost ez izan gure zerbitzua `localhost`-en aurkitzen da. Gure kasuan, datu basearen izena `webapp` horrela konfiguratu dugulako Docker kontenedore/bolumena sortzerakoan.
Datu basearen erabiltzaile eta pasahitza `root`/`root` dira.

Docker Compose sarea app-arekin igotzeak, bakarrik erabili ordez, ziurtatzen du gure guneko edukiontziak bakarrik sortzen direla hasieran, kontenedore guztien ordez. Hauek dira gure web zerbitzariarentzat eraiki diren portuak, xehetasun guztiekin:

- NGINX - `80:80`
- MySQL - `33307:3306`
- PHPMYADMIN - `8080:80`
- PHP - `9003:9003`
  - Mailhog - `25:25` (PORTUA PHPren BARRUAN)


Bi kontenedore gehigarri daude, Composer, NPM komandoak kontrolatzen dituztenak, plataforma horiek tokiko ekipoan instalatuta eduki behar izan gabe. Proiektua hau exekutatu edo altsatzeko erabili proiektuaren erroko komandoen adibide hauek, beste proiektu baterako (gure proiektua adibide gisa erabili ezkero) aldatu, erabilera partikularreko kasuetara egokitu daitezen.

- `docker-compose run --rm composer update`
- `docker-compose run --rm npm run dev`



## Arazoak baimenekin


Aplikazioa bisitatzen duzun bitartean edo edukiontzi-komando bat exekutatzen duzun bitartean fitxategi-sistemaren baimenekin arazoren bat aurkitzen baduzu, saia zaitez jarraian adierazten diren urrats-multzoetako bat betetzen.

**Zerbitzaria edo ingurune lokala erroko erabiltzaile gisa erabiltzen ari bazara:**

- Deskargatu `docker-compose down` duen edozein edukiontzi
- Ordeztu docker-compose.yml artxiboko edozein instantzia `php.dockerfile` `php.root.dockerfile`
- Konpilatu berriro edukiontziak `docker-compose build --no-cache` exekutatuta

**Zerbitzaria edo ingurune lokala root ez den erabiltzaile gisa erabiltzen ari bazara:**

- Deskargatu `docker-compose down` duen edozein edukiontzi
- Zure terminalean, exekutatu eta gero `export UID = $(id -u) export GID = $(id -g)`
- Aurreko urratseko irakurketa hutseko aldagaietan akatsen bat ikusten baduzu, alde batera utz dezakezu eta jarraitu
- Konpilatu berriro kontenedoreak `docker-compose build --no-cache` exekutatuta

Jarraian, aktibatu berriro edukiontzi-sarea edo exekutatu berriro lehen probatzen ari zinen komandoa eta ikusi horrek konpontzen duen.

## MySQL biltegiratze iraunkorra

Modu lehenetsian, Docker sarea desaktibatzen den bakoitzean, MySQLren datuak ezabatu egingo dira edukiontziak suntsitu ondoren. Edukiontziak deskonektatu eta haien babeskopiak egin ondoren, datu iraunkorrak eduki nahi badituzu, honako hau egin:

1. Sortu karpeta bat proiektuaren erroan, `mysql` `nginx` `src` karpetekin batera
2. Zure artxiboko `mysql` zerbitzuan, gehitu lerro hauek: `docker-compose.yml`


```mysql
  volumes:
      - mysqlDB:/mysqlDB/init.sql:/docker-entrypoint-initdb.d/init.sql
```

## Produkzioan erabiltzea

Hasiera batean, tokiko garapenerako sortu genuen txantiloi hori, baina Laravel aplikazioen oinarrizko inplementazioetan erabiltzeko bezain sendoa da. Gomendiorik garrantzitsuena HTTPS gaituta dagoela ziurtatzea litzateke, fitxategiari gehikuntzak eginez eta [Let's Encrypt](https://hub.docker.com/r/linuxserver/letsencrypt) bezalako zerbait erabiliz SSL `nginx/default.conf` ziurtagiri bat ekoizteko.
