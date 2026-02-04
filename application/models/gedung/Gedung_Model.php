<?php

/**
 * 
 */
class Gedung_Model extends CI_Model
{

	function __construct()
	{
		parent::__construct();
	}

	public function get_unread_transaction()
	{
		$sql = "SELECT * FROM PEMBAYARAN WHERE STATUS_VERIF = 'PENDING'";
		$query = $this->db->query($sql);
		return $query->num_rows();
	}

	// 	public function get_transaksi_flag($username)
	// {
	//     $sql = "
	//         SELECT COUNT(*) AS jml
	//         FROM pembayaran pb
	//         JOIN pemesanan p 
	//           ON p.ID_PEMESANAN = pb.ID_PEMESANAN_RAW
	//         WHERE LOWER(p.USERNAME) = LOWER(?)
	//            AND pb.STATUS_VERIF IN ('PENDING','CONFIRMED','REJECTED')
	//     ";

	//     $row = $this->db->query($sql, [$username])->row();
	//     return $row ? (int)$row->jml : 0;
	// }

	// ambil pemesanan yang masih "unread" (misal FLAG = 1 atau 3)
	public function get_pemesanan_unread_ids($username, $limit = 5)
	{
		$sql = "
        SELECT DISTINCT p.ID_PEMESANAN AS id
        FROM v_pemesanan v
        JOIN pemesanan p
          ON p.ID_PEMESANAN = (
                CASE
                    WHEN v.ID_PEMESANAN LIKE 'PMSN%' THEN CAST(SUBSTRING(v.ID_PEMESANAN, 5) AS UNSIGNED)
                    ELSE CAST(v.ID_PEMESANAN AS UNSIGNED)
                END
          )
				WHERE LOWER(v.USERNAME)=LOWER(?)
					AND v.STATUS IN ('PROPOSAL APPROVE','SUBMITED','PROCESS')
          AND p.FLAG IN (1,3)
        ORDER BY p.ID_PEMESANAN DESC
        LIMIT ?
    ";
		$rows = $this->db->query($sql, [$username, (int)$limit])->result_array();
		$ids = [];
		foreach ($rows as $r) $ids[] = (int)$r['id'];
		return $ids;
	}

	// ambil transaksi yang masih "unread"
	// NOTE: kamu tadi error karena kolom FLAG_TRX belum ada.
	// Jadi sementara pakai kondisi STATUS_VERIF saja (PENDING/REJECTED/CONFIRMED) atau buat kolom FLAG_TRX dulu.
	public function get_transaksi_unread_ids($username, $limit = 5)
	{
		$sql = "
        SELECT pb.ID_PEMBAYARAN AS id
        FROM pembayaran pb
        JOIN pemesanan p ON p.ID_PEMESANAN = pb.ID_PEMESANAN_RAW
        WHERE LOWER(p.USERNAME)=LOWER(?)
          AND pb.STATUS_VERIF IN ('PENDING','CONFIRMED','REJECTED')
        ORDER BY pb.ID_PEMBAYARAN DESC
        LIMIT ?
    ";
		$rows = $this->db->query($sql, [$username, (int)$limit])->result_array();
		$ids = [];
		foreach ($rows as $r) $ids[] = (int)$r['id'];
		return $ids;
	}


	public function get_transaksi_flag($username)
	{
		$sql = "
        SELECT COUNT(*) AS jml
        FROM pembayaran pb
        JOIN pemesanan p ON p.ID_PEMESANAN = pb.ID_PEMESANAN_RAW
        WHERE LOWER(p.USERNAME) = LOWER(?)
          AND pb.FLAG_TRX = 1
          AND pb.STATUS_VERIF IN ('PENDING','CONFIRMED','REJECTED')
    ";
		$row = $this->db->query($sql, [$username])->row();
		return $row ? (int)$row->jml : 0;
	}

	public function mark_trx_read($id_pembayaran, $username)
	{
		$id_pembayaran = (int)$id_pembayaran;

		$sql = "
        UPDATE pembayaran pb
        JOIN pemesanan p ON p.ID_PEMESANAN = pb.ID_PEMESANAN_RAW
        SET pb.FLAG_TRX = 2
        WHERE pb.ID_PEMBAYARAN = ?
          AND LOWER(p.USERNAME) = LOWER(?)
    ";
		return $this->db->query($sql, [$id_pembayaran, $username]);
	}


	public function clear_transaksi_flag($username)
	{
		$sql = "
        UPDATE pembayaran pb
        JOIN pemesanan p ON p.ID_PEMESANAN = pb.ID_PEMESANAN_RAW
        SET pb.FLAG_TRX = 2
        WHERE LOWER(p.USERNAME) = LOWER(?)
          AND pb.FLAG_TRX = 1
          AND pb.STATUS_VERIF IN ('PENDING','CONFIRMED','REJECTED')
    ";
		return $this->db->query($sql, [$username]);
	}



	public function get_calendar_events($id_gedung, $start, $end)
	{
		$id_gedung = (int)$id_gedung;

		$this->db->select("
        p.ID_PEMESANAN as id,
        p.TANGGAL_PEMESANAN as tgl,
        TIME_FORMAT(p.JAM_PEMESANAN, '%H:%i') as start,
        TIME_FORMAT(p.JAM_SELESAI, '%H:%i') as end,
        p.STATUS as status
    ", false);

		$this->db->from('pemesanan p');
		$this->db->where('p.ID_GEDUNG', $id_gedung);
		$this->db->where('p.TANGGAL_PEMESANAN >=', $start);
		$this->db->where('p.TANGGAL_PEMESANAN <=', $end);

		// status yang dianggap ngunci jadwal
		$this->db->where_in('p.STATUS', array(0, 3));

		$this->db->order_by('p.TANGGAL_PEMESANAN', 'ASC');
		$this->db->order_by('p.JAM_PEMESANAN', 'ASC');

		$rows = $this->db->get()->result_array();

		$out = array();
		foreach ($rows as $r) {
			$out[] = array(
				'id'     => (int)$r['id'],
				'tgl'    => $r['tgl'],
				'start'  => $r['start'],
				'end'    => $r['end'],
				'status' => (string)$r['status'],
				'title'  => 'Booking Ruangan'
			);
		}

		return $out;
	}





	public function laporan_pembayaran_periodic($start_date, $end_date)
	{
		$sql = "
        SELECT
            pb.*,
            u.NAMA_LENGKAP,
            u.perusahaan,
            u.nama_perusahaan,
            u.departemen
        FROM pembayaran pb
        LEFT JOIN pemesanan p ON p.ID_PEMESANAN = pb.ID_PEMESANAN_RAW
        LEFT JOIN user u ON u.USERNAME = p.USERNAME
        WHERE pb.TANGGAL_TRANSFER BETWEEN ? AND ?
        ORDER BY pb.TANGGAL_TRANSFER ASC, pb.ID_PEMBAYARAN ASC
    ";

		$query = $this->db->query($sql, array($start_date, $end_date));
		return $query->result_array();
	}


	public function has_locked_conflict($id_gedung, $tanggal, $jam_mulai, $jam_selesai)
	{
		$sql = "
        SELECT 1
        FROM pembayaran p
        JOIN pemesanan ps ON ps.ID_PEMESANAN = p.ID_PEMESANAN_RAW
        WHERE p.STATUS_VERIF IN ('PENDING', 'CONFIRMED')
          AND ps.ID_GEDUNG = ?
          AND ps.TANGGAL_PEMESANAN = ?
          -- overlap: existing_start < new_end AND existing_end > new_start
          AND ps.JAM_PEMESANAN < ?
          AND ps.JAM_SELESAI  > ?
        LIMIT 1
    ";

		$q = $this->db->query($sql, [$id_gedung, $tanggal, $jam_selesai, $jam_mulai]);
		return $q->num_rows() > 0;
	}



	public function delete_jadwal($id_pemesanan, $data)
	{
		$this->db->where('ID_PEMESANAN', $id_pemesanan);
		$this->db->update('pemesanan_fix_detail', $data);
	}

	public function set_finish_transaction($id_pembayaran)
	{
		$id_pembayaran = (int)$id_pembayaran;
		$sql = "UPDATE pembayaran 
            SET STATUS_VERIF = 'CONFIRMED', CONFIRMED_AT = NOW()
            WHERE ID_PEMBAYARAN = $id_pembayaran";
		return $this->db->query($sql);
	}


	public function get_details_transaction($id_pembayaran)
	{
		$sql = "SELECT * FROM PEMBAYARAN WHERE ID_PEMBAYARAN = $id_pembayaran";
		$query = $this->db->query($sql);
		return $query->row();
	}

	public function get_all()
	{
		$sql = "SELECT * FROM HOME_DATA";
		$query = $this->db->query($sql);
		$hasil = $query->result_array();
		return $hasil;
	}

	public function get_all_pembayaran()
	{
		$sql = "
        SELECT 
            pb.*,
            u.NAMA_LENGKAP,
            u.perusahaan,
            u.nama_perusahaan,
            u.departemen
        FROM pembayaran pb
        LEFT JOIN pemesanan p ON p.ID_PEMESANAN = pb.ID_PEMESANAN_RAW
        LEFT JOIN user u ON u.USERNAME = p.USERNAME
        ORDER BY pb.CREATED_AT DESC
    ";
		return $this->db->query($sql)->result_array();
	}


	// atau pending saja:
	public function get_all_pembayaran_pending()
	{
		$sql = "SELECT * FROM PEMBAYARAN WHERE STATUS_VERIF='PENDING' ORDER BY CREATED_AT DESC";
		return $this->db->query($sql)->result_array();
	}


	public function get_pemesanan_flag($username)
	{
		$sql = "
        SELECT COUNT(*) AS jml
        FROM v_pemesanan v
        JOIN pemesanan p
          ON p.ID_PEMESANAN = (
            CASE
              WHEN BINARY LEFT(v.ID_PEMESANAN, 4) = 'PMSN'
                THEN CAST(SUBSTRING(v.ID_PEMESANAN, 5) AS UNSIGNED)
              ELSE CAST(v.ID_PEMESANAN AS UNSIGNED)
            END
          )
				WHERE LOWER(v.USERNAME) = LOWER(?)
					AND v.STATUS IN ('PROPOSAL APPROVE','SUBMITED','REJECTED','PROCESS')
          AND p.FLAG = 1
    ";

		$row = $this->db->query($sql, [$username])->row();
		return $row ? (int)$row->jml : 0;
	}

	public function mark_flag_unread($id_view)
	{
		$sql = "
        UPDATE pemesanan
        SET FLAG = 1
        WHERE ID_PEMESANAN = (
          CASE
            WHEN BINARY LEFT(?, 4) = 'PMSN'
              THEN CAST(SUBSTRING(?, 5) AS UNSIGNED)
            ELSE CAST(? AS UNSIGNED)
          END
        )
    ";
		return $this->db->query($sql, [$id_view, $id_view, $id_view]);
	}





	public function clear_pemesanan_flag($username)
	{
		$sql = "
        UPDATE pemesanan p
        JOIN v_pemesanan v
          ON p.ID_PEMESANAN = (
            CASE
              WHEN BINARY LEFT(v.ID_PEMESANAN, 4) = 'PMSN'
                THEN CAST(SUBSTRING(v.ID_PEMESANAN, 5) AS UNSIGNED)
              ELSE CAST(v.ID_PEMESANAN AS UNSIGNED)
            END
          )
        SET p.FLAG = 2
				WHERE LOWER(v.USERNAME) = LOWER(?)
					AND p.FLAG = 1
					AND v.STATUS IN ('PROPOSAL APPROVE','SUBMITED','REJECTED','PROCESS')
    ";

		return $this->db->query($sql, [$username]);
	}






	public function insert_pemesanan_fix_detail($data)
	{
		$this->db->insert('pemesanan_fix_detail', $data);
	}

	/**
	 * Jadwal penggunaan gedung = PEMESANAN yang sudah DIBAYAR & diverifikasi (STATUS_VERIF = CONFIRMED).
	 */
	public function jadwal_gedung($first_date = null, $second_date = null)
	{
		$sql = "
        SELECT
            ps.ID_PEMESANAN,
            ps.TANGGAL_PEMESANAN AS TANGGAL_FINAL_PEMESANAN,
            DATE(p.CONFIRMED_AT) AS TANGGAL_APPROVAL,
            g.NAMA_GEDUNG,
            COALESCE(pd.DESKRIPSI_ACARA, '-') AS DESKRIPSI_ACARA,
            1 AS FINAL_STATUS,
            ps.USERNAME,
            u.NAMA_LENGKAP,
            TIME_FORMAT(ps.JAM_PEMESANAN, '%H:%i') AS JAM_MULAI,
            TIME_FORMAT(ps.JAM_SELESAI,  '%H:%i') AS JAM_SELESAI,
            ps.TIPE_JAM AS TIPE_JAM
        FROM PEMBAYARAN p
        JOIN PEMESANAN ps ON ps.ID_PEMESANAN = p.ID_PEMESANAN_RAW
        LEFT JOIN USER u ON u.USERNAME = ps.USERNAME
        LEFT JOIN GEDUNG g ON g.ID_GEDUNG = ps.ID_GEDUNG
        LEFT JOIN PEMESANAN_DETAILS pd ON pd.ID_PEMESANAN = ps.ID_PEMESANAN
        WHERE p.STATUS_VERIF = 'CONFIRMED'
    ";

		$binds = [];
		if (!empty($first_date) && !empty($second_date)) {
			$sql .= " AND ps.TANGGAL_PEMESANAN BETWEEN ? AND ?";
			$binds[] = $first_date;
			$binds[] = $second_date;
		}

		$sql .= "
        ORDER BY ps.TANGGAL_PEMESANAN ASC,
                 ps.JAM_PEMESANAN ASC,
                 ps.ID_PEMESANAN ASC
    ";

		return $this->db->query($sql, $binds)->result_array();
	}






	public function jadwal_gedung_upcoming()
	{
		$sql = "
        SELECT 
            pfd.ID_PEMESANAN,
            pfd.TANGGAL_FINAL_PEMESANAN,
            pfd.TANGGAL_APPROVAL,
            g.NAMA_GEDUNG,
            pd.DESKRIPSI_ACARA,
            pfd.FINAL_STATUS,
            pfd.USERNAME,
            u.NAMA_LENGKAP,
            p.JAM_PEMESANAN AS JAM_MULAI,
            p.JAM_SELESAI AS JAM_SELESAI,
            p.TIPE_JAM AS TIPE_JAM
        FROM PEMESANAN_FIX_DETAIL pfd
        LEFT JOIN USER u ON u.USERNAME = pfd.USERNAME
        LEFT JOIN PEMESANAN p ON pfd.ID_PEMESANAN = p.ID_PEMESANAN
        LEFT JOIN GEDUNG g ON p.ID_GEDUNG = g.ID_GEDUNG
        LEFT JOIN PEMESANAN_DETAILS pd ON pfd.ID_PEMESANAN = pd.ID_PEMESANAN
        WHERE pfd.FINAL_STATUS = 1
          AND pfd.TANGGAL_FINAL_PEMESANAN >= CURDATE()
        ORDER BY pfd.TANGGAL_FINAL_PEMESANAN ASC, p.JAM_PEMESANAN ASC
    ";
		return $this->db->query($sql)->result_array();
	}
	public function get_last_jadwal_date_upcoming()
	{
		$sql = "
				SELECT MAX(TANGGAL_FINAL_PEMESANAN) AS last_date
				FROM PEMESANAN_FIX_DETAIL
				WHERE FINAL_STATUS = 1
				AND TANGGAL_FINAL_PEMESANAN >= CURDATE()
			";
		return $this->db->query($sql)->row();
	}
	public function fixed_date()
	{
		$sql = "
        SELECT
            v.ID_PEMESANAN,
            v.USERNAME,
            v.NAMA_GEDUNG,
            v.TANGGAL_PEMESANAN,

            -- jam mulai: dari fix_detail kalau ada, kalau tidak dari V_PEMESANAN
            TIME_FORMAT(
                COALESCE(f.JAM_MULAI, v.JAM_PEMESANAN),
                '%H:%i'
            ) AS JAM_MULAI,

           -- jam selesai: dari fix_detail kalau ada, kalau tidak dari V_PEMESANAN
TIME_FORMAT(
    COALESCE(f.JAM_SELESAI, v.JAM_SELESAI),
    '%H:%i'
) AS JAM_SELESAI

        FROM V_PEMESANAN v

        LEFT JOIN pemesanan_fix_detail f
            ON f.ID_PEMESANAN =
               CAST(REPLACE(REPLACE(REPLACE(UPPER(TRIM(v.ID_PEMESANAN)), 'PMSN', ''), ' ', ''), '0', '0') AS UNSIGNED)
            AND f.FINAL_STATUS = 1

        WHERE UPPER(TRIM(v.STATUS)) = 'SUBMITED'
        ORDER BY
            CAST(REPLACE(REPLACE(REPLACE(UPPER(TRIM(v.ID_PEMESANAN)), 'PMSN', ''), ' ', ''), '0', '0') AS UNSIGNED) DESC
    ";

		return $this->db->query($sql)->result();
	}







	public function check_date($tanggal, $id_gedung, $jam_mulai, $jam_selesai)
	{
		// status yang dianggap "aktif" (silakan sesuaikan mappingmu)
		// contoh: 0=draft, 1=submitted, 2=process, 3=confirmed
		$aktif = array(0, 1, 2, 3);
		$this->db->from('pemesanan');
		$this->db->where('ID_GEDUNG', (int)$id_gedung);
		$this->db->where('TANGGAL_PEMESANAN', $tanggal);
		$this->db->where_in('STATUS', $aktif);

		// OVERLAP RULE: existing_start < new_end AND existing_end > new_start
		$this->db->where('JAM_PEMESANAN <', $jam_selesai);
		$this->db->where('JAM_SELESAI >', $jam_mulai);

		return $this->db->count_all_results(); // >0 berarti bentrok
	}


	public function get_email_address($username)
	{
		$sql = "SELECT EMAIL FROM USER WHERE USERNAME = '$username'";
		$query = $this->db->query($sql);
		return $query->row();
	}

	public function get_gedung()
	{
		$sql = "SELECT * FROM GEDUNG";
		$query = $this->db->query($sql);
		$hasil = $query->result_array();
		return $hasil;
	}

	public function insert_pemesanan($data)
	{
		// Kalau REQUEST_ID ada, cek dulu apakah sudah pernah dibuat
		if (isset($data['REQUEST_ID']) && $data['REQUEST_ID'] !== '') {
			$exist = $this->db->get_where('pemesanan', array('REQUEST_ID' => $data['REQUEST_ID']))->row_array();
			if (!empty($exist)) {
				return (int)$exist['ID_PEMESANAN']; // sudah ada -> pakai yang lama
			}
		}

		$ok = $this->db->insert('pemesanan', $data);
		if ($ok) {
			return $this->db->insert_id();
		}

		// Jika gagal karena UNIQUE duplicate REQUEST_ID, ambil row yang sudah ada
		$err = $this->db->error(); // CI3: ['code'=>..., 'message'=>...]
		if (isset($err['code']) && (int)$err['code'] === 1062 && isset($data['REQUEST_ID'])) {
			$exist = $this->db->get_where('pemesanan', array('REQUEST_ID' => $data['REQUEST_ID']))->row_array();
			if (!empty($exist)) {
				return (int)$exist['ID_PEMESANAN'];
			}
		}

		return false;
	}




	public function get_last_id_pesanan()
	{
		$sql = "SELECT ID_PEMESANAN FROM PEMESANAN ORDER BY ID_PEMESANAN DESC LIMIT 1";
		$query = $this->db->query($sql);
		$hasil = $query->row();
		return $hasil;
	}

	public function get_pending_transaction()
	{
		$sql = "SELECT vp.ID_PEMESANAN
	FROM V_PEMESANAN vp
	JOIN pemesanan_details pd ON pd.ID_PEMESANAN = vp.ID_PEMESANAN
	WHERE vp.STATUS = 'PROCESS'";
		$q = $this->db->query($sql);
		if (!$q) return [];
		return $q->result_array();
	}
	public function get_all_pemesanan()
	{
		$sql = "
        SELECT
            vp.*,
            u.NAMA_LENGKAP     AS USER_NAMA_LENGKAP,
            u.nama_perusahaan  AS USER_NAMA_PERUSAHAAN,
            u.departemen       AS USER_DEPARTEMEN,
            u.perusahaan       AS USER_JENIS
        FROM V_PEMESANAN vp
        LEFT JOIN user u ON u.USERNAME = vp.USERNAME
        ORDER BY vp.TANGGAL_PEMESANAN DESC
    ";

		return $this->db->query($sql)->result_array();
	}


	public function get_all_pending_transaction()
	{
		$sql = "SELECT vp.*
            FROM V_PEMESANAN vp
            JOIN pemesanan_details pd
              ON pd.ID_PEMESANAN =
                 (CASE
                    WHEN vp.ID_PEMESANAN LIKE 'PMSN%' THEN CAST(SUBSTRING(vp.ID_PEMESANAN, 5) AS UNSIGNED)
                    ELSE CAST(vp.ID_PEMESANAN AS UNSIGNED)
                  END)
            WHERE vp.STATUS IN ('PROCESS','PROPOSAL APPROVE','PAID')
            ORDER BY vp.TANGGAL_PEMESANAN DESC";

		$q = $this->db->query($sql);
		if (!$q) return [];

		return $q->result_array();
	}



	public function user_detail_pembayaran($username)
	{
		$sql = "
      SELECT
        p.ID_PEMBAYARAN,
        p.ID_PEMESANAN_RAW,
        p.KODE_PEMESANAN,
        p.TANGGAL_PEMESANAN,
        p.NAMA_GEDUNG,
        p.NAMA_PAKET,
        p.TOTAL_TAGIHAN,
        p.ATAS_NAMA_PENGIRIM,
        CASE
          WHEN UPPER(TRIM(IFNULL(u.perusahaan,''))) = 'INTERNAL' AND IFNULL(p.TOTAL_TAGIHAN,0) = 0
            THEN CONCAT(IFNULL(u.NAMA_LENGKAP, ps.USERNAME), ' (AUTO)')
          ELSE p.ATAS_NAMA_PENGIRIM
        END AS ATAS_NAMA_TAMPIL,
        p.TANGGAL_TRANSFER,
        p.BANK_PENGIRIM,
        p.NOMINAL_TRANSFER,
        p.STATUS_VERIF,
        p.CREATED_AT,
        p.CONFIRMED_AT
      FROM pembayaran p
      JOIN pemesanan ps ON ps.ID_PEMESANAN = p.ID_PEMESANAN_RAW
      LEFT JOIN user u ON u.USERNAME = ps.USERNAME
      WHERE ps.USERNAME = ?
        AND p.STATUS_VERIF IN ('PENDING','CONFIRMED','REJECTED')
      ORDER BY
        (p.CONFIRMED_AT IS NULL) ASC,
        p.CONFIRMED_AT DESC,
        p.CREATED_AT DESC
    ";

		return $this->db->query($sql, array($username))->result_array();
	}




	public function get_pemesanan($username)
	{
		$sql = "SELECT * FROM V_PEMESANAN WHERE USERNAME = '$username'";
		$query = $this->db->query($sql);
		$hasil = $query->result_array();
		return $hasil;
	}

	public function count_pemesanan($username)
	{
		$sql = "SELECT * FROM PEMESANAN WHERE USERNAME = '$username'";
		$query = $this->db->query($sql);
		return $query->num_rows();
	}

	public function get_rejected_pemesanan($id_pemesanan)
	{
		$sql = "select * from pemesanan where id_pemesanan = $id_pemesanan";
		$query = $this->db->query($sql);
		return $query->row();
	}

	public function update_transaksi($id_pemesanan, $data, $remarks)
	{
		//$sql = "UPDATE pemesanan SET STATUS = $data WHERE ID_PEMESANAN = $id_pemesanan";
		$sql = "
		UPDATE PEMESANAN 
		SET STATUS = $data,
		REMARKS = (
			CASE 
			WHEN STATUS = 1 THEN NULL
			ELSE '$remarks'
			END
		)
		,FLAG = 1
		WHERE ID_PEMESANAN = $id_pemesanan";
		$query = $this->db->query($sql);
		return $query;
	}

	public function get_detail_pesanan($id_pemesanan)
	{
		$num = (int) preg_replace('/\D+/', '', (string)$id_pemesanan);
		if ($num <= 0) return null;
		$kode = 'PMSN000' . $num;

		$sql = "SELECT *
				FROM V_PEMESANAN
				WHERE ID_PEMESANAN = ?
				OR ID_PEMESANAN = ?
				LIMIT 1";

		return $this->db->query($sql, array($num, $kode))->row();
	}




	public function cancel_order($id_pemesanan, $data)
	{
		$this->db->where('ID_PEMESANAN', $id_pemesanan);
		$this->db->update('PEMESANAN', $data);
	}

	public function get_proposal_by_id($id_pemesanan)
	{
		$sql = "SELECT * FROM pemesanan_details WHERE ID_PEMESANAN = $id_pemesanan";
		$query = $this->db->query($sql);
		$hasil = $query->row();
		return $hasil;
	}

	public function update_user_flag($id_pemesanan)
	{
		$sql = "UPDATE PEMESANAN SET FLAG = 2 WHERE ID_PEMESANAN = $id_pemesanan";
		$query = $this->db->query($sql);
		return $query;
	}

	public function upload_proposal($data)
	{
		$this->db->insert('pemesanan_details', $data);
	}

	public function get_last_order()
	{
		$sql = "select 
        p.ID_PEMESANAN AS ID_PEMESANAN,
        p.USERNAME AS USERNAME,
        p.TANGGAL_PEMESANAN AS TANGGAL_PEMESANAN,

        -- TAMBAH INI
        p.JAM_PEMESANAN AS JAM_PEMESANAN,
        p.JAM_SELESAI AS JAM_SELESAI,
        p.TIPE_JAM AS TIPE_JAM,

        p.EMAIL AS EMAIL,
        coalesce(p.JUMLAH_CATERING,'Tidak Ada') AS JUMLAH_CATERING,
        coalesce(c.NAMA_PAKET,'Tidak Ada') AS NAMA_PAKET,
        g.NAMA_GEDUNG AS NAMA_GEDUNG,
        c.HARGA AS HARGA_SATUAN,
        coalesce((c.HARGA * p.JUMLAH_CATERING),0) AS TOTAL_HARGA,
        (case p.STATUS when 0 then 'PENDING' when 1 then 'DISETUJUI' when 2 then 'DITOLAK' end) AS STATUS,
        g.HARGA_SEWA AS HARGA_SEWA,
        (g.HARGA_SEWA + coalesce((c.HARGA * p.JUMLAH_CATERING),0)) AS TOTAL_KESELURUHAN,
        pemesanan_details.DESKRIPSI_ACARA AS DESKRIPSI_ACARA 
    from pemesanan p 
    left join catering c on c.ID_CATERING = p.ID_CATERING 
    left join gedung g on g.ID_GEDUNG = p.ID_GEDUNG 
    left join pemesanan_details on pemesanan_details.ID_PEMESANAN = p.ID_PEMESANAN
    order by p.ID_PEMESANAN desc
    limit 0,1";

		$query = $this->db->query($sql);
		return $query->result_array();
	}


	public function delete_pemesanan($id_pemesanan)
	{
		$this->db->where('ID_PEMESANAN', $id_pemesanan);
		$this->db->delete('gedung');
	}

	public function get_gedung_name($id_gedung)
	{
		$sql = "SELECT NAMA_GEDUNG, HARGA_SEWA FROM GEDUNG WHERE ID_GEDUNG = $id_gedung";
		$query = $this->db->query($sql);
		$hasil = $query->result_array();
		return $hasil;
	}

	public function insert_gedung_img($data)
	{
		$this->db->insert('gedung_img', $data);
	}

	public function insert_perawatan($data)
	{
		$this->db->insert('perawatan', $data);
	}

	public function get_last_id_gedung()
	{
		$sql = "SELECT MAX(ID_GEDUNG) AS ID_GEDUNG FROM GEDUNG";
		$query = $this->db->query($sql);
		$hasil = $query->row();
		return $hasil;
	}

	public function insert_gedung($data)
	{
		$this->db->insert('gedung', $data);
	}

	public function update_gedung($id_gedung, $data)
	{
		$this->db->where('ID_GEDUNG', $id_gedung);
		$this->db->update('gedung', $data);
	}

	public function delete_gedung($id_gedung)
	{
		$this->db->where('ID_GEDUNG', $id_gedung);
		$this->db->delete('gedung');
	}

	public function get_menu_catering()
	{
		$sql = "SELECT * FROM CATERING";
		$query = $this->db->query($sql);
		$hasil = $query->result_array();
		return $hasil;
	}

	public function sort_by_name()
	{
		$sql = "SELECT * FROM HOME_DATA ORDER BY NAMA_GEDUNG ASC";
		$query = $this->db->query($sql);
		$hasil = $query->result_array();
		return $hasil;
	}

	public function sort_by_capacity()
	{
		$sql = "SELECT * FROM HOME_DATA ORDER BY KAPASITAS DESC";
		$query = $this->db->query($sql);
		$hasil = $query->result_array();
		return $hasil;
	}

	public function gedung_details($id_gedung)
	{
		$query = "SELECT ID_GEDUNG, NAMA_GEDUNG, ALAMAT, DESKRIPSI_GEDUNG, KAPASITAS, HARGA_SEWA, fasilitas FROM GEDUNG WHERE ID_GEDUNG = $id_gedung";
		$sql = $this->db->query($query);
		return $sql->result_array();
	}

	public function search_gedung($nama_gedung)
	{
		$query = "SELECT ID_GEDUNG, NAMA_GEDUNG, PATH, IMG_NAME FROM HOME_DATA WHERE NAMA_GEDUNG LIKE '%$nama_gedung%'";
		$sql = $this->db->query($query);
		return $sql->result_array();
	}

	public function get_gedung_img($id_gedung)
	{
		$query = "SELECT ID_GEDUNG, PATH, IMG_NAME FROM GEDUNG_IMG WHERE ID_GEDUNG = $id_gedung";
		$sql = $this->db->query($query);
		return $sql->result_array();
	}
	public function get_order_by_id_user($id_pemesanan, $username)
	{
		$sql = "select 
        p.ID_PEMESANAN AS ID_PEMESANAN,
        p.USERNAME AS USERNAME,
        p.TANGGAL_PEMESANAN AS TANGGAL_PEMESANAN,
        p.JAM_PEMESANAN AS JAM_PEMESANAN,
        p.JAM_SELESAI AS JAM_SELESAI,
        p.TIPE_JAM AS TIPE_JAM,
        p.EMAIL AS EMAIL,
        coalesce(p.JUMLAH_CATERING,'Tidak Ada') AS JUMLAH_CATERING,
        coalesce(c.NAMA_PAKET,'Tidak Ada') AS NAMA_PAKET,
        g.NAMA_GEDUNG AS NAMA_GEDUNG,
        c.HARGA AS HARGA_SATUAN,
        coalesce((c.HARGA * p.JUMLAH_CATERING),0) AS TOTAL_HARGA,
        (case p.STATUS when 0 then 'PENDING' when 1 then 'DISETUJUI' when 2 then 'DITOLAK' end) AS STATUS,
        g.HARGA_SEWA AS HARGA_SEWA,
        (g.HARGA_SEWA + coalesce((c.HARGA * p.JUMLAH_CATERING),0)) AS TOTAL_KESELURUHAN,
        pemesanan_details.DESKRIPSI_ACARA AS DESKRIPSI_ACARA 
    from pemesanan p 
    left join catering c on c.ID_CATERING = p.ID_CATERING 
    left join gedung g on g.ID_GEDUNG = p.ID_GEDUNG 
    left join pemesanan_details on pemesanan_details.ID_PEMESANAN = p.ID_PEMESANAN
    where p.ID_PEMESANAN = ? and p.USERNAME = ?
    limit 1";

		return $this->db->query($sql, [$id_pemesanan, $username])->result_array();
	}

	public function is_order_owner($id_pemesanan, $username)
	{
		return $this->db->where('ID_PEMESANAN', $id_pemesanan)
			->where('USERNAME', $username)
			->count_all_results('pemesanan') > 0;
	}

	public function proposal_exists($id_pemesanan)
	{
		return $this->db->where('ID_PEMESANAN', $id_pemesanan)
			->count_all_results('pemesanan_details') > 0;
	}

	public function update_proposal($id_pemesanan, $data)
	{
		// jangan ubah ID_PEMESANAN saat update
		unset($data['ID_PEMESANAN']);
		$this->db->where('ID_PEMESANAN', $id_pemesanan)
			->update('pemesanan_details', $data);
	}

	public function has_confirmed_conflict($id_gedung, $tanggal, $jam_mulai, $jam_selesai)
	{
		$sql = "
			SELECT 1
			FROM V_PEMESANAN v
			WHERE v.STATUS IN ('PROPOSAL APPROVE','APPROVE & PAID','PAID')
			AND v.ID_GEDUNG = ?
			AND v.TANGGAL_PEMESANAN = ?
			AND v.JAM_PEMESANAN < ?
			AND v.JAM_SELESAI  > ?
			LIMIT 1
		";

		$q = $this->db->query($sql, [$id_gedung, $tanggal, $jam_selesai, $jam_mulai]);
		return $q->num_rows() > 0;
	}

	public function get_status_by_user($username)
	{
		return $this->db->select('ID_PEMESANAN, STATUS')
			->from('pemesanan')
			->where('USERNAME', $username)
			->order_by('ID_PEMESANAN', 'DESC')
			->get()
			->result_array();
	}
}
