<div class="doc">
    <img class="logo" src="{{ asset('image/logo_lpjk.jpg') }}" />
    <h1>HASIL PEMERIKSAAN KELENGKAPAN DAN KEBENARAN DATA DOKUMEN PERMOHONAN SKA</h1>
    <div class="clearfix"></div>
  
    <table style="margin-bottom: 40px">
      <tr>
        <td>Tanggal</td>
        <td>: {{$regta[0]->Tgl_Registrasi}}</td>
      </tr>
      <tr>
        <td>ASOSIASI</td>
        <td>: {{$regta[0]->ID_Asosiasi_Profesi}}</td>
      </tr>
      <tr>
        <td>No. /Tgl Surat</td>
        <td>: ./{{$regta[0]->Tgl_Registrasi}}</td>
      </tr>
      <tr>
        <td>Nama Pemohon</td>
        <td>: {{$regta[0]->personal->Nama}}</td>
      </tr>
      <tr>
        <td>ID Personal</td>
        <td>: {{$regta[0]->ID_Personal}}</td>
      </tr>
      <tr>
        <td>Tgl Permohonan</td>
        <td>: {{$regta[0]->Tgl_Registrasi}}</td>
      </tr>
      <tr>
        <td>Pemeriksa</td>
        <td>: </td>
      </tr>
      <tr>
        <td>Tempat USTK</td>
        <td>: {{$regta[0]->ustk->nama}}</td>
      </tr>
    </table>
    <table class="rowdata" cellpadding="0" cellspacing="0">
      <tr>
        <th>No</th>
        <th>Dokumen</th>
        <th>Ada</th>
        <th>Tidak</th>
        <th>Valid</th>
        <th>Tidak</th>
        <th>Keterangan</th>
      </tr>
      <tr>
        <td>1</td>
        <td>Surat Pernyataan Kebenaran Data Pemohon</td>
        <td class="center">V</td>
        <td class="center"></td>
        <td class="center">V</td>
        <td class="center"></td>
        <td class="center"></td>
      </tr>
      <tr>
        <td>2</td>
        <td>Photo Copy KTP</td>
        <td class="center">V</td>
        <td class="center"></td>
        <td class="center">V</td>
        <td class="center"></td>
        <td class="center"></td>
      </tr>
      <tr>
        <td>3</td>
        <td>Photo Copy NPWP Perorangan</td>
        <td class="center">V</td>
        <td class="center"></td>
        <td class="center">V</td>
        <td class="center"></td>
        <td class="center"></td>
      </tr>
      <tr>
        <td>4</td>
        <td>Daftar Riwayat Hidup</td>
        <td class="center">V</td>
        <td class="center"></td>
        <td class="center">V</td>
        <td class="center"></td>
        <td class="center"></td>
      </tr>
      <tr>
        <td>5</td>
        <td>Pas Photo Pemohon</td>
        <td class="center">V</td>
        <td class="center"></td>
        <td class="center">V</td>
        <td class="center"></td>
        <td class="center"></td>
      </tr>
      <tr>
        <td>6</td>
        <td>Data Kursus</td>
        <td class="center"></td>
        <td class="center">V</td>
        <td class="center"></td>
        <td class="center">V</td>
        <td class="center"></td>
      </tr>
      <tr>
        <td>7</td>
        <td>Fotocopy Ijazah Legalisir</td>
        <td class="center">V</td>
        <td class="center"></td>
        <td class="center">V</td>
        <td class="center"></td>
        <td class="center"></td>
      </tr>
      <tr>
        <td>8</td>
        <td>Surat Keterangan dari Universitas / Perguruan Tinggi / Sekolah</td>
        <td class="center">V</td>
        <td class="center"></td>
        <td class="center">V</td>
        <td class="center"></td>
        <td class="center"></td>
      </tr>
      <tr>
        <td>9</td>
        <td>Data Pendidikan</td>
        <td class="center">V</td>
        <td class="center"></td>
        <td class="center">V</td>
        <td class="center"></td>
        <td class="center"></td>
      </tr>
      <tr>
        <td>10</td>
        <td>Data Pengalaman Organisasi</td>
        <td class="center"></td>
        <td class="center">V</td>
        <td class="center"></td>
        <td class="center">V</td>
        <td class="center"></td>
      </tr>
      <tr>
        <td>11</td>
        <td>Data Pengalaman Kerja di Proyek</td>
        <td class="center">V</td>
        <td class="center"></td>
        <td class="center">V</td>
        <td class="center"></td>
        <td class="center"></td>
      </tr>
      <tr>
        <td>12</td>
        <td>Berita Acara Verifikasi & Validasi Awal</td>
        <td class="center">V</td>
        <td class="center"></td>
        <td class="center">V</td>
        <td class="center"></td>
        <td class="center"></td>
      </tr>
      <tr>
        <td>13</td>
        <td>Surat Permohonan</td>
        <td class="center">V</td>
        <td class="center"></td>
        <td class="center">V</td>
        <td class="center"></td>
        <td class="center"></td>
      </tr>
      <tr>
        <td>14</td>
        <td>Surat Pengantar Permohonan Asosiasi</td>
        <td class="center">V</td>
        <td class="center"></td>
        <td class="center">V</td>
        <td class="center"></td>
        <td class="center"></td>
      </tr>
      <tr>
        <td>15</td>
        <td>Photo Copy Sertifikat Keahlian (SKA) / Asli</td>
        <td class="center">V</td>
        <td class="center"></td>
        <td class="center">V</td>
        <td class="center"></td>
        <td class="center"></td>
      </tr>
      <tr>
        <td>16</td>
        <td>Penilaian Mandiri Pemohon (Self Asessment)</td>
        <td class="center">V</td>
        <td class="center"></td>
        <td class="center">V</td>
        <td class="center"></td>
        <td class="center"></td>
      </tr>
    </table>
  </div>
  
  <div class="doc">
    <img class="logo" src="{{ asset('image/logo_lpjk.jpg') }}" />
    <h1>Data Asosiasi Profesi Tenaga Ahli Konstruksi</h1>
    <div class="clearfix"></div>
  
    <table>
      <tr>
        <td>1. Nama</td>
        <td>: {{$regta[0]->personal->Nama}}</td>
      </tr>
      <tr>
        <td>2. No KTP</td>
        <td>: {{$regta[0]->ID_Personal}}</td>
      </tr>
      <tr>
        <td>3. Tgl Permohonan</td>
        <td>: {{$regta[0]->Tgl_Registrasi}}</td>
      </tr>
      <tr>
        <td>4. Tempat & Tanggal Lahir</td>
        <td>: {{$regta[0]->personal->Tempat_Lahir}}, {{$regta[0]->personal->Tgl_Lahir}}</td>
      </tr>
      <tr>
        <td>5. Alamat</td>
        <td></td>
      </tr>
      <tr>
        <td>Jalan</td>
        <td>: {{$regta[0]->personal->Alamat1}}</td>
      </tr>
      <tr>
        <td>Kota / Kabupaten</td>
        <td>: {{$regta[0]->personal->kabupaten->nama}}</td>
      </tr>
      <tr>
        <td>Kodepos</td>
        <td>: {{$regta[0]->personal->Kodepos}}</td>
      </tr>
      <tr>
        <td>Propinsi</td>
        <td>: {{$regta[0]->personal->provinsi->nama}}</td>
      </tr>
      <tr>
        <td>6. NPWP</td>
        <td>: {{$regta[0]->personal->npwp}}</td>
      </tr>
      <tr>
        <td>7. Detail Data Personal</td>
        <td>: </td>
      </tr>
    </table>
  
    <div class="nobreak">
      <h2>PENDIDIKAN</h2>
      <table class="rowdata" cellpadding="0" cellspacing="0">
        <tr>
          <th>No</th>
          <th>Tingkat Pendidikan</th>
          <th>Nama Perguruan Tinggi / Sekolah</th>
          <th>Jurusan</th>
          <th>Kota</th>
          <th>Tahun Lulus</th>
          <th>No Ijazah</th>
        </tr>
        @foreach($regta[0]->personal->pendidikan as $i => $p)
          <tr>
            <td class="center">{{$i + 1}}</td>
            <td>{{$p->jenjang->deskripsi}}</td>
            <td>{{$p->Nama_Sekolah}}</td>
            <td>{{$p->Jurusan}}</td>
            <td>{{$p->kabupaten->nama}}</td>
            <td class="center">{{$p->Tahun}}</td>
            <td>{{$p->No_Ijazah}}</td>
          </tr>
        @endforeach
      </table>
    </div>
    <div class="nobreak">
      <h2>PENGALAMAN PROYEK</h2>
      <table class="rowdata" cellpadding="0" cellspacing="0">
        <tr>
          <th>No</th>
          <th>Nama Proyek</th>
          <th>Lokasi Proyek</th>
          <th>Nilai Kontrak (Dalam Ribu)</th>
          <th>Mulai</th>
          <th>Selesai</th>
          <th>Jabatan</th>
        </tr>
        @foreach($regta[0]->personal->proyek as $i => $p)
          <tr>
            <td class="center">{{$i + 1}}</td>
            <td>{{$p->Proyek}}</td>
            <td>{{$p->lokasi->nama}}</td>
            <td>{{$p->Nilai}}</td>
            <td class="center">{{$p->Tgl_Mulai}}</td>
            <td class="center">{{$p->Tgl_Selesai}}</td>
            <td>{{$p->Jabatan}}</td>
          </tr>
        @endforeach
      </table>
    </div>
  </div>
  
  <style>
    .doc {
      page-break-after: always;
    }
    @media print {
      .landscape {
        -webkit-transform: rotate(90deg);
        -moz-transform: rotate(90deg);
        -o-transform: rotate(90deg);
        -ms-transform: rotate(90deg);
        transform: rotate(90deg);
      }
    }
    body {
      font-family: Arial, Helvetica, sans-serif;
      font-size: 12px;
    }
    h1 {
      margin-top: 60px;
      font-size: 18px;
      font-weight: normal;
    }
    h2 {
      font-size: 14px;
      font-weight: normal;
      text-align: center;
    }
    h4 {
      font-size: 12px;
      font-weight: normal;
      margin-top: 40px;
      margin-bottom: 2px;
    }
    .center {
      text-align: center;
    }
    .nobreak {
      page-break-inside: avoid;
    }
    table {
      font-size: 12px;
      page-break-inside: avoid;
    }
    table.rowdata {
      width: 100%;
    }
    th {
      padding: 4px;
      font-weight: normal;
    }
    td {
      padding: 4px;
    }
    .rowdata th, .rowdata td {
      border: 1px solid #000;
      border-right: none;
      border-bottom: none;
    }
    .rowdata th:last-child, .rowdata td:last-child {
      border-right: 1px solid #000;
    }
    .rowdata tr:last-child td {
      border-bottom: 1px solid #000;
    }
    .logo {
      width: 90px;
      float: left;
      margin-top: -52px;
      margin-right: 20px;
      margin-bottom: 50px;
    }
    .clearfix {
        clear: both;
    }
    .ttd-box {
      width: 180px;
      margin-right: 20px;
      text-align: center;
      float: left;
    }
  
    .ttd {
        height: 130px;
        border: solid 1px;
        position: relative;
    }
  
    .ttd span {
        position: absolute;
        bottom: 3px;
        left: 0;
        right: 0;
    }
    .ttd span.top {
      top: 4;
      bottom: auto;
    }
    .ttd.float {
      float: left;
      width: 48%;
    }
  </style>