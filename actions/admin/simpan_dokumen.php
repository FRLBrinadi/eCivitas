<?php
require_once __DIR__ . '/../../config/app.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../../views/admin/index.php");
    exit;
}

$jenis_id = !empty($_POST['id']) ? (int)$_POST['id'] : 0;

$pdo->beginTransaction();

try {
    // 1. PROSES DATA UTAMA (INSERT atau UPDATE)
    if ($jenis_id === 0) {
        $sql_doc = "INSERT INTO m_jenis_dokumen (kode_surat, nama_dokumen, deskripsi, is_active) VALUES (?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql_doc);
        $stmt->execute([$_POST['kode_surat'], $_POST['nama_dokumen'], $_POST['deskripsi'], $_POST['is_active']]);
        $jenis_id = $pdo->lastInsertId();
    } else {
        $sql_doc = "UPDATE m_jenis_dokumen SET kode_surat = ?, nama_dokumen = ?, deskripsi = ?, is_active = ? WHERE id = ?";
        $stmt = $pdo->prepare($sql_doc);
        $stmt->execute([$_POST['kode_surat'], $_POST['nama_dokumen'], $_POST['deskripsi'], $_POST['is_active'], $jenis_id]);
    }

    // ===============================================
    // 2. PROSES KOMPONEN FORM (m_form_template)
    // ===============================================
    
    $submitted_comp_ids = [];
    if (isset($_POST['components'])) {
        foreach ($_POST['components'] as $comp) {
            if (!empty($comp['id']) && is_numeric($comp['id'])) {
                $submitted_comp_ids[] = (int)$comp['id'];
            }
        }
    }

    // DELETE Komponen yang dihapus user
    if (empty($submitted_comp_ids)) {
        $pdo->prepare("DELETE FROM m_form_template WHERE jenis_id = ?")->execute([$jenis_id]);
    } else {
        $ids_string = implode(",", $submitted_comp_ids);
        $pdo->query("DELETE FROM m_form_template WHERE jenis_id = $jenis_id AND id NOT IN ($ids_string)");
    }

    // UPSERT Komponen
    if (isset($_POST['components'])) {
        $stmt_ins = $pdo->prepare("INSERT INTO m_form_template (jenis_id, label_field, tipe_input, data_source, is_required, urutan) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt_upd = $pdo->prepare("UPDATE m_form_template SET label_field=?, tipe_input=?, data_source=?, is_required=?, urutan=? WHERE id=?");

        foreach ($_POST['components'] as $comp) {
            $wajib = isset($comp['is_required']) ? 1 : 0;
            $urutan = $comp['urutan'] ?? 0;

            if (!empty($comp['id']) && is_numeric($comp['id'])) {
                // PDO execute menggunakan array, bukan bind_param
                $stmt_upd->execute([$comp['label_field'], $comp['tipe_input'], $comp['data_source'], $wajib, $urutan, $comp['id']]);
            } else {
                $stmt_ins->execute([$jenis_id, $comp['label_field'], $comp['tipe_input'], $comp['data_source'], $wajib, $urutan]);
            }
        }
    }

    // ===============================================
    // 3. PROSES SYARAT LAMPIRAN (m_syarat_lampiran)
    // ===============================================
    
    $submitted_lamp_ids = [];
    if (isset($_POST['lampiran'])) {
        foreach ($_POST['lampiran'] as $lamp) {
            if (!empty($lamp['id']) && is_numeric($lamp['id'])) {
                $submitted_lamp_ids[] = (int)$lamp['id'];
            }
        }
    }

    // DELETE Lampiran yang dihapus user
    if (empty($submitted_lamp_ids)) {
        $pdo->prepare("DELETE FROM m_syarat_lampiran WHERE jenis_id = ?")->execute([$jenis_id]);
    } else {
        $ids_l_string = implode(",", $submitted_lamp_ids);
        $pdo->query("DELETE FROM m_syarat_lampiran WHERE jenis_id = $jenis_id AND id NOT IN ($ids_l_string)");
    }

    // UPSERT Lampiran
    if (isset($_POST['lampiran'])) {
        $stmt_ins_l = $pdo->prepare("INSERT INTO m_syarat_lampiran (jenis_id, nama_lampiran, is_required) VALUES (?, ?, ?)");
        $stmt_upd_l = $pdo->prepare("UPDATE m_syarat_lampiran SET nama_lampiran=?, is_required=? WHERE id=?");

        foreach ($_POST['lampiran'] as $lamp) {
            $wajib = isset($lamp['is_required']) ? 1 : 0;

            if (!empty($lamp['id']) && is_numeric($lamp['id'])) {
                $stmt_upd_l->execute([$lamp['nama_lampiran'], $wajib, $lamp['id']]);
            } else {
                $stmt_ins_l->execute([$jenis_id, $lamp['nama_lampiran'], $wajib]);
            }
        }
    }

    $pdo->commit();
    echo "<script>alert('Data Berhasil Disimpan!'); window.location.href='../../views/admin/form_dokumen.php?id=$jenis_id';</script>";

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo "Terjadi Kesalahan: " . $e->getMessage();
}
?>