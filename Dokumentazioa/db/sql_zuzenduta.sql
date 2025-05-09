CREATE TABLE Erabiltzaileak (
	id_erabiltzailea INT PRIMARY KEY AUTO_INCREMENT,
	izena VARCHAR(25) NOT NULL,
	abizena VARCHAR(50) NOT NULL,
	emaila VARCHAR(100) UNIQUE NOT NULL,
	helbidea VARCHAR(150) NOT NULL,
	telefonoa VARCHAR(9) UNIQUE NOT NULL,
	pasahitza VARCHAR(255) NOT NULL
);

CREATE TABLE Administratzaileak (
	id_admin INT PRIMARY KEY AUTO_INCREMENT,
	NAN VARCHAR(9) UNIQUE NOT NULL,
	izena VARCHAR(25) NOT NULL,
	abizena VARCHAR(50) NOT NULL,
	emaila VARCHAR(100) UNIQUE NOT NULL,
	helbidea VARCHAR(150) NOT NULL,
	telefonoa VARCHAR(9) UNIQUE NOT NULL,
	pasahitza VARCHAR(255) NOT NULL
);

CREATE TABLE Gidariak (
	id_gidariak INT PRIMARY KEY AUTO_INCREMENT,
	NAN VARCHAR(9) UNIQUE NOT NULL,
	izena VARCHAR(25) NOT NULL,
	abizena VARCHAR(50) NOT NULL,
	helbidea VARCHAR(150) NOT NULL,
	jaiotze_data DATE NOT NULL,
	emaila VARCHAR(100) UNIQUE NOT NULL,
	telefonoa VARCHAR(9) UNIQUE NOT NULL,
	pasahitza VARCHAR(255) NOT NULL,
taxi_matrikula VARCHAR(20) UNIQUE NOT NULL,
	gidari_egoera VARCHAR(50)
);

CREATE TABLE Erreserbak (
	id_erreserba INT PRIMARY KEY AUTO_INCREMENT,
	data_esleipena DATE NOT NULL,
	ordua_esleipena TIME NOT NULL,
	egoera_erreserba VARCHAR(50) NOT NULL,
	id_erabiltzailea INT NOT NULL,
	FOREIGN KEY (id_erabiltzailea) REFERENCES Erabiltzaileak(id_erabiltzailea)
);
CREATE TABLE Bidaiak (
	id_bidaia INT PRIMARY KEY AUTO_INCREMENT,
	jatorria VARCHAR(150) NOT NULL,
	helmuga VARCHAR(150) NOT NULL,
	data DATE NOT NULL,
	ordua TIME NOT NULL,
	egoera VARCHAR(50) NOT NULL,
	id_gidariak INT NOT NULL,
	id_erreserba INT UNIQUE NOT NULL,
	FOREIGN KEY (id_gidariak) REFERENCES Gidariak(id_gidariak),
	FOREIGN KEY (id_erreserba) REFERENCES Erreserbak(id_erreserba)
);

CREATE TABLE Historikoa (
	id_historiala INT PRIMARY KEY AUTO_INCREMENT,
	data_amaiera DATE NOT NULL,
	ordua_bukaera TIME NOT NULL,
	jatorria VARCHAR(150) NOT NULL,
	helmuga VARCHAR(150) NOT NULL,
	id_bidaia INT UNIQUE NOT NULL,
	FOREIGN KEY (id_bidaia) REFERENCES Bidaiak(id_bidaia)
);
