package com.kudeaketa.alaiktomugi;

import java.sql.Connection;
import java.sql.SQLException;

import javax.swing.SwingUtilities;
import javax.swing.UIManager;

public class main {
    public static void main(String[] args) {
        System.out.println("Probando conexión a la base de datos...");

        try (Connection conn = konexioa.getConnection()) {
            if (conn != null && !conn.isClosed()) {
                System.out.println("¡Conexión establecida con éxito!");
            } else {
                System.out.println("No se pudo establecer la conexión.");
            }
        } catch (SQLException e) {
            System.out.println("Error al conectar con la base de datos:");
            e.printStackTrace();
        }

        SwingUtilities.invokeLater(() -> {
            try {
                for (UIManager.LookAndFeelInfo info : UIManager.getInstalledLookAndFeels()) {
                    if ("Nimbus".equals(info.getName())) {
                        UIManager.setLookAndFeel(info.getClassName());
                        break;
                    }
                }
            } catch (Exception ignored) {
            }
            new LoginPanela().setVisible(true);
        });
    }
}
