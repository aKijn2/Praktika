package com.kudeaketa.alaiktomugi;

import java.awt.BorderLayout;
import java.awt.Color;
import java.awt.Dimension;
import java.awt.FlowLayout;
import java.awt.Font;
import java.awt.GridLayout;
import java.sql.Connection;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.util.regex.Pattern;

import javax.swing.BorderFactory;
import javax.swing.JButton;
import javax.swing.JFrame;
import javax.swing.JLabel;
import javax.swing.JOptionPane;
import javax.swing.JPanel;
import javax.swing.JScrollPane;
import javax.swing.JTable;
import javax.swing.JTextField;
import javax.swing.RowFilter;
import javax.swing.SwingConstants;
import javax.swing.event.DocumentEvent;
import javax.swing.event.DocumentListener;
import javax.swing.table.DefaultTableModel;
import javax.swing.table.TableRowSorter;

/**
 * GidariakIkusiEtaEguneratuPanela klasea.
 * 
 * Leiho honek datu-baseko gidari guztiak taula batean bistaratzen ditu,
 * eta hauek eguneratzeko edo ezabatzeko aukera eskaintzen du.
 * Bilaketa sistema bat ere badu NAN, izena edo abizena erabiliz.
 * 
 * @author IKER HERNÁNDEZ - ACHRAF ALLACH
 * @version 1.0
 */

public class GidariakIkusiEtaEguneratuPanela extends JFrame {

    // Taularen zutabe izenak
    private static final String[] COLUMN_NAMES = {
        "NAN", "Izena", "Abizena", "Helbidea", "Jaiotze Data",
        "Emaila", "Telefonoa", "Pasahitza", "Erabiltzailea", "Taxi Matrikula"
    };

    // Kolore eta letra-tipoak
    private static final Color PRIMARY_GREEN = new Color(46, 204, 113);
    private static final Font FONT_TITLE = new Font("Segoe UI", Font.BOLD, 28);
    private static final Font FONT_LABEL = new Font("Segoe UI", Font.PLAIN, 14);
    private static final Font FONT_BUTTON = new Font("Segoe UI", Font.BOLD, 14);
    private static final Font FONT_TABLE = new Font("Segoe UI", Font.PLAIN, 14);

    // GUI osagaiak
    private JTable table;
    private DefaultTableModel tableModel;
    private TableRowSorter<DefaultTableModel> sorter;
    private JTextField searchField;
    private final JTextField[] inputFields = new JTextField[COLUMN_NAMES.length];

    /**
     * Eraikitzailea. Leihoa eta osagai guztiak sortzen ditu.
     */
    public GidariakIkusiEtaEguneratuPanela() {
        initializeFrame();
        initComponents();
        loadData();
    }

    /**
     * Leihoaren hasierako ezarpenak konfiguratzen ditu.
     */
    private void initializeFrame() {
        setTitle("Gidariak Ikusi eta Eguneratu");
        setSize(1000, 800);
        setLocationRelativeTo(null);
        setDefaultCloseOperation(JFrame.DISPOSE_ON_CLOSE);
    }

    /**
     * Panel nagusiko osagai guztiak hasieratzen eta gehitzen ditu.
     */
    private void initComponents() {
        JPanel mainPanel = new JPanel(new BorderLayout(15, 15));
        mainPanel.setBorder(BorderFactory.createEmptyBorder(15, 15, 15, 15));
        mainPanel.setBackground(Color.WHITE);
        setContentPane(mainPanel);

        mainPanel.add(createTitleLabel(), BorderLayout.NORTH);
        mainPanel.add(createTableScrollPane(), BorderLayout.BEFORE_FIRST_LINE);
        mainPanel.add(createFormPanel(), BorderLayout.CENTER);
        mainPanel.add(createBottomPanel(), BorderLayout.SOUTH);
    }

    /**
     * Goiburuko etiketaren sorrera (izenburua).
     * @return JLabel
     */
    private JLabel createTitleLabel() {
        JLabel titleLabel = new JLabel("GIDARIAK IKUSI, EGUNERATU ETA EZABATU", SwingConstants.CENTER);
        titleLabel.setFont(FONT_TITLE);
        titleLabel.setForeground(PRIMARY_GREEN);
        return titleLabel;
    }

    /**
     * Taula bat sortzen du gidarien informazioa bistaratzeko.
     * @return JScrollPane
     */
    private JScrollPane createTableScrollPane() {
        tableModel = new DefaultTableModel(COLUMN_NAMES, 0) {
            @Override
            public boolean isCellEditable(int row, int column) {
                return false; // Gelaxkak ezin dira zuzenean editatu
            }
        };

        table = new JTable(tableModel);
        table.setFont(FONT_TABLE);
        table.setRowHeight(24);

        sorter = new TableRowSorter<>(tableModel);
        table.setRowSorter(sorter);

        // Aukeratutako errenkadaren datuak formularioan kargatzen dira
        table.getSelectionModel().addListSelectionListener(e -> {
            if (!e.getValueIsAdjusting() && table.getSelectedRow() != -1) {
                fillFormFromSelectedRow();
            }
        });

        JScrollPane scrollPane = new JScrollPane(table);
        scrollPane.setPreferredSize(new Dimension(950, 300));
        return scrollPane;
    }

    /**
     * Gidariaren datuak sartzeko formulario-panelaren sorrera.
     * @return JPanel
     */
    private JPanel createFormPanel() {
        JPanel formPanel = new JPanel(new GridLayout(COLUMN_NAMES.length, 2, 10, 10));
        formPanel.setBackground(Color.WHITE);

        for (int i = 0; i < COLUMN_NAMES.length; i++) {
            JLabel label = new JLabel(COLUMN_NAMES[i] + ":");
            label.setFont(FONT_LABEL);

            JTextField input = new JTextField();
            input.setFont(FONT_LABEL);
            input.setBorder(BorderFactory.createLineBorder(PRIMARY_GREEN, 1));

            inputFields[i] = input;

            formPanel.add(label);
            formPanel.add(input);
        }
        return formPanel;
    }

    /**
     * Beheko botoi eta bilaketa-panelaren sorrera.
     * @return JPanel
     */
    private JPanel createBottomPanel() {
        JPanel bottomPanel = new JPanel(new BorderLayout(10, 10));
        bottomPanel.setBackground(Color.WHITE);

        bottomPanel.add(createButtonPanel(), BorderLayout.WEST);
        bottomPanel.add(createSearchPanel(), BorderLayout.CENTER);

        return bottomPanel;
    }

    /**
     * Eguneratu eta Ezabatu botoiak sortzen ditu.
     * @return JPanel
     */
    private JPanel createButtonPanel() {
        JPanel buttonPanel = new JPanel(new FlowLayout(FlowLayout.LEFT, 10, 10));
        buttonPanel.setBackground(Color.WHITE);

        JButton updateButton = createGreenButton("Eguneratu");
        updateButton.addActionListener(e -> updateGidaria());

        JButton deleteButton = createGreenButton("Ezabatu");
        deleteButton.addActionListener(e -> deleteGidariaByNAN());

        buttonPanel.add(updateButton);
        buttonPanel.add(deleteButton);

        return buttonPanel;
    }

    /**
     * Bilaketa-eremua duen panela sortzen du.
     * @return JPanel
     */
    private JPanel createSearchPanel() {
        JPanel searchPanel = new JPanel(new BorderLayout());
        searchPanel.setBackground(Color.WHITE);
        searchPanel.setBorder(BorderFactory.createLineBorder(PRIMARY_GREEN, 2));

        searchField = new JTextField("Bilatu NAN, izena edo abizena...");
        searchField.setForeground(Color.GRAY);
        searchField.setBorder(BorderFactory.createEmptyBorder(5, 10, 5, 10));

        searchField.getDocument().addDocumentListener(new DocumentListener() {
            @Override public void insertUpdate(DocumentEvent e) { filter(); }
            @Override public void removeUpdate(DocumentEvent e) { filter(); }
            @Override public void changedUpdate(DocumentEvent e) { filter(); }
        });

        searchField.addFocusListener(new java.awt.event.FocusAdapter() {
            public void focusGained(java.awt.event.FocusEvent e) {
                if (searchField.getText().equals("Bilatu NAN, izena edo abizena...")) {
                    searchField.setText("");
                    searchField.setForeground(Color.BLACK);
                }
            }
            public void focusLost(java.awt.event.FocusEvent e) {
                if (searchField.getText().isEmpty()) {
                    searchField.setForeground(Color.GRAY);
                    searchField.setText("Bilatu NAN, izena edo abizena...");
                }
            }
        });

        searchPanel.add(searchField, BorderLayout.CENTER);
        return searchPanel;
    }

    /**
     * Kolore berdez botoi bat sortzen du.
     * @param text Botoiaren testua
     * @return JButton
     */
    private JButton createGreenButton(String text) {
        JButton button = new JButton(text);
        button.setBackground(PRIMARY_GREEN);
        button.setForeground(Color.WHITE);
        button.setFocusPainted(false);
        button.setFont(FONT_BUTTON);
        button.setBorder(BorderFactory.createEmptyBorder(8, 20, 8, 20));
        return button;
    }

    /**
     * Gidarien datuak datu-basetik kargatzen ditu taulara.
     */
    private void loadData() {
        tableModel.setRowCount(0);
        try (Connection conn = konexioa.getConnection();
             PreparedStatement stmt = conn.prepareStatement(
                     "SELECT nan, izena, abizena, helbidea, jaiotze_data, emaila, telefonoa, pasahitza, erabiltzailea, taxi_matrikula FROM gidaria");
             ResultSet rs = stmt.executeQuery()) {

            while (rs.next()) {
                Object[] row = new Object[COLUMN_NAMES.length];
                for (int i = 0; i < COLUMN_NAMES.length; i++) {
                    row[i] = rs.getObject(i + 1);
                }
                tableModel.addRow(row);
            }

        } catch (SQLException e) {
            showError("Errorea datuak kargatzerakoan: " + e.getMessage());
        }
    }

    /**
     * Hautatutako errenkadaren datuak formularioan kargatzen ditu.
     */
    private void fillFormFromSelectedRow() {
        int selectedRow = table.convertRowIndexToModel(table.getSelectedRow());
        for (int i = 0; i < inputFields.length; i++) {
            Object value = tableModel.getValueAt(selectedRow, i);
            inputFields[i].setText(value != null ? value.toString() : "");
        }
    }

    /**
     * Bilaketa testuaren arabera taula iragazten du.
     */
    private void filter() {
        String text = searchField.getText().trim();
        if (text.isEmpty() || text.equals("Bilatu NAN, izena edo abizena...")) {
            sorter.setRowFilter(null);
        } else {
            sorter.setRowFilter(RowFilter.regexFilter("(?i)" + Pattern.quote(text)));
        }
    }

    /**
     * Hautatutako gidariaren datuak eguneratzen ditu datu-basean.
     */
    private void updateGidaria() {
        if (table.getSelectedRow() == -1) {
            showInfo("Mesedez, aukeratu gidari bat eguneratzeko.");
            return;
        }

        String nan = inputFields[0].getText().trim();
        if (nan.isEmpty()) {
            showError("NAN ezin da hutsa izan.");
            return;
        }

        try (Connection conn = konexioa.getConnection();
             PreparedStatement ps = conn.prepareStatement(
                     "UPDATE gidaria SET izena=?, abizena=?, helbidea=?, jaiotze_data=?, emaila=?, telefonoa=?, pasahitza=?, erabiltzailea=?, taxi_matrikula=? WHERE nan=?")) {

            for (int i = 1; i < inputFields.length; i++) {
                ps.setString(i, inputFields[i].getText().trim());
            }
            ps.setString(10, nan);

            int updatedRows = ps.executeUpdate();
            if (updatedRows > 0) {
                showInfo("Gidaria eguneratua izan da.");
                loadData();
                clearForm();
            } else {
                showError("Ez da gidaria aurkitu NAN honekin: " + nan);
            }

        } catch (SQLException e) {
            showError("Errorea eguneratzerakoan: " + e.getMessage());
        }
    }

    /**
     * Sartutako NAN-aren arabera gidaria ezabatzen du.
     */
    private void deleteGidariaByNAN() {
        String nan = inputFields[0].getText().trim();
        if (nan.isEmpty()) {
            showInfo("Mesedez, sartu NAN ezabatzeko.");
            return;
        }

        int confirm = JOptionPane.showConfirmDialog(this,
                "Ziur zaude NAN hau ezabatu nahi duzula?\n" + nan,
                "Berrespena", JOptionPane.YES_NO_OPTION);

        if (confirm != JOptionPane.YES_OPTION) return;

        try (Connection conn = konexioa.getConnection();
             PreparedStatement ps = conn.prepareStatement("DELETE FROM gidaria WHERE nan = ?")) {

            ps.setString(1, nan);
            int deletedRows = ps.executeUpdate();

            if (deletedRows > 0) {
                showInfo("Gidaria ezabatua izan da.");
                loadData();
                clearForm();
            } else {
                showError("Ez da gidaria aurkitu NAN honekin: " + nan);
            }

        } catch (SQLException e) {
            showError("Errorea ezabatzerakoan: " + e.getMessage());
        }
    }

    /**
     * Formularioa garbitzen du (testu-eremuak hutsik eta hautapena kendu).
     */
    private void clearForm() {
        for (JTextField field : inputFields) {
            field.setText("");
        }
        table.clearSelection();
    }

    /**
     * Errore mezua erakusten du.
     * @param message Errorearen mezua
     */
    private void showError(String message) {
        JOptionPane.showMessageDialog(this, message, "Errorea", JOptionPane.ERROR_MESSAGE);
    }

    /**
     * Informazio mezua erakusten du.
     * @param message Mezua
     */
    private void showInfo(String message) {
        JOptionPane.showMessageDialog(this, message, "Informazioa", JOptionPane.INFORMATION_MESSAGE);
    }
}
