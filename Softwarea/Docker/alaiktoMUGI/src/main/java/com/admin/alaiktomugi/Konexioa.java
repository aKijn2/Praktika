package com.admin.alaiktomugi;

import java.sql.Connection;
import java.sql.DriverManager;
import java.sql.SQLException;

public class Konexioa {
    private static final String HOST = "localhost";
    private static final String DB = "alaiktomugi";
    private static final String USER = "root";
    private static final String PASS = "mysql";
    private static final String URL = "jdbc:mysql://" + HOST + ":3307/" + DB + "?useSSL=false&serverTimezone=UTC";

    public static Connection getConnection() throws SQLException {
        return DriverManager.getConnection(URL, USER, PASS);
    }
}
