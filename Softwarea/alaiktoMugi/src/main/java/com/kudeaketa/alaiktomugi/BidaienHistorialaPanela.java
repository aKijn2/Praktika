package com.kudeaketa.alaiktomugi;

import javax.swing.*;
import javax.swing.border.EmptyBorder;
import javax.swing.event.DocumentEvent;
import javax.swing.event.DocumentListener;
import javax.swing.table.DefaultTableCellRenderer;
import javax.swing.table.DefaultTableModel;
import javax.swing.table.TableRowSorter;
import java.awt.*;
import java.sql.*;

public class BidaienHistorialaPanela extends JFrame {

    private JTable table;
    private DefaultTableModel tableModel;
    private TableRowSorter<DefaultTableModel> sorter;
    private JTextField filterTextField;

    public BidaienHistorialaPanela() {
        setTitle("Bidaien Historiala");
        setSize(820, 520);
        setLocationRelativeTo(null);
        setDefaultCloseOperation(JFrame.DISPOSE_ON_CLOSE);

        initComponents();
        loadDataFromDatabase();
    }

    private void initComponents() {
        JPanel mainPanel = new JPanel(new BorderLayout(20, 20));
        mainPanel.setBorder(new EmptyBorder(20, 20, 20, 20));
        mainPanel.setBackground(Color.WHITE);
        setContentPane(mainPanel);

        // Panel superior para título y filtro con espacio entre ellos
        JPanel topPanel = new JPanel(new BorderLayout(15, 0));
        topPanel.setBackground(Color.WHITE);
        mainPanel.add(topPanel, BorderLayout.NORTH);

        // Título
        JLabel titleLabel = new JLabel("BIDAIAREN HISTORIALA");
        titleLabel.setFont(new Font("Segoe UI", Font.BOLD, 30));
        titleLabel.setForeground(new Color(33, 47, 61));
        titleLabel.setHorizontalAlignment(SwingConstants.LEFT);
        topPanel.add(titleLabel, BorderLayout.WEST);

        // Panel filtro con icono
        JPanel filterPanel = new JPanel(new BorderLayout());
        filterPanel.setBackground(Color.WHITE);
        filterPanel.setPreferredSize(new Dimension(300, 40));
        filterPanel.setBorder(BorderFactory.createLineBorder(new Color(46, 204, 113), 2)); // Verde
        topPanel.add(filterPanel, BorderLayout.EAST);

        // Icono lupa (usando unicode)
        JLabel iconLabel = new JLabel("\uD83D\uDD0D "); // lupa emoji
        iconLabel.setFont(new Font("Segoe UI Symbol", Font.PLAIN, 20));
        iconLabel.setForeground(new Color(46, 204, 113)); // Verde
        iconLabel.setBorder(new EmptyBorder(0, 5, 0, 5));
        filterPanel.add(iconLabel, BorderLayout.WEST);

        // Campo filtro con hint
        filterTextField = new JTextField();
        filterTextField.setFont(new Font("Segoe UI", Font.PLAIN, 16));
        filterTextField.setBorder(BorderFactory.createEmptyBorder(5, 5, 5, 5));
        filterTextField.setToolTipText("Idatzi iragazi nahi duzun testua");
        filterPanel.add(filterTextField, BorderLayout.CENTER);
        addPlaceholder(filterTextField, "Iragazi izen, jatorri edo helmuga...");

        // Tabla
        String[] columnNames = { "ID Historikoa", "Amaiera Data", "Amaiera Ordua", "Jatorria", "Helmuga", "ID Bidaia" };
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

        // Filas alternas para legibilidad
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

        // Scroll pane con borde sutil
        JScrollPane scrollPane = new JScrollPane(table);
        scrollPane.setBorder(BorderFactory.createLineBorder(new Color(189, 195, 199), 1));
        mainPanel.add(scrollPane, BorderLayout.CENTER);

        // Ordenador para tabla
        sorter = new TableRowSorter<>(tableModel);
        table.setRowSorter(sorter);

        // Filtro en tiempo real con DocumentListener para mejor UX
        filterTextField.getDocument().addDocumentListener(new javax.swing.event.DocumentListener() {
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
        if (text.trim().length() == 0) {
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
        clearTable();
        try (Connection conn = konexioa.getConnection()) {
            String sql = "SELECT id_historikoa, amaiera_data, amaiera_ordua, jatorria, helmuga, bidaia_id_bidaia " +
                    "FROM historikoa ORDER BY amaiera_data DESC, amaiera_ordua DESC";
            PreparedStatement ps = conn.prepareStatement(sql);
            ResultSet rs = ps.executeQuery();

            while (rs.next()) {
                Object[] row = {
                        rs.getInt("id_historikoa"),
                        rs.getString("amaiera_data"),
                        rs.getString("amaiera_ordua"),
                        rs.getString("jatorria"),
                        rs.getString("helmuga"),
                        rs.getInt("bidaia_id_bidaia")
                };
                tableModel.addRow(row);
            }
        } catch (SQLException e) {
            JOptionPane.showMessageDialog(this,
                    "Errorea datu-basearekin: " + e.getMessage(),
                    "Errorea",
                    JOptionPane.ERROR_MESSAGE);
        }
    }

    private void clearTable() {
        tableModel.setRowCount(0);
    }
}
