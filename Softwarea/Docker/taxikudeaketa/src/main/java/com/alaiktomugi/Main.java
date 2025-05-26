package com.alaiktomugi;

import javax.swing.SwingUtilities;

public class Main {
    public static void main(String[] args) {
        System.out.println("Programa hasten...");
        SwingUtilities.invokeLater(() -> {
            new KudeaketaPanela().setVisible(true);
        });
    }
}
