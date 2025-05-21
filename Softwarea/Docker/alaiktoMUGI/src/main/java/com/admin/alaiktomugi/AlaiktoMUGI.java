package com.admin.alaiktomugi;

public class AlaiktoMUGI {

    public static void main(String[] args) {
        try {
            java.sql.Connection conn = Konexioa.getConnection();
            if (conn != null && !conn.isClosed()) {
                System.out.println("Conexion exitosa a la base de datos.");
                conn.close();
            } else {
                System.out.println("No se pudo conectar a la base de datos.");
            }
        } catch (Exception e) {
            System.out.println("Error al conectar: " + e.getMessage());
        }
    }
}
