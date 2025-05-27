/**
 * @author IKER HERNÁNDEZ - ACHRAF ALLACH
 * 
 * Erabiltzaileak saioa hasteko leiho grafikoa.
 * Administratzailea autentifikatzeko interfazea eskaintzen du.
 */
package com.kudeaketa.alaiktomugi;

import java.awt.BorderLayout;
import java.awt.Color;
import java.awt.Dimension;
import java.awt.FlowLayout;
import java.awt.Font;
import java.awt.GridLayout;
import java.awt.Image;
import java.net.URL;
import java.sql.Connection;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import java.sql.SQLException;

import javax.swing.ImageIcon;
import javax.swing.JButton;
import javax.swing.JFrame;
import javax.swing.JLabel;
import javax.swing.JOptionPane;
import javax.swing.JPanel;
import javax.swing.JPasswordField;
import javax.swing.JTextField;
import javax.swing.SwingUtilities;
import javax.swing.border.EmptyBorder;

/**
 * LoginPanela klasea: Administratzaileak saioa hasteko interfaze grafikoa.
 * Erabiltzaileak posta elektronikoa eta pasahitza sartu behar ditu.
 */
public class LoginPanela extends JFrame {

    /** Erabiltzailearen posta elektronikoa gordetzeko eremua */
    private JTextField emailField;

    /** Erabiltzailearen pasahitza gordetzeko eremua */
    private JPasswordField passwordField;

    /**
     * Eraikitzailea: Saioa hasteko interfaze grafikoa inicializatzen du.
     */
    public LoginPanela() {
        initComponents();
    }

    /**
     * Interfaze grafikoaren osagaiak inicializatzen ditu.
     */
    private void initComponents() {
        setTitle("AlaiktoMUGI - Login"); // Leihoaren izenburua
        setDefaultCloseOperation(JFrame.EXIT_ON_CLOSE);
        setResizable(false);
        setSize(500, 400);
        setLocationRelativeTo(null);

        JPanel contentPanel = new JPanel(new BorderLayout());
        contentPanel.setBackground(Color.WHITE);
        contentPanel.setBorder(new EmptyBorder(20, 20, 20, 20));
        setContentPane(contentPanel);

     // Logoaren panela sortu
        JPanel logoPanel = new JPanel();
        // Atzeko kolorea zuria jarri
        logoPanel.setBackground(Color.WHITE);

        // JLabel deklaratu, irudia edo testua jartzeko
        JLabel logoLabel;

        // Irudiaren URL-a lortu klasearen baliabideetatik
        URL imageUrl = getClass().getResource("/irudiak/logo1.png");
        if (imageUrl != null) {
            // Irudia kargatu
            ImageIcon originalIcon = new ImageIcon(imageUrl);
            // Irudia tamaina egokian eskalatu (200x100)
            Image scaledImage = originalIcon.getImage().getScaledInstance(200, 100, Image.SCALE_SMOOTH);
            // Eskalatua den irudia ImageIcon bihurtu
            ImageIcon scaledIcon = new ImageIcon(scaledImage);
            // JLabel-ean irudia jarri
            logoLabel = new JLabel(scaledIcon);
        } else {
            // Irudia aurkitu ez badu, errore-mezua idatzi kontsolan
            System.err.println("Ez da aurkitu /irudiak/logo1.png irudia");
            // Eta testu bat jarri JLabel-ean
            logoLabel = new JLabel("Irudia aurkitu ez da");
        }

        // JLabel panela gehitu
        logoPanel.add(logoLabel);
        // Logo panela edukia duen panela (contentPanel) ipini goialdean
        contentPanel.add(logoPanel, BorderLayout.NORTH);


        // Inprimaki-panela
        JPanel formPanel = new JPanel();
        formPanel.setBackground(Color.WHITE);
        formPanel.setLayout(new GridLayout(4, 1, 10, 10));

        JLabel emailLabel = new JLabel("POSTA:");
        emailLabel.setFont(new Font("Segoe UI", Font.BOLD, 14));
        formPanel.add(emailLabel);

        emailField = new JTextField();
        emailField.setFont(new Font("Segoe UI", Font.PLAIN, 14));
        formPanel.add(emailField);

        JLabel passwordLabel = new JLabel("PASAHITZA:");
        passwordLabel.setFont(new Font("Segoe UI", Font.BOLD, 14));
        passwordLabel.setPreferredSize(new Dimension(100, 500));
        formPanel.add(passwordLabel);

        passwordField = new JPasswordField();
        passwordField.setFont(new Font("Segoe UI", Font.PLAIN, 14));
        formPanel.add(passwordField);

        contentPanel.add(formPanel, BorderLayout.CENTER);

        // Botoien panela
        JPanel buttonPanel = new JPanel();
        buttonPanel.setBackground(Color.WHITE);
        buttonPanel.setLayout(new FlowLayout(FlowLayout.CENTER, 30, 20));

        JButton hasiButton = new JButton("HASI");
        hasiButton.setBackground(new Color(46, 204, 113));
        hasiButton.setForeground(Color.WHITE);
        hasiButton.setFont(new Font("Segoe UI", Font.BOLD, 14));
        hasiButton.setFocusPainted(false);
        hasiButton.setPreferredSize(new Dimension(100, 40));
        hasiButton.addActionListener(e -> loginAdministratzailea());
        buttonPanel.add(hasiButton);

        JButton itxiButton = new JButton("ITXI");
        itxiButton.setBackground(new Color(231, 76, 60));
        itxiButton.setForeground(Color.WHITE);
        itxiButton.setFont(new Font("Segoe UI", Font.BOLD, 14));
        itxiButton.setFocusPainted(false);
        itxiButton.setPreferredSize(new Dimension(100, 40));
        itxiButton.addActionListener(e -> System.exit(0));
        buttonPanel.add(itxiButton);

        contentPanel.add(buttonPanel, BorderLayout.SOUTH);
    }

    /**
     * Administratzailea autentifikatzen du datu-basean.
     * Emaila eta pasahitza egiaztatzen ditu.
     */
    private void loginAdministratzailea() {
        String emaila = emailField.getText().trim();
        String pasahitza = new String(passwordField.getPassword());

        // Eremuak hutsik dauden egiaztatu
        if (emaila.isEmpty() || pasahitza.isEmpty()) {
            JOptionPane.showMessageDialog(this, "Mesedez, bete eremu guztiak.", "Informazioa",
                    JOptionPane.INFORMATION_MESSAGE);
            return;
        }

        try (Connection conn = konexioa.getConnection()) {
            String sql = "SELECT * FROM administratzailea WHERE emaila = ? AND pasahitza = ?";
            PreparedStatement stmt = conn.prepareStatement(sql);
            stmt.setString(1, emaila);
            stmt.setString(2, pasahitza);
            ResultSet rs = stmt.executeQuery();

            if (rs.next()) {
                JOptionPane.showMessageDialog(this, "Ongi etorri, " + rs.getString("izena") + "!");
                this.setVisible(false);
                new AukeraPanela().setVisible(true);
            } else {
                JOptionPane.showMessageDialog(this, "Emaila edo pasahitza okerra!", "Errorea",
                        JOptionPane.ERROR_MESSAGE);
            }

        } catch (SQLException ex) {
            JOptionPane.showMessageDialog(this, "Errorea datu-basearekin: " + ex.getMessage(), "Errorea",
                    JOptionPane.ERROR_MESSAGE);
        }
    }

    /**
     * Programa abiarazten du eta LoginPanela leihoa bistaratzen du.
     * @param args Komando-lerroko argumentuak.
     */
    public static void main(String[] args) {
        SwingUtilities.invokeLater(() -> new LoginPanela().setVisible(true));
    }
}
