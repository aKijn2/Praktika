package com.kudeaketa.alaiktomugi;

import javax.swing.*;
import javax.swing.border.EmptyBorder;
import java.awt.*;
import java.awt.event.FocusAdapter;
import java.awt.event.FocusEvent;
import java.sql.*;

public class LoginPanela extends JFrame {

    private JTextField sartuEmailaTextField;
    private JPasswordField sartuPasahitzaTextField;
    private JButton saioaHasiButton;

    public LoginPanela() {
        initComponents();
    }

    private void initComponents() {
        setTitle("AlaikToMugi - Login");
        setDefaultCloseOperation(JFrame.EXIT_ON_CLOSE);
        setResizable(false);
        setSize(400, 500);
        setLocationRelativeTo(null);

        // Fondo gris muy claro
        Color fondo = new Color(245, 247, 250);
        getContentPane().setBackground(fondo);
        setLayout(new GridBagLayout());

        // Panel blanco central con borde redondeado y mismo color de fondo (sin sombra)
        JPanel formPanel = new JPanel() {
            @Override
            protected void paintComponent(Graphics g) {
                super.paintComponent(g);
                Graphics2D g2 = (Graphics2D) g.create();
                g2.setRenderingHint(RenderingHints.KEY_ANTIALIASING, RenderingHints.VALUE_ANTIALIAS_ON);
                g2.setColor(fondo);
                g2.fillRoundRect(0, 0, getWidth(), getHeight(), 25, 25);
                g2.dispose();
            }
        };
        formPanel.setOpaque(false);
        formPanel.setPreferredSize(new Dimension(340, 400));
        formPanel.setLayout(new BoxLayout(formPanel, BoxLayout.Y_AXIS));
        formPanel.setBorder(new EmptyBorder(40, 40, 40, 40));

        add(formPanel);

        // Logo centrado
        JLabel logoLabel = new JLabel();
        ImageIcon logoIcon = new ImageIcon("img/logo.jpg");
        Image scaledLogo = logoIcon.getImage().getScaledInstance(80, 80, Image.SCALE_SMOOTH);
        logoLabel.setIcon(new ImageIcon(scaledLogo));
        logoLabel.setAlignmentX(Component.CENTER_ALIGNMENT);
        formPanel.add(logoLabel);

        formPanel.add(Box.createVerticalStrut(30));

        // Email Field con placeholder
        sartuEmailaTextField = createUnderlinedField("Erabiltzailea (Emaila)");
        formPanel.add(sartuEmailaTextField);
        formPanel.add(Box.createVerticalStrut(25));

        // Password Field con placeholder
        sartuPasahitzaTextField = new JPasswordField();
        sartuPasahitzaTextField.setFont(new Font("Segoe UI", Font.PLAIN, 14));
        sartuPasahitzaTextField.setBorder(BorderFactory.createMatteBorder(0, 0, 2, 0, new Color(46, 204, 113)));
        sartuPasahitzaTextField.setForeground(Color.GRAY);
        sartuPasahitzaTextField.setEchoChar((char)0); // Mostrar placeholder al inicio
        sartuPasahitzaTextField.setText("Pasahitza");
        sartuPasahitzaTextField.setAlignmentX(Component.CENTER_ALIGNMENT);
        sartuPasahitzaTextField.addFocusListener(new FocusAdapter() {
            public void focusGained(FocusEvent e) {
                if (String.valueOf(sartuPasahitzaTextField.getPassword()).equals("Pasahitza")) {
                    sartuPasahitzaTextField.setText("");
                    sartuPasahitzaTextField.setForeground(Color.BLACK);
                    sartuPasahitzaTextField.setEchoChar('●');
                }
            }

            public void focusLost(FocusEvent e) {
                if (String.valueOf(sartuPasahitzaTextField.getPassword()).isEmpty()) {
                    sartuPasahitzaTextField.setEchoChar((char)0);
                    sartuPasahitzaTextField.setText("Pasahitza");
                    sartuPasahitzaTextField.setForeground(Color.GRAY);
                }
            }
        });
        formPanel.add(sartuPasahitzaTextField);

        formPanel.add(Box.createVerticalStrut(40));

        // Botón con efecto hover
        saioaHasiButton = new JButton("Saioa Hasi");
        saioaHasiButton.setFont(new Font("Segoe UI", Font.BOLD, 16));
        saioaHasiButton.setForeground(Color.WHITE);
        saioaHasiButton.setBackground(new Color(46, 204, 113));
        saioaHasiButton.setFocusPainted(false);
        saioaHasiButton.setCursor(new Cursor(Cursor.HAND_CURSOR));
        saioaHasiButton.setAlignmentX(Component.CENTER_ALIGNMENT);
        saioaHasiButton.setBorder(BorderFactory.createEmptyBorder(12, 50, 12, 50));

        // Hover effect
        saioaHasiButton.addMouseListener(new java.awt.event.MouseAdapter() {
            public void mouseEntered(java.awt.event.MouseEvent evt) {
                saioaHasiButton.setBackground(new Color(39, 174, 96));
            }
            public void mouseExited(java.awt.event.MouseEvent evt) {
                saioaHasiButton.setBackground(new Color(46, 204, 113));
            }
        });

        saioaHasiButton.addActionListener(evt -> loginAdministratzailea());
        formPanel.add(saioaHasiButton);
    }

    private JTextField createUnderlinedField(String placeholder) {
        JTextField field = new JTextField();
        field.setFont(new Font("Segoe UI", Font.PLAIN, 14));
        field.setForeground(Color.GRAY);
        field.setText(placeholder);
        field.setBorder(BorderFactory.createMatteBorder(0, 0, 2, 0, new Color(46, 204, 113)));
        field.setAlignmentX(Component.CENTER_ALIGNMENT);

        field.addFocusListener(new FocusAdapter() {
            public void focusGained(FocusEvent e) {
                if (field.getText().equals(placeholder)) {
                    field.setText("");
                    field.setForeground(Color.BLACK);
                }
            }
            public void focusLost(FocusEvent e) {
                if (field.getText().isEmpty()) {
                    field.setForeground(Color.GRAY);
                    field.setText(placeholder);
                }
            }
        });
        return field;
    }

    private void loginAdministratzailea() {
        String emaila = sartuEmailaTextField.getText().trim();
        String pasahitza = new String(sartuPasahitzaTextField.getPassword());

        if (emaila.isEmpty() || emaila.equals("Erabiltzailea (Emaila)") ||
            pasahitza.isEmpty() || pasahitza.equals("Pasahitza")) {
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
}
