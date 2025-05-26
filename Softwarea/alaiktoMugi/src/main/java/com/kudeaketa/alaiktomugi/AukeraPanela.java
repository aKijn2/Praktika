package com.kudeaketa.alaiktomugi;

import javax.swing.*;
import java.awt.*;

public class AukeraPanela extends javax.swing.JFrame {

    public AukeraPanela() {
        initComponents();
    }

    private void initComponents() {
        setTitle("Aukera Panela");
        setDefaultCloseOperation(javax.swing.WindowConstants.EXIT_ON_CLOSE);
        setSize(500, 400);
        setLocationRelativeTo(null);

        // Panel principal
        JPanel mainPanel = new JPanel(new GridBagLayout());
        mainPanel.setBackground(new Color(245, 245, 245));
        GridBagConstraints gbc = new GridBagConstraints();
        gbc.insets = new Insets(10, 20, 10, 20);
        gbc.fill = GridBagConstraints.HORIZONTAL;
        gbc.gridx = 0;

        // Título
        JLabel titleLabel = new JLabel("AUKERA PANELA");
        titleLabel.setFont(new Font("Segoe UI", Font.BOLD, 26));
        titleLabel.setForeground(new Color(52, 73, 94));
        titleLabel.setHorizontalAlignment(SwingConstants.CENTER);
        gbc.gridy = 0;
        gbc.gridwidth = 1;
        gbc.anchor = GridBagConstraints.CENTER;
        mainPanel.add(titleLabel, gbc);

        // Botón para añadir/eliminar gidariak
        JButton gidariakAltanEmanBtn = createStyledButton("Gidariak altan eman / Ezabatu");
        gidariakAltanEmanBtn.addActionListener(e -> new GidariakAltanEmanPanela().setVisible(true));
        gbc.gridy++;
        mainPanel.add(gidariakAltanEmanBtn, gbc);

        // Botón para ver y actualizar gidariak
        JButton gidariakIkusiEguneratuBtn = createStyledButton("Gidariak ikusi / Eguneratu");
        gidariakIkusiEguneratuBtn.addActionListener(e -> new GidariakIkusiEtaEguneratuPanela().setVisible(true));
        gbc.gridy++;
        mainPanel.add(gidariakIkusiEguneratuBtn, gbc);

        // Botón para ver bezeroak
        JButton bezeroakIkusiBtn = createStyledButton("Bezeroak ikusi");
        bezeroakIkusiBtn.addActionListener(e -> new BezeroakIkusiPanela().setVisible(true));
        gbc.gridy++;
        mainPanel.add(bezeroakIkusiBtn, gbc);

        // Botón para ver historial de viajes
        JButton bidaienHistorialaBtn = createStyledButton("Bidaien historiala ikusi");
        bidaienHistorialaBtn.addActionListener(e -> new BidaienHistorialaPanela().setVisible(true));
        gbc.gridy++;
        mainPanel.add(bidaienHistorialaBtn, gbc);

        getContentPane().add(mainPanel);
    }

    private JButton createStyledButton(String text) {
        JButton button = new JButton(text);
        button.setFont(new Font("Segoe UI", Font.PLAIN, 16));
        button.setBackground(new Color(46, 204, 113));
        button.setForeground(Color.WHITE);
        button.setFocusPainted(false);
        button.setBorder(BorderFactory.createEmptyBorder(10, 20, 10, 20));
        return button;
    }

    public static void main(String args[]) {
        SwingUtilities.invokeLater(() -> new AukeraPanela().setVisible(true));
    }
}
