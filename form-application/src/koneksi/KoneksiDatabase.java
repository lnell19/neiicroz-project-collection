package koneksi;

import java.sql.Connection;
import java.sql.DriverManager;
import java.sql.SQLException;
import javax.swing.JOptionPane;

public class KoneksiDatabase {
    public static Connection BukaKoneksi() {
        try {
            // Load driver MySQL terbaru
            Class.forName("com.mysql.cj.jdbc.Driver");

            // URL koneksi
            String url = "jdbc:mysql://127.0.0.1:3306/belajar_crud?useSSL=false&allowPublicKeyRetrieval=true&serverTimezone=UTC";
            String user = "root"; // ganti kalau user MySQL kamu beda
            String password = "root"; // ganti sesuai password root kamu di Docker

            // Buat koneksi
            Connection cn = DriverManager.getConnection(url, user, password);
            System.out.println("✅ Koneksi ke database berhasil!");
            return cn;

        } catch (ClassNotFoundException | SQLException e) {
            JOptionPane.showMessageDialog(null, "❌ Gagal koneksi ke database: " + e.getMessage());
            return null;
        }
    }
}
