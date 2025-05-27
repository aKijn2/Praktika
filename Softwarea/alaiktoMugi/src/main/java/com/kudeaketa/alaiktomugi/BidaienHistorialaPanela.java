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
import javax.swing.SwingConstants;
import javax.swing.border.EmptyBorder;
import javax.swing.event.DocumentEvent;
import javax.swing.table.DefaultTableCellRenderer;
import javax.swing.table.DefaultTableModel;
import javax.swing.table.TableRowSorter;

/**
 * Bidaien historialaren interfaz grafikoa erakusten duen leihoa.
 * <p>
 * Panel honek bidaia historikoen datuak kargatzen ditu datu-basetik eta taula batean bistaratzen ditu.
 * Iragazki baten bidez izena, jatorria edo helmugaren arabera bilaketa egin daiteke.
 * </p>
 *
 * @author IKER HERNÁNDEZ - ACHRAF ALLACH
 */
public class BidaienHistorialaPanela extends JFrame {

    // Taula nagusia eta datu-modeloa
    private JTable table;
    private DefaultTableModel tableModel;

    // Taularen ordenatzailea eta testu-iragazkia
    private TableRowSorter<DefaultTableModel> sorter;
    private JTextField filterTextField;

    /**
     * Eraikitzaile lehenetsia. Leihoa sortzen du eta datuak kargatzen ditu.
     */
    public BidaienHistorialaPanela() {
        setTitle("Bidaien Historiala");
        setSize(1000, 540);
        setLocationRelativeTo(null);
        setDefaultCloseOperation(JFrame.DISPOSE_ON_CLOSE);

        initComponents();       // Interfaze grafikoa sortu
        loadDataFromDatabase(); // Datuak kargatu
    }

    /**
     * Interfaze grafikoaren osagai guztiak hasieratzen ditu.
     */
    private void initComponents() {
        JPanel mainPanel = new JPanel(new BorderLayout(20, 20));
        mainPanel.setBorder(new EmptyBorder(20, 20, 20, 20));
        mainPanel.setBackground(Color.WHITE);
        setContentPane(mainPanel);

        // Goiko panela: titulua eta iragazkia
        JPanel topPanel = new JPanel(new BorderLayout(15, 0));
        topPanel.setBackground(Color.WHITE);
        mainPanel.add(topPanel, BorderLayout.NORTH);

        // Titulua
        JLabel titleLabel = new JLabel("BIDAIAREN HISTORIALA");
        titleLabel.setFont(new Font("Segoe UI", Font.BOLD, 30));
        titleLabel.setForeground(new Color(33, 47, 61));
        titleLabel.setHorizontalAlignment(SwingConstants.LEFT);
        topPanel.add(titleLabel, BorderLayout.WEST);

        // Iragazki-testuaren panela
        JPanel filterPanel = new JPanel(new BorderLayout());
        filterPanel.setBackground(Color.WHITE);
        filterPanel.setPreferredSize(new Dimension(300, 40));
        filterPanel.setBorder(BorderFactory.createLineBorder(new Color(46, 204, 113), 2));
        topPanel.add(filterPanel, BorderLayout.EAST);

        // Bilaketa ikonoa
        JLabel iconLabel = new JLabel("\uD83D\uDD0D ");
        iconLabel.setFont(new Font("Segoe UI Symbol", Font.PLAIN, 20));
        iconLabel.setForeground(new Color(46, 204, 113));
        iconLabel.setBorder(new EmptyBorder(0, 5, 0, 5));
        filterPanel.add(iconLabel, BorderLayout.WEST);

        // Testu-iragazkia
        filterTextField = new JTextField();
        filterTextField.setFont(new Font("Segoe UI", Font.PLAIN, 16));
        filterTextField.setBorder(BorderFactory.createEmptyBorder(5, 5, 5, 5));
        filterTextField.setToolTipText("Idatzi iragazi nahi duzun testua");
        filterPanel.add(filterTextField, BorderLayout.CENTER);
        addPlaceholder(filterTextField, "Iragazi izen, jatorri edo helmuga...");

        // Taulako zutabeen izenak
        String[] columnNames = {
            "ID Historikoa", "ID Bidaia", "Hasiera data", "Hasiera ordua",
            "Amaiera data", "Amaiera ordua", "Jatorria", "Helmuga",
            "ID Gidaria", "Gidari izena", "ID Bezeroa", "Bezero izena", "Iraupena (min)"
        };

        // Datu-modeloa konfiguratu
        tableModel = new DefaultTableModel(columnNames, 0) {
            public boolean isCellEditable(int row, int col) {
                return false; // Ezin dira gelaxkak editatu
            }
        };

        // Taula sortu eta itxura ezarri
        table = new JTable(tableModel);
        table.setFont(new Font("Segoe UI", Font.PLAIN, 14));
        table.setRowHeight(28);
        table.setFillsViewportHeight(true);
        table.getTableHeader().setFont(new Font("Segoe UI", Font.BOLD, 16));
        table.getTableHeader().setBackground(new Color(46, 204, 113));
        table.getTableHeader().setForeground(Color.WHITE);
        table.getTableHeader().setReorderingAllowed(false);

        // Lerro bakoitzari kolore txandakatuak ezarri
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

        // Taula scroll batean sartu eta panel nagusira gehitu
        JScrollPane scrollPane = new JScrollPane(table);
        scrollPane.setBorder(BorderFactory.createLineBorder(new Color(189, 195, 199), 1));
        mainPanel.add(scrollPane, BorderLayout.CENTER);

        // Taularen ordenatzailea sortu
        sorter = new TableRowSorter<>(tableModel);
        table.setRowSorter(sorter);

        // Testu-aldaketak entzun eta iragazkia aplikatu
        filterTextField.getDocument().addDocumentListener(new javax.swing.event.DocumentListener() {
            public void insertUpdate(DocumentEvent e) { filterTable(); }
            public void removeUpdate(DocumentEvent e) { filterTable(); }
            public void changedUpdate(DocumentEvent e) { filterTable(); }
        });
    }

    /**
     * Iragazkia aplikatzen du taularen gainean, testuaren arabera.
     */
    private void filterTable() {
        String text = filterTextField.getText();
        if (text.trim().isEmpty()) {
            sorter.setRowFilter(null); // Ez du iragazten
        } else {
            sorter.setRowFilter(RowFilter.regexFilter("(?i)" + text)); // Bilaketa sentikortasunik gabe
        }
    }

    /**
     * Placeholder testua gehitzen dio JTextField bati.
     *
     * @param textField JTextField objektua
     * @param placeholder Agertuko den testua hutsik dagoenean
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
     * Datuak datu-basetik eskuratzen ditu eta taulan bistaratzen ditu.
     */
    private void loadDataFromDatabase() {
        clearTable(); // Aurreko datuak ezabatu

        try (Connection conn = konexioa.getConnection()) {
            String sql = """
                SELECT h.id_historikoa, b.id_bidaia, b.data AS hasiera_data, b.ordua,
                       h.amaiera_data, h.amaiera_ordua, h.jatorria, h.helmuga,
                       g.id_gidaria, CONCAT(g.izena, ' ', g.abizena) AS gidari_izena,
                       bez.id_bezeroa, CONCAT(bez.izena, ' ', bez.abizena) AS bezero_izena,
                       TIMESTAMPDIFF(MINUTE,
                           STR_TO_DATE(CONCAT(b.data, ' ', b.ordua), '%Y-%m-%d %H:%i:%s'),
                           STR_TO_DATE(CONCAT(h.amaiera_data, ' ', h.amaiera_ordua), '%Y-%m-%d %H:%i:%s')
                       ) AS iraupena_minutuetan
                FROM historikoa h
                JOIN bidaia b ON h.bidaia_id_bidaia = b.id_bidaia
                JOIN gidaria g ON b.gidaria_id_gidaria = g.id_gidaria
                JOIN erreserba e ON b.erreserba_id_erreserba = e.id_erreserba
                JOIN bezeroa bez ON e.bezeroa_id_bezeroa = bez.id_bezeroa
                ORDER BY h.amaiera_data DESC, h.amaiera_ordua DESC
            """;

            PreparedStatement ps = conn.prepareStatement(sql);
            ResultSet rs = ps.executeQuery();

            // Datuak irakurri eta taulan sartu
            while (rs.next()) {
                Object[] row = {
                    rs.getInt("id_historikoa"),
                    rs.getInt("id_bidaia"),
                    rs.getString("hasiera_data"),
                    rs.getString("ordua"),
                    rs.getString("amaiera_data"),
                    rs.getString("amaiera_ordua"),
                    rs.getString("jatorria"),
                    rs.getString("helmuga"),
                    rs.getInt("id_gidaria"),
                    rs.getString("gidari_izena"),
                    rs.getInt("id_bezeroa"),
                    rs.getString("bezero_izena"),
                    rs.getInt("iraupena_minutuetan") + " min"
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

    /**
     * Taulan dauden errenkada guztiak ezabatzen ditu.
     */
    private void clearTable() {
        tableModel.setRowCount(0);
    }
}
