/**
 * @author IKER HERNÁNDEZ - ACHRAF ALLACH
 * @version 1.0
 *
 * BezeroakIkusiPanela klaseak bezeroen datuak bistaratzen ditu taula batean,
 * eta erabiltzaileari aukera ematen dio iragazteko izenaren, emailaren edo NANaren arabera.
 * Datuak datu-basetik kargatzen dira eta interfaz grafiko profesional bat eskaintzen da.
 */

package com.kudeaketa.alaiktomugi;

import java.awt.BorderLayout;
import java.awt.Color;
import java.awt.Component;
import java.awt.Dimension;
import java.awt.Font;
import java.sql.Connection;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import java.sql.SQLException;

import javax.swing.BorderFactory;
import javax.swing.JFrame;
import javax.swing.JLabel;
import javax.swing.JOptionPane;
import javax.swing.JPanel;
import javax.swing.JScrollPane;
import javax.swing.JTable;
import javax.swing.JTextField;
import javax.swing.RowFilter;
import javax.swing.border.EmptyBorder;
import javax.swing.event.DocumentEvent;
import javax.swing.event.DocumentListener;
import javax.swing.table.DefaultTableCellRenderer;
import javax.swing.table.DefaultTableModel;
import javax.swing.table.TableRowSorter;

/**
 * JFrame baten hedapena egiten duen klasea, bezeroen informazioa taula batean erakusteko.
 */
public class BezeroakIkusiPanela extends JFrame {

    private JTable table;
    private DefaultTableModel tableModel;
    private TableRowSorter<DefaultTableModel> sorter;
    private JTextField filterTextField;

    /**
     * BezeroakIkusiPanela eraikitzailea. Interfazea hasieratu eta datuak kargatzen ditu.
     */
    public BezeroakIkusiPanela() {
        setTitle("Bezeroak Ikusi");
        setSize(850, 500);
        setLocationRelativeTo(null);
        setDefaultCloseOperation(JFrame.DISPOSE_ON_CLOSE);

        initComponents();
        loadDataFromDatabase();
    }

    /**
     * Interfazeko osagaiak sortu eta antolatzen ditu.
     */
    private void initComponents() {
        JPanel mainPanel = new JPanel(new BorderLayout(20, 20));
        mainPanel.setBackground(Color.WHITE);
        mainPanel.setBorder(new EmptyBorder(20, 20, 20, 20));
        setContentPane(mainPanel);

        // Goiko panela: titulua eta bilaketa-iragazkia
        JPanel topPanel = new JPanel(new BorderLayout(15, 0));
        topPanel.setBackground(Color.WHITE);
        mainPanel.add(topPanel, BorderLayout.NORTH);

        JLabel titleLabel = new JLabel("BEZEROAK");
        titleLabel.setFont(new Font("Segoe UI", Font.BOLD, 30));
        titleLabel.setForeground(new Color(33, 47, 61));
        topPanel.add(titleLabel, BorderLayout.WEST);

        JPanel filterPanel = new JPanel(new BorderLayout());
        filterPanel.setBackground(Color.WHITE);
        filterPanel.setPreferredSize(new Dimension(300, 40));
        filterPanel.setBorder(BorderFactory.createLineBorder(new Color(46, 204, 113), 2));
        topPanel.add(filterPanel, BorderLayout.EAST);

        JLabel iconLabel = new JLabel("\uD83D\uDD0D "); // Lupa ikonoa
        iconLabel.setFont(new Font("Segoe UI Symbol", Font.PLAIN, 20));
        iconLabel.setForeground(new Color(46, 204, 113));
        iconLabel.setBorder(new EmptyBorder(0, 5, 0, 5));
        filterPanel.add(iconLabel, BorderLayout.WEST);

        filterTextField = new JTextField();
        filterTextField.setFont(new Font("Segoe UI", Font.PLAIN, 16));
        filterTextField.setBorder(BorderFactory.createEmptyBorder(5, 5, 5, 5));
        filterTextField.setToolTipText("Iragazi bezeroaren izena, abizena, edo emaila...");
        filterPanel.add(filterTextField, BorderLayout.CENTER);
        addPlaceholder(filterTextField, "Iragazi izena, emaila, nan...");

        // Taularen konfigurazioa
        String[] columnNames = { "ID", "Izena", "Abizena", "Emaila", "Helbidea", "Telefonoa", "NAN" };
        tableModel = new DefaultTableModel(columnNames, 0) {
            public boolean isCellEditable(int row, int col) {
                return false; // Ezin da zuzenean taulan ediziatu
            }
        };

        table = new JTable(tableModel);
        table.setFont(new Font("Segoe UI", Font.PLAIN, 14));
        table.setRowHeight(28);
        table.setFillsViewportHeight(true);
        table.getTableHeader().setFont(new Font("Segoe UI", Font.BOLD, 16));
        table.getTableHeader().setBackground(new Color(46, 204, 113));
        table.getTableHeader().setForeground(Color.WHITE);
        table.getTableHeader().setReorderingAllowed(false);

        // Lerro bakoitza kolore desberdinez (zuri/taupadatsu) bistaratzen da
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

        // Filtro dinamikoa idazterakoan
        sorter = new TableRowSorter<>(tableModel);
        table.setRowSorter(sorter);
        filterTextField.getDocument().addDocumentListener(new DocumentListener() {
            public void insertUpdate(DocumentEvent e) { filterTable(); }
            public void removeUpdate(DocumentEvent e) { filterTable(); }
            public void changedUpdate(DocumentEvent e) { filterTable(); }
        });
    }

    /**
     * Taularen edukia testu baten arabera iragazten du (filtratu).
     */
    private void filterTable() {
        String text = filterTextField.getText();
        if (text.trim().length() == 0 || text.equals("Iragazi izena, emaila, nan...")) {
            sorter.setRowFilter(null);
        } else {
            sorter.setRowFilter(RowFilter.regexFilter("(?i)" + text)); // Bilaketa ezberdina (insensible a mayúsculas)
        }
    }

    /**
     * Testu-eremuei "placeholder" efektua gehitzen die, hutsik dagoenean mezu bat erakusteko.
     * @param textField Testu-eremua
     * @param placeholder Erakutsi nahi den testua
     */
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

    /**
     * Bezeroen datuak datu-basetik kargatzen ditu eta taulan bistaratzen ditu.
     */
    private void loadDataFromDatabase() {
        tableModel.setRowCount(0); // Taula garbitu aurretik
        try (Connection conn = konexioa.getConnection()) {
            String sql = "SELECT id_bezeroa, izena, abizena, emaila, helbidea, telefonoa, nan FROM bezeroa";
            PreparedStatement ps = conn.prepareStatement(sql);
            ResultSet rs = ps.executeQuery();

            // Datuak errenkada moduan gehitu
            while (rs.next()) {
                Object[] row = {
                        rs.getInt("id_bezeroa"),
                        rs.getString("izena"),
                        rs.getString("abizena"),
                        rs.getString("emaila"),
                        rs.getString("helbidea"),
                        rs.getString("telefonoa"),
                        rs.getString("nan")
                };
                tableModel.addRow(row);
            }

        } catch (SQLException e) {
            JOptionPane.showMessageDialog(this, "Errorea bezeroak kargatzean: " + e.getMessage(),
                    "Errorea", JOptionPane.ERROR_MESSAGE);
        }
    }
}
