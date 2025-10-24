package form;

import java.sql.*;
import javax.swing.*;
import javax.swing.table.DefaultTableModel;
import koneksi.KoneksiDatabase;
import java.util.logging.Level;
import java.util.logging.Logger;

public class FrmData extends javax.swing.JFrame {

    public Statement st;
    public ResultSet rs;
    public Connection cn;

    public FrmData() {
        initComponents();
        cn = KoneksiDatabase.BukaKoneksi(); // buka koneksi ke MySQL
        tampilData(); // tampilkan data ke tabel

        // === Event Tombol ===
        btnSimpan.addActionListener(evt -> simpanData());
        btnEdit.addActionListener(evt -> editData());
        btnHapus.addActionListener(evt -> hapusData());
        btnBatal.addActionListener(evt -> {
            resetForm();
            tampilData();
        });
        btnCari.addActionListener(evt -> cariData());

        // Klik tabel → isi form otomatis
        tblData.getSelectionModel().addListSelectionListener(e -> isiFormDariTabel());
    }

    // === FUNGSI TAMPIL DATA ===
    private void tampilData() {
        try {
            st = cn.createStatement();
            rs = st.executeQuery("SELECT * FROM biodata");

            DefaultTableModel model = new DefaultTableModel();
            model.addColumn("NIK");
            model.addColumn("Nama");
            model.addColumn("Telepon");
            model.addColumn("Alamat");

            while (rs.next()) {
                model.addRow(new Object[]{
                    rs.getString("NIK"),
                    rs.getString("nama"),
                    rs.getString("telepon"),
                    rs.getString("alamat")
                });
            }

            tblData.setModel(model);
        } catch (SQLException e) {
            JOptionPane.showMessageDialog(this, "Gagal menampilkan data: " + e.getMessage());
        }
    }

    // === FUNGSI SIMPAN DATA ===
    private void simpanData() {
        String nik = txtNIK.getText();
        String nama = txtNama.getText();
        String telepon = txtTlp.getText();
        String alamat = txtAlm.getText();

        if (nik.isEmpty() || nama.isEmpty() || telepon.isEmpty() || alamat.isEmpty()) {
            JOptionPane.showMessageDialog(this, "⚠️ Semua data harus diisi!");
            return;
        }

        try {
            st = cn.createStatement();
            String sql = "INSERT INTO biodata (NIK, nama, telepon, alamat) VALUES ('"
                    + nik + "', '" + nama + "', '" + telepon + "', '" + alamat + "')";
            st.executeUpdate(sql);

            JOptionPane.showMessageDialog(this, "✅ Data berhasil disimpan!");
            tampilData();
            resetForm();

        } catch (SQLException e) {
            if (e.getErrorCode() == 1062) {
                JOptionPane.showMessageDialog(this, "❗ NIK sudah terdaftar!");
            } else {
                JOptionPane.showMessageDialog(this, "❌ Gagal menyimpan data: " + e.getMessage());
            }
        }
    }

    // === FUNGSI EDIT DATA ===
    private void editData() {
        String nik = txtNIK.getText();
        String nama = txtNama.getText();
        String telepon = txtTlp.getText();
        String alamat = txtAlm.getText();

        if (nik.isEmpty() || nama.isEmpty() || telepon.isEmpty() || alamat.isEmpty()) {
            JOptionPane.showMessageDialog(this, "⚠️ Semua field harus diisi untuk update!");
            return;
        }

        try {
            st = cn.createStatement();
            String cekSql = "SELECT * FROM biodata WHERE NIK = '" + nik + "'";
            rs = st.executeQuery(cekSql);

            if (!rs.next()) {
                JOptionPane.showMessageDialog(this, "❌ Data dengan NIK tersebut tidak ditemukan!");
                return;
            }

            String updateSql = "UPDATE biodata SET nama='" + nama + "', telepon='" + telepon
                    + "', alamat='" + alamat + "' WHERE NIK='" + nik + "'";
            st.executeUpdate(updateSql);

            JOptionPane.showMessageDialog(this, "✏️ Data berhasil diubah!");
            tampilData();
            resetForm();

        } catch (SQLException e) {
            JOptionPane.showMessageDialog(this, "❌ Gagal mengubah data: " + e.getMessage());
        }
    }

    // === FUNGSI HAPUS DATA ===
    private void hapusData() {
        String nik = txtNIK.getText();
        if (nik.isEmpty()) {
            JOptionPane.showMessageDialog(this, "⚠️ Masukkan NIK yang ingin dihapus!");
            return;
        }

        int konfirmasi = JOptionPane.showConfirmDialog(this,
                "Yakin ingin menghapus data NIK: " + nik + " ?",
                "Konfirmasi Hapus", JOptionPane.YES_NO_OPTION);

        if (konfirmasi == JOptionPane.YES_OPTION) {
            try {
                st = cn.createStatement();
                String sql = "DELETE FROM biodata WHERE NIK = '" + nik + "'";
                st.executeUpdate(sql);

                JOptionPane.showMessageDialog(this, "🗑️ Data berhasil dihapus!");
                tampilData();
                resetForm();

            } catch (SQLException e) {
                JOptionPane.showMessageDialog(this, "❌ Gagal menghapus data: " + e.getMessage());
            }
        }
    }

    // === FUNGSI RESET FORM ===
    private void resetForm() {
        txtNIK.setText("");
        txtNama.setText("");
        txtTlp.setText("");
        txtAlm.setText("");
        txtCari.setText("");
        txtNIK.setEditable(true);
        txtNIK.requestFocus();
        tblData.clearSelection();
    }

    // === FUNGSI ISI FORM DARI TABEL ===
    private void isiFormDariTabel() {
        int row = tblData.getSelectedRow();
        if (row != -1) {
            txtNIK.setText(tblData.getValueAt(row, 0).toString());
            txtNama.setText(tblData.getValueAt(row, 1).toString());
            txtTlp.setText(tblData.getValueAt(row, 2).toString());
            txtAlm.setText(tblData.getValueAt(row, 3).toString());
            txtNIK.setEditable(false); // tidak boleh ubah NIK
        }
    }

    // === FUNGSI CARI DATA ===
    private void cariData() {
        String kategori = cmbCari.getSelectedItem().toString();
        String kataKunci = txtCari.getText();

        if (kataKunci.isEmpty()) {
            JOptionPane.showMessageDialog(this, "⚠️ Masukkan kata kunci untuk pencarian!");
            return;
        }

        try {
            st = cn.createStatement();
            String sql = "SELECT * FROM biodata WHERE " + kategori + " LIKE '%" + kataKunci + "%'";
            rs = st.executeQuery(sql);

            DefaultTableModel model = new DefaultTableModel();
            model.addColumn("NIK");
            model.addColumn("Nama");
            model.addColumn("Telepon");
            model.addColumn("Alamat");

            boolean adaData = false;
            while (rs.next()) {
                adaData = true;
                model.addRow(new Object[]{
                    rs.getString("NIK"),
                    rs.getString("nama"),
                    rs.getString("telepon"),
                    rs.getString("alamat")
                });
            }

            if (!adaData) {
                JOptionPane.showMessageDialog(this, "🔍 Data tidak ditemukan!");
            }

            tblData.setModel(model);

        } catch (SQLException e) {
            JOptionPane.showMessageDialog(this, "❌ Terjadi kesalahan saat mencari data: " + e.getMessage());
        }
    }




    /**
     * This method is called from within the constructor to initialize the form.
     * WARNING: Do NOT modify this code. The content of this method is always
     * regenerated by the Form Editor.
     */
    @SuppressWarnings("unchecked")
    // <editor-fold defaultstate="collapsed" desc="Generated Code">//GEN-BEGIN:initComponents
    private void initComponents() {

        jLabel1 = new javax.swing.JLabel();
        jLabel2 = new javax.swing.JLabel();
        jLabel3 = new javax.swing.JLabel();
        jLabel4 = new javax.swing.JLabel();
        txtNIK = new javax.swing.JTextField();
        txtNama = new javax.swing.JTextField();
        txtTlp = new javax.swing.JTextField();
        txtAlm = new javax.swing.JTextField();
        btnSimpan = new javax.swing.JButton();
        btnHapus = new javax.swing.JButton();
        btnBatal = new javax.swing.JButton();
        jScrollPane1 = new javax.swing.JScrollPane();
        tblData = new javax.swing.JTable();
        cmbCari = new javax.swing.JComboBox<>();
        jLabel5 = new javax.swing.JLabel();
        txtCari = new javax.swing.JTextField();
        btnCari = new javax.swing.JButton();
        btnEdit = new javax.swing.JButton();

        setDefaultCloseOperation(javax.swing.WindowConstants.EXIT_ON_CLOSE);

        jLabel1.setText("NIK");

        jLabel2.setText("Nama Lengkap");

        jLabel3.setText("Telepon");

        jLabel4.setText("Alamat");

        txtNama.addActionListener(new java.awt.event.ActionListener() {
            public void actionPerformed(java.awt.event.ActionEvent evt) {
                txtNamaActionPerformed(evt);
            }
        });

        txtAlm.addActionListener(new java.awt.event.ActionListener() {
            public void actionPerformed(java.awt.event.ActionEvent evt) {
                txtAlmActionPerformed(evt);
            }
        });

        btnSimpan.setText("Simpan");

        btnHapus.setText("Hapus");

        btnBatal.setText("Batal");

        tblData.setAutoCreateRowSorter(true);
        tblData.setModel(new javax.swing.table.DefaultTableModel(
            new Object [][] {
                {null, null, null, null},
                {null, null, null, null},
                {null, null, null, null},
                {null, null, null, null}
            },
            new String [] {
                "NIK", "Nama", "Telepon", "Alamat"
            }
        ));
        jScrollPane1.setViewportView(tblData);

        cmbCari.setModel(new javax.swing.DefaultComboBoxModel<>(new String[] { "NIK", "nama", "telepon", "alamat" }));

        jLabel5.setText("Cari Data");

        btnCari.setText("Cari");
        btnCari.addActionListener(new java.awt.event.ActionListener() {
            public void actionPerformed(java.awt.event.ActionEvent evt) {
                btnCariActionPerformed(evt);
            }
        });

        btnEdit.setText("Edit");

        javax.swing.GroupLayout layout = new javax.swing.GroupLayout(getContentPane());
        getContentPane().setLayout(layout);
        layout.setHorizontalGroup(
            layout.createParallelGroup(javax.swing.GroupLayout.Alignment.LEADING)
            .addGroup(layout.createSequentialGroup()
                .addContainerGap()
                .addGroup(layout.createParallelGroup(javax.swing.GroupLayout.Alignment.LEADING)
                    .addGroup(layout.createParallelGroup(javax.swing.GroupLayout.Alignment.LEADING, false)
                        .addGroup(layout.createSequentialGroup()
                            .addComponent(jLabel5)
                            .addGap(18, 18, 18)
                            .addComponent(cmbCari, javax.swing.GroupLayout.PREFERRED_SIZE, 109, javax.swing.GroupLayout.PREFERRED_SIZE)
                            .addPreferredGap(javax.swing.LayoutStyle.ComponentPlacement.UNRELATED)
                            .addComponent(txtCari))
                        .addGroup(javax.swing.GroupLayout.Alignment.TRAILING, layout.createSequentialGroup()
                            .addPreferredGap(javax.swing.LayoutStyle.ComponentPlacement.RELATED, 305, javax.swing.GroupLayout.PREFERRED_SIZE)
                            .addComponent(btnCari)))
                    .addGroup(layout.createParallelGroup(javax.swing.GroupLayout.Alignment.TRAILING)
                        .addGroup(layout.createSequentialGroup()
                            .addComponent(btnSimpan, javax.swing.GroupLayout.PREFERRED_SIZE, 86, javax.swing.GroupLayout.PREFERRED_SIZE)
                            .addPreferredGap(javax.swing.LayoutStyle.ComponentPlacement.RELATED)
                            .addComponent(btnHapus, javax.swing.GroupLayout.PREFERRED_SIZE, 81, javax.swing.GroupLayout.PREFERRED_SIZE)
                            .addPreferredGap(javax.swing.LayoutStyle.ComponentPlacement.RELATED)
                            .addComponent(btnBatal, javax.swing.GroupLayout.PREFERRED_SIZE, 78, javax.swing.GroupLayout.PREFERRED_SIZE))
                        .addGroup(layout.createParallelGroup(javax.swing.GroupLayout.Alignment.LEADING)
                            .addGroup(layout.createSequentialGroup()
                                .addGap(4, 4, 4)
                                .addGroup(layout.createParallelGroup(javax.swing.GroupLayout.Alignment.TRAILING)
                                    .addComponent(jLabel2)
                                    .addComponent(jLabel1, javax.swing.GroupLayout.Alignment.LEADING)
                                    .addComponent(jLabel3, javax.swing.GroupLayout.Alignment.LEADING)
                                    .addComponent(jLabel4, javax.swing.GroupLayout.Alignment.LEADING))
                                .addPreferredGap(javax.swing.LayoutStyle.ComponentPlacement.UNRELATED)
                                .addGroup(layout.createParallelGroup(javax.swing.GroupLayout.Alignment.LEADING, false)
                                    .addComponent(txtAlm)
                                    .addComponent(txtNama)
                                    .addComponent(txtTlp)
                                    .addComponent(txtNIK, javax.swing.GroupLayout.PREFERRED_SIZE, 257, javax.swing.GroupLayout.PREFERRED_SIZE)))
                            .addComponent(jScrollPane1, javax.swing.GroupLayout.PREFERRED_SIZE, 375, javax.swing.GroupLayout.PREFERRED_SIZE))
                        .addComponent(btnEdit)))
                .addContainerGap(javax.swing.GroupLayout.DEFAULT_SIZE, Short.MAX_VALUE))
        );
        layout.setVerticalGroup(
            layout.createParallelGroup(javax.swing.GroupLayout.Alignment.LEADING)
            .addGroup(layout.createSequentialGroup()
                .addContainerGap()
                .addGroup(layout.createParallelGroup(javax.swing.GroupLayout.Alignment.BASELINE)
                    .addComponent(jLabel1)
                    .addComponent(txtNIK, javax.swing.GroupLayout.PREFERRED_SIZE, javax.swing.GroupLayout.DEFAULT_SIZE, javax.swing.GroupLayout.PREFERRED_SIZE))
                .addGap(18, 18, 18)
                .addGroup(layout.createParallelGroup(javax.swing.GroupLayout.Alignment.BASELINE)
                    .addComponent(jLabel2)
                    .addComponent(txtNama, javax.swing.GroupLayout.PREFERRED_SIZE, javax.swing.GroupLayout.DEFAULT_SIZE, javax.swing.GroupLayout.PREFERRED_SIZE))
                .addGap(18, 18, 18)
                .addGroup(layout.createParallelGroup(javax.swing.GroupLayout.Alignment.BASELINE)
                    .addComponent(jLabel3)
                    .addComponent(txtTlp, javax.swing.GroupLayout.PREFERRED_SIZE, javax.swing.GroupLayout.DEFAULT_SIZE, javax.swing.GroupLayout.PREFERRED_SIZE))
                .addGap(18, 18, 18)
                .addGroup(layout.createParallelGroup(javax.swing.GroupLayout.Alignment.BASELINE)
                    .addComponent(jLabel4)
                    .addComponent(txtAlm, javax.swing.GroupLayout.PREFERRED_SIZE, javax.swing.GroupLayout.DEFAULT_SIZE, javax.swing.GroupLayout.PREFERRED_SIZE))
                .addGap(18, 18, 18)
                .addGroup(layout.createParallelGroup(javax.swing.GroupLayout.Alignment.BASELINE)
                    .addComponent(btnSimpan)
                    .addComponent(btnHapus)
                    .addComponent(btnBatal))
                .addPreferredGap(javax.swing.LayoutStyle.ComponentPlacement.UNRELATED)
                .addComponent(jScrollPane1, javax.swing.GroupLayout.PREFERRED_SIZE, 110, javax.swing.GroupLayout.PREFERRED_SIZE)
                .addPreferredGap(javax.swing.LayoutStyle.ComponentPlacement.RELATED)
                .addComponent(btnEdit)
                .addPreferredGap(javax.swing.LayoutStyle.ComponentPlacement.UNRELATED)
                .addGroup(layout.createParallelGroup(javax.swing.GroupLayout.Alignment.BASELINE)
                    .addComponent(cmbCari, javax.swing.GroupLayout.PREFERRED_SIZE, javax.swing.GroupLayout.DEFAULT_SIZE, javax.swing.GroupLayout.PREFERRED_SIZE)
                    .addComponent(jLabel5)
                    .addComponent(txtCari, javax.swing.GroupLayout.PREFERRED_SIZE, javax.swing.GroupLayout.DEFAULT_SIZE, javax.swing.GroupLayout.PREFERRED_SIZE))
                .addPreferredGap(javax.swing.LayoutStyle.ComponentPlacement.RELATED)
                .addComponent(btnCari)
                .addContainerGap(javax.swing.GroupLayout.DEFAULT_SIZE, Short.MAX_VALUE))
        );

        pack();
        setLocationRelativeTo(null);
    }// </editor-fold>//GEN-END:initComponents

    private void txtAlmActionPerformed(java.awt.event.ActionEvent evt) {//GEN-FIRST:event_txtAlmActionPerformed
        // TODO add your handling code here:
    }//GEN-LAST:event_txtAlmActionPerformed

    private void txtNamaActionPerformed(java.awt.event.ActionEvent evt) {//GEN-FIRST:event_txtNamaActionPerformed
        // TODO add your handling code here:
    }//GEN-LAST:event_txtNamaActionPerformed

    private void btnCariActionPerformed(java.awt.event.ActionEvent evt) {//GEN-FIRST:event_btnCariActionPerformed
        // TODO add your handling code here:
    }//GEN-LAST:event_btnCariActionPerformed

    /**
     * @param args the command line arguments
     */
    public static void main(String args[]) {
        /* Set the Nimbus look and feel */
        //<editor-fold defaultstate="collapsed" desc=" Look and feel setting code (optional) ">
        /* If Nimbus (introduced in Java SE 6) is not available, stay with the default look and feel.
         * For details see http://download.oracle.com/javase/tutorial/uiswing/lookandfeel/plaf.html 
         */
        try {
            for (javax.swing.UIManager.LookAndFeelInfo info : javax.swing.UIManager.getInstalledLookAndFeels()) {
                if ("Nimbus".equals(info.getName())) {
                    javax.swing.UIManager.setLookAndFeel(info.getClassName());
                    break;
                }
            }
        } catch (ReflectiveOperationException | javax.swing.UnsupportedLookAndFeelException ex) {
    Logger.getLogger(FrmData.class.getName()).log(Level.SEVERE, null, ex);
}
        //</editor-fold>

        /* Create and display the form */
        java.awt.EventQueue.invokeLater(() -> new FrmData().setVisible(true));
    }

    // Variables declaration - do not modify//GEN-BEGIN:variables
    private javax.swing.JButton btnBatal;
    private javax.swing.JButton btnCari;
    private javax.swing.JButton btnEdit;
    private javax.swing.JButton btnHapus;
    private javax.swing.JButton btnSimpan;
    private javax.swing.JComboBox<String> cmbCari;
    private javax.swing.JLabel jLabel1;
    private javax.swing.JLabel jLabel2;
    private javax.swing.JLabel jLabel3;
    private javax.swing.JLabel jLabel4;
    private javax.swing.JLabel jLabel5;
    private javax.swing.JScrollPane jScrollPane1;
    private javax.swing.JTable tblData;
    private javax.swing.JTextField txtAlm;
    private javax.swing.JTextField txtCari;
    private javax.swing.JTextField txtNIK;
    private javax.swing.JTextField txtNama;
    private javax.swing.JTextField txtTlp;
    // End of variables declaration//GEN-END:variables
}
