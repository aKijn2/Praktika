package com.kudeaketa.alaiktomugi;

import javax.swing.*;
import javax.swing.border.EmptyBorder;
import javax.swing.event.DocumentEvent;
import javax.swing.event.DocumentListener;
import javax.swing.table.*;
import java.awt.*;
import java.sql.*;

public class GidariakIkusiPanela extends JFrame {

    private JTable table;
    private DefaultTableModel tableModel;
    private TableRowSorter<DefaultTableModel> sorter;
    private JTextField filterTextField;

    public GidariakIkusiPanela() {
        setTitle("Gidariak Ikusi");
        setSize(950, 500);
        setLocationRelativeTo(null);
        setDefaultCloseOperation(JFrame.DISPOSE_ON_CLOSE);

        initComponents();
        loadDataFromDatabase();
    }

    private void initComponents() {
        JPanel mainPanel = new JPanel(new BorderLayout(20, 20));
        mainPanel.setBackground(Color.WHITE);
        mainPanel.setBorder(new EmptyBorder(20, 20, 20, 20));
        setContentPane(mainPanel);

        // Título y filtro
        JPanel topPanel = new JPanel(new BorderLayout(15, 0));
        topPanel.setBackground(Color.WHITE);
        mainPanel.add(topPanel, BorderLayout.NORTH);

        JLabel titleLabel = new JLabel("GIDARIAK");
        titleLabel.setFont(new Font("Segoe UI", Font.BOLD, 30));
        titleLabel.setForeground(new Color(33, 47, 61));
        topPanel.add(titleLabel, BorderLayout.WEST);

        JPanel filterPanel = new JPanel(new BorderLayout());
        filterPanel.setBackground(Color.WHITE);
        filterPanel.setPreferredSize(new Dimension(300, 40));
        filterPanel.setBorder(BorderFactory.createLineBorder(new Color(46, 204, 113), 2)); // Verde
        topPanel.add(filterPanel, BorderLayout.EAST);

        JLabel iconLabel = new JLabel("\uD83D\uDD0D ");
        iconLabel.setFont(new Font("Segoe UI Symbol", Font.PLAIN, 20));
        iconLabel.setForeground(new Color(46, 204, 113)); // Verde
        iconLabel.setBorder(new EmptyBorder(0, 5, 0, 5));
        filterPanel.add(iconLabel, BorderLayout.WEST);

        filterTextField = new JTextField();
        filterTextField.setFont(new Font("Segoe UI", Font.PLAIN, 16));
        filterTextField.setBorder(BorderFactory.createEmptyBorder(5, 5, 5, 5));
        filterPanel.add(filterTextField, BorderLayout.CENTER);
        addPlaceholder(filterTextField, "Iragazi izena, nan, taxi matrikula...");

        // Tabla
        String[] columnNames = { "ID", "NAN", "Izena", "Abizena", "Helbidea", "Jaiotze data", "Emaila", "Telefonoa",
                "Erabiltzailea", "Taxi matrikula" };
        tableModel = new DefaultTableModel(columnNames, 0) {
            public boolean isCellEditable(int row, int col) {
                return false;
            }
        };
        table = new JTable(tableModel);
        table.setFont(new Font("Segoe UI", Font.PLAIN, 14));
        table.setRowHeight(28);
        table.setFillsViewportHeight(true);
        table.getTableHeader().setFont(new Font("Segoe UI", Font.BOLD, 16));
        table.getTableHeader().setBackground(new Color(46, 204, 113)); // Verde
        table.getTableHeader().setForeground(Color.WHITE);
        table.getTableHeader().setReorderingAllowed(false);

        // Alternancia de color
        table.setDefaultRenderer(Object.class, new DefaultTableCellRenderer() {
            private final Color evenColor = new Color(245, 245, 245);

            public Component getTableCellRendererComponent(JTable table, Object value,
                    boolean isSelected, boolean hasFocus,
                    int row, int column) {
                Component c = super.getTableCellRendererComponent(table, value, isSelected, hasFocus, row, column);
                if (!isSelected) {
                    c.setBackground(row % 2 == 0 ? Color.WHITE : evenColor);
                }
                return c;
            }
        });

        JScrollPane scrollPane = new JScrollPane(table);
        scrollPane.setBorder(BorderFactory.createLineBorder(new Color(189, 195, 199), 1));
        mainPanel.add(scrollPane, BorderLayout.CENTER);

        // Filtro dinámico
        sorter = new TableRowSorter<>(tableModel);
        table.setRowSorter(sorter);
        filterTextField.getDocument().addDocumentListener(new DocumentListener() {
            public void insertUpdate(DocumentEvent e) {
                filterTable();
            }

            public void removeUpdate(DocumentEvent e) {
                filterTable();
            }

            public void changedUpdate(DocumentEvent e) {
                filterTable();
            }
        });
    }

    private void filterTable() {
        String text = filterTextField.getText();
        if (text.trim().length() == 0 || text.equals("Iragazi izena, nan, taxi matrikula...")) {
            sorter.setRowFilter(null);
        } else {
            sorter.setRowFilter(RowFilter.regexFilter("(?i)" + text));
        }
    }

    private void addPlaceholder(JTextField textField, String placeholder) {
        textField.setForeground(Color.GRAY);
        textField.setText(placeholder);
        textField.addFocusListener(new java.awt.event.FocusAdapter() {
            public void focusGained(java.awt.event.FocusEvent e) {
                if (textField.getText().equals(placeholder)) {
                    textField.setText("");
                    textField.setForeground(Color.BLACK);
                }
            }

            public void focusLost(java.awt.event.FocusEvent e) {
                if (textField.getText().isEmpty()) {
                    textField.setForeground(Color.GRAY);
                    textField.setText(placeholder);
                }
            }
        });
    }

    private void loadDataFromDatabase() {
        tableModel.setRowCount(0);
        try (Connection conn = konexioa.getConnection()) {
            String sql = "SELECT id_gidaria, nan, izena, abizena, helbidea, jaiotze_data, emaila, telefonoa, erabiltzailea, taxi_matrikula FROM gidaria";
            PreparedStatement ps = conn.prepareStatement(sql);
            ResultSet rs = ps.executeQuery();

            while (rs.next()) {
                Object[] row = {
                        rs.getInt("id_gidaria"),
                        rs.getString("nan"),
                        rs.getString("izena"),
                        rs.getString("abizena"),
                        rs.getString("helbidea"),
                        rs.getDate("jaiotze_data"),
                        rs.getString("emaila"),
                        rs.getString("telefonoa"),
                        rs.getString("erabiltzailea"),
                        rs.getString("taxi_matrikula")
                };
                tableModel.addRow(row);
            }
        } catch (SQLException e) {
            JOptionPane.showMessageDialog(this, "Errorea gidariak kargatzean: " + e.getMessage(),
                    "Errorea", JOptionPane.ERROR_MESSAGE);
        }
    }
}
