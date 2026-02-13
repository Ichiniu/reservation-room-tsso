<?php
$no = 1;
$date = date_create(strtotime(time()));
?>
<!DOCTYPE html>
<html lang="en">

<body>
    <center><b>
            <h3>Laporan Aktivitas Kegiatan</h3>
        </b></center>
    <!--
	<div class="container">
		<div class="row">
		<div class="col s12 m12"> -->
    <font face="courier" size="13px">
        <table border="1">
            <tr>
                <th>No</th>
                <th>Ruangan</th>
                <th>Tanggal</th>
                <th>Total Peserta</th>
                <th>Acara</th>
                <th>Nama Penyewa</th>
            </tr>
            <?php
            $grand_total_peserta = 0;
            foreach ($report as $row):
                $tp = !empty($row['TOTAL_PESERTA']) ? (int)$row['TOTAL_PESERTA'] : 0;
                $grand_total_peserta += $tp;
            ?>
                <tr>
                    <td><?php echo $no++ ?></td>
                    <td><?php echo $row['NAMA_GEDUNG'] ?></td>
                    <td><?php echo $row['TANGGAL_FINAL_PEMESANAN'] ?></td>
                    <td style="text-align: center;"><?php echo $tp; ?></td>
                    <td><?php echo $row['DESKRIPSI_ACARA'] ?></td>
                    <td><?php echo $row['NAMA_LENGKAP'] ?></td>
                </tr>
            <?php endforeach; ?>
            <tr style="background-color: #f0f0f0; font-weight: bold;">
                <td colspan="3" style="text-align: right;">GRAND TOTAL PESERTA</td>
                <td style="text-align: center;"><?php echo $grand_total_peserta; ?></td>
                <td colspan="2"></td>
            </tr>
        </table>
        <b>Periode
            <?php echo date_format(date_create($start_date), 'd F Y') ?>-<?php echo date_format(date_create($end_date), 'd F Y') ?></b><br>
        <b>Dicetak pada: </b>
        <b><?php echo date_format($date, "d M y") ?></b></td><br>
        <b>Dicetak untuk: Pengelola</b>
    </font>
    <!--
		</div>
		</div>
	</div> -->
</body>

</html>