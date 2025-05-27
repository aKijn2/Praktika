/**
 * @author IKER HERNÁNDEZ - ACHRAF ALLACH
 * 
 * Programaren abiarazpena egiten duen klasea.
 * Datu-basearen konexioa probatzen du eta interfazea bistaratzen du.
 */
package com.kudeaketa.alaiktomugi;

import java.sql.Connection;
import java.sql.SQLException;

import javax.swing.SwingUtilities;
import javax.swing.UIManager;

/**
 * Programa abiarazteko klase nagusia.
 * Datu-basearekin konexioa egiaztatzen du eta interfazea hasieratzen du.
 */
public class main {
    /**
     * Programaren sarrera-puntua.
     * @param args Komando-lerroko argumentuak.
     */
    public static void main(String[] args) {
        System.out.println("Datu-basearekiko konexioa probatzen...");

        try (Connection conn = konexioa.getConnection()) {
            if (conn != null && !conn.isClosed()) {
                System.out.println("Konexioa ongi ezarri da!");
            } else {
                System.out.println("Ezin izan da konexioa ezarri.");
            }
        } catch (SQLException e) {
            System.out.println("Errorea datu-basearekin konektatzean:");
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
