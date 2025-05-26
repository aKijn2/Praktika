package com.alaiktomugi;

import javax.swing.*;
import javax.swing.table.DefaultTableModel;
import java.sql.*;

public class KudeaketaPanela extends JFrame {

    private JTable tabla = new JTable();
    private JButton historialaButton = new JButton("Historiala");

    public KudeaketaPanela() {
        setTitle("Kudeaketa Panela");
        setSize(800, 600);
        setDefaultCloseOperation(EXIT_ON_CLOSE);

        historialaButton.addActionListener(e -> cargarHistorial());

        JPanel panel = new JPanel();
        panel.add(historialaButton);
        add(panel, "North");
        add(new JScrollPane(tabla), "Center");
    }

    private void cargarHistorial() {
        try (Connection conn = Konexioa.getConnection();
                PreparedStatement stmt = conn.prepareStatement(
                        "SELECT h.id_historikoa, h.amaiera_data, h.amaiera_ordua, " +
                                "h.jatorria, h.helmuga, b.data, b.ordua, b.egoera " +
                                "FROM historikoa h JOIN bidaia b ON h.bidaia_id_bidaia = b.id_bidaia");
                ResultSet rs = stmt.executeQuery()) {

            DefaultTableModel model = new DefaultTableModel();
            model.addColumn("ID");
            model.addColumn("Amaiera Data");
            model.addColumn("Amaiera Ordua");
            model.addColumn("Jatorria");
            model.addColumn("Helmuga");
            model.addColumn("Hasiera Data");
            model.addColumn("Hasiera Ordua");
            model.addColumn("Egoera");

            while (rs.next()) {
                model.addRow(new Object[] {
                        rs.getInt("id_historikoa"),
                        rs.getString("amaiera_data"),
                        rs.getString("amaiera_ordua"),
                        rs.getString("jatorria"),
                        rs.getString("helmuga"),
                        rs.getString("data"),
                        rs.getString("ordua"),
                        rs.getString("egoera")
                });
            }

            tabla.setModel(model);

        } catch (SQLException ex) {
            JOptionPane.showMessageDialog(this, "Error cargando historial: " + ex.getMessage());
        }
    }

    public static void main(String[] args) {
        SwingUtilities.invokeLater(() -> new KudeaketaPanela().setVisible(true));
    }
}
