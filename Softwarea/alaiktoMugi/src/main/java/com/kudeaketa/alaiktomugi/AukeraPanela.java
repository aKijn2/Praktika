package com.kudeaketa.alaiktomugi;

import java.awt.Color;
import java.awt.Font;
import java.awt.GridBagConstraints;
import java.awt.GridBagLayout;
import java.awt.Insets;

import javax.swing.BorderFactory;
import javax.swing.JButton;
import javax.swing.JLabel;
import javax.swing.JPanel;
import javax.swing.SwingConstants;
import javax.swing.SwingUtilities;

/**
 * AukeraPanela klasea aplikazioaren menu nagusia da.
 * Administratzaileentzako leiho grafikoa eskaintzen du, non aukera desberdinak dauden:
 * gidariak kudeatu, bezeroak ikusi eta bidaien historiala kontsultatu.
 * 
 * @author IKER HERNÁNDEZ - ACHRAF ALLACH
 */
public class AukeraPanela extends javax.swing.JFrame {

    /**
     * AukeraPanela-ren eraikitzailea. 
     * Komponente guztiak hasieratzen ditu eta leihoa bistaratzen prest uzten du.
     */
    public AukeraPanela() {
        initComponents();
    }

    /**
     * Interfazeko osagai grafiko guztiak hasieratzen ditu eta ekintza bakoitzari bere
     * dagokion panela irekitzen dion botoia esleitzen dio.
     */
    private void initComponents() {
        setTitle("Aukera Panela"); // Leihoaren izenburua
        setDefaultCloseOperation(javax.swing.WindowConstants.EXIT_ON_CLOSE); // Leihoa ixtean aplikazioa bukatu
        setSize(500, 400); // Leihoaren tamaina
        setLocationRelativeTo(null); // Pantailaren erdian kokatu

        // Panel nagusia GridBagLayout erabiliz, elementuak ondo lerrokatzeko
        JPanel mainPanel = new JPanel(new GridBagLayout());
        mainPanel.setBackground(new Color(245, 245, 245)); // Atzeko kolorea
        GridBagConstraints gbc = new GridBagConstraints();
        gbc.insets = new Insets(10, 20, 10, 20); // Marjinak
        gbc.fill = GridBagConstraints.HORIZONTAL;
        gbc.gridx = 0;

        // Titulua (goiburua)
        JLabel titleLabel = new JLabel("AUKERA PANELA");
        titleLabel.setFont(new Font("Segoe UI", Font.BOLD, 26)); // Letra estiloa eta tamaina
        titleLabel.setForeground(new Color(52, 73, 94)); // Kolorea
        titleLabel.setHorizontalAlignment(SwingConstants.CENTER); // Erdian lerrokatu
        gbc.gridy = 0;
        gbc.gridwidth = 1;
        gbc.anchor = GridBagConstraints.CENTER;
        mainPanel.add(titleLabel, gbc);

        // Gidariak gehitu edo ezabatzeko botoia
        JButton gidariakAltanEmanBtn = createStyledButton("Gidariak altan eman / Ezabatu");
        gidariakAltanEmanBtn.addActionListener(e -> new GidariakAltanEmanPanela().setVisible(true)); // Panel berria ireki
        gbc.gridy++;
        mainPanel.add(gidariakAltanEmanBtn, gbc);

        // Gidariak ikusteko eta eguneratzeko botoia
        JButton gidariakIkusiEguneratuBtn = createStyledButton("Gidariak ikusi / Eguneratu");
        gidariakIkusiEguneratuBtn.addActionListener(e -> new GidariakIkusiEtaEguneratuPanela().setVisible(true));
        gbc.gridy++;
        mainPanel.add(gidariakIkusiEguneratuBtn, gbc);

        // Bezeroen informazioa ikusteko botoia
        JButton bezeroakIkusiBtn = createStyledButton("Bezeroak ikusi");
        bezeroakIkusiBtn.addActionListener(e -> new BezeroakIkusiPanela().setVisible(true));
        gbc.gridy++;
        mainPanel.add(bezeroakIkusiBtn, gbc);

        // Bidaien historiaren informazioa ikusteko botoia
        JButton bidaienHistorialaBtn = createStyledButton("Bidaien historiala ikusi");
        bidaienHistorialaBtn.addActionListener(e -> new BidaienHistorialaPanela().setVisible(true));
        gbc.gridy++;
        mainPanel.add(bidaienHistorialaBtn, gbc);

        getContentPane().add(mainPanel); // Panel nagusia leihoan gehitu
    }

    /**
     * Botoi bat sortzen du estilo pertsonalizatuarekin.
     * 
     * @param text Botoian agertuko den testua.
     * @return JButton objektua estilizatua.
     */
    private JButton createStyledButton(String text) {
        JButton button = new JButton(text);
        button.setFont(new Font("Segoe UI", Font.PLAIN, 16)); // Letra estiloa eta tamaina
        button.setBackground(new Color(46, 204, 113)); // Atzeko kolorea (berdea)
        button.setForeground(Color.WHITE); // Testuaren kolorea (zuria)
        button.setFocusPainted(false); // Nabigazioa desaktibatu
        button.setBorder(BorderFactory.createEmptyBorder(10, 20, 10, 20)); // Barruko tarteak
        return button;
    }

    /**
     * Aplikazioaren hasierako metodoa. AukeraPanela bistaratzen du.
     * 
     * @param args Komando-lerroko argumentuak (erabili gabe).
     */
    public static void main(String args[]) {
        SwingUtilities.invokeLater(() -> new AukeraPanela().setVisible(true)); // Interfazea abiarazi
    }
}
