/**
 * @author IKER HERNÁNDEZ - ACHRAF ALLACH
 * 
 * Datu basearekin konektatzeko klasea.
 * MySQL zerbitzarira konexioa ezartzen du, aplikazioaren datuak kudeatzeko.
 */
package com.kudeaketa.alaiktomugi;

import java.sql.Connection;
import java.sql.DriverManager;
import java.sql.SQLException;

/**
 * Datu basearekin konektatzeko klasea.
 * Konexio objektua eskaintzen du MySQL zerbitzarira.
 */
public class konexioa {

    /** Zerbitzariaren helbidea */
    private static final String HOST = "localhost";

    /** Datu-basearen izena */
    private static final String DB = "alaiktomugi";

    /** Erabiltzailearen izena */
    private static final String USER = "root";

    /** Pasahitza */
    private static final String PASS = "mysql";

    /** Konexio URL-a MySQL zerbitzarira konektatzeko */
    private static final String URL = "jdbc:mysql://" + HOST + ":3307/" + DB + "?useSSL=false&serverTimezone=UTC";

    /**
     * Datu basearekin konexioa ezartzen du.
     * 
     * @return MySQL datu basearekin konexioa.
     * @throws SQLException Konexioa ezartzean errorea gertatuz gero.
     */
    public static Connection getConnection() throws SQLException {
        return DriverManager.getConnection(URL, USER, PASS);
    }
}
