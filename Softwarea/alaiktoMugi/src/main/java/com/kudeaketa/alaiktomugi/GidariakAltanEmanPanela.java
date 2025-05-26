package com.kudeaketa.alaiktomugi;

import javax.swing.*;
import javax.swing.border.EmptyBorder;
import java.awt.*;
import java.sql.*;

public class GidariakAltanEmanPanela extends JFrame {
    private JTextField[] inputFields = new JTextField[10];
    private final String[] labels = {
        "NAN", "Izena", "Abizena", "Helbidea", "Jaiotze Data (YYYY-MM-DD)", "Emaila",
        "Telefonoa", "Pasahitza", "Erabiltzailea", "Taxi Matrikula"
    };

    public GidariakAltanEmanPanela() {
        setTitle("Gidariak Gehitu");
        setSize(600, 330);
        setLocationRelativeTo(null);
        setDefaultCloseOperation(JFrame.DISPOSE_ON_CLOSE);

        JPanel mainPanel = new JPanel(new BorderLayout(20, 20));
        mainPanel.setBorder(new EmptyBorder(20, 20, 20, 20));
        mainPanel.setBackground(Color.WHITE);
        setContentPane(mainPanel);

        JLabel titleLabel = new JLabel("GIDARIAK GEHITU");
        titleLabel.setFont(new Font("Segoe UI", Font.BOLD, 26));
        titleLabel.setForeground(new Color(46, 204, 113));
        titleLabel.setHorizontalAlignment(SwingConstants.CENTER);
        mainPanel.add(titleLabel, BorderLayout.NORTH);

        JPanel formPanel = new JPanel(new GridLayout(5, 4, 10, 10));
        formPanel.setBackground(Color.WHITE);
        for (int i = 0; i < labels.length; i++) {
            JLabel lbl = new JLabel(labels[i] + ":");
            lbl.setFont(new Font("Segoe UI", Font.PLAIN, 14));
            inputFields[i] = new JTextField();
            inputFields[i].setFont(new Font("Segoe UI", Font.PLAIN, 14));
            inputFields[i].setBorder(BorderFactory.createLineBorder(new Color(46, 204, 113), 1));
            formPanel.add(lbl);
            formPanel.add(inputFields[i]);
        }
        mainPanel.add(formPanel, BorderLayout.CENTER);

        JPanel bottomPanel = new JPanel(new FlowLayout(FlowLayout.CENTER, 15, 0));
        bottomPanel.setBackground(Color.WHITE);

        JButton addButton = createGreenButton("Gehitu Gidaria");
        JButton openManageButton = createGreenButton("Kudeaketa Ireki");
        bottomPanel.add(addButton);
        bottomPanel.add(openManageButton);

        mainPanel.add(bottomPanel, BorderLayout.SOUTH);

        // Listeners
        addButton.addActionListener(e -> insertGidaria());
        openManageButton.addActionListener(e -> {
            GidariakIkusiEtaEguneratuPanela frame = new GidariakIkusiEtaEguneratuPanela();
            frame.setVisible(true);
        });
    }

    private JButton createGreenButton(String text) {
        JButton button = new JButton(text);
        button.setBackground(new Color(46, 204, 113));
        button.setForeground(Color.WHITE);
        button.setFocusPainted(false);
        button.setFont(new Font("Segoe UI", Font.BOLD, 14));
        button.setBorder(BorderFactory.createEmptyBorder(8, 20, 8, 20));
        return button;
    }

    private void insertGidaria() {
        // Validar que ningún campo esté vacío
        for (int i = 0; i < inputFields.length; i++) {
            if (inputFields[i].getText().trim().isEmpty()) {
                JOptionPane.showMessageDialog(this, labels[i] + " ezin da hutsik egon.", "Errorea", JOptionPane.ERROR_MESSAGE);
                return;
            }
        }

        try (Connection conn = konexioa.getConnection()) {
            String sql = "INSERT INTO gidaria (nan, izena, abizena, helbidea, jaiotze_data, emaila, telefonoa, pasahitza, erabiltzailea, taxi_matrikula) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            PreparedStatement ps = conn.prepareStatement(sql);
            for (int i = 0; i < 10; i++) {
                ps.setString(i + 1, inputFields[i].getText().trim());
            }
            ps.executeUpdate();
            JOptionPane.showMessageDialog(this, "Gidaria ongi gehitu da!", "Arrakasta", JOptionPane.INFORMATION_MESSAGE);
            clearForm();
        } catch (SQLException e) {
            JOptionPane.showMessageDialog(this, "Errorea gehitzerakoan: " + e.getMessage(), "Errorea", JOptionPane.ERROR_MESSAGE);
        }
    }

    private void clearForm() {
        for (JTextField f : inputFields) {
            f.setText("");
        }
    }
}
