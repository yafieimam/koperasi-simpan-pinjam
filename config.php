<?php return array (
  'adminlte' => 
  array (
    'title' => 'Koperasi Security BSP',
    'title_prefix' => '',
    'title_postfix' => '',
    'logo' => '<b>KSBSP</b>',
    'logo_mini' => '<b>B</b>SP',
    'skin' => 'red-light',
    'layout' => 'fixed',
    'collapse_sidebar' => false,
    'right_sidebar' => false,
    'right_sidebar_icon' => 'fas fa-cogs',
    'right_sidebar_theme' => 'dark',
    'right_sidebar_slide' => true,
    'dashboard_url' => 'dashboard',
    'logout_url' => 'logout',
    'logout_method' => NULL,
    'login_url' => 'login',
    'register_url' => 'register',
    'menu' => 
    array (
      0 => 
      array (
        'text' => 'Dashboard',
        'url' => 'dashboard',
        'icon' => 'tachometer',
        'active' => 
        array (
          0 => 'dashboard',
        ),
      ),
      1 => 
      array (
        'text' => 'Profile',
        'url' => 'my-profile',
        'icon' => 'address-card',
        'label' => '',
        'label_color' => 'success',
        'role' => 
        array (
          0 => 'MEMBER',
          1 => 'DANSEK',
          2 => 'ADMIN_AREA',
          3 => 'ADMIN_KOPERASI',
          4 => 'SUPERVISOR',
          5 => 'PENGAWAS_1',
          6 => 'PENGAWAS_2',
          7 => 'PENGAWAS_3',
          8 => 'PENGURUS_1',
          9 => 'PENGURUS_2',
          10 => 'GENERAL_MANAGER',
          11 => 'MANAGER',
          12 => 'DIREKTUR_UTAMA',
          13 => 'DIREKTUR',
        ),
      ),
      2 => 
      array (
        'text' => 'Kartu Anggota',
        'url' => 'member/kartu-anggota',
        'icon' => 'credit-card',
        'label' => '',
        'label_color' => 'success',
        'role' => 
        array (
          0 => 'MEMBER',
          1 => 'DANSEK',
          2 => 'ADMIN_AREA',
          3 => 'ADMIN_KOPERASI',
          4 => 'SUPERVISOR',
          5 => 'PENGAWAS_1',
          6 => 'PENGAWAS_2',
          7 => 'PENGAWAS_3',
          8 => 'PENGURUS_1',
          9 => 'PENGURUS_2',
          10 => 'GENERAL_MANAGER',
          11 => 'MANAGER',
          12 => 'DIREKTUR_UTAMA',
          13 => 'DIREKTUR',
        ),
      ),
      3 => 
      array (
        'text' => 'Anggota',
        'url' => 'members',
        'icon' => 'users',
        'label' => '',
        'label_color' => 'success',
        'role' => 
        array (
          0 => 'SUPERADMIN',
          1 => 'POWERADMIN',
          2 => 'PENGAWAS_1',
          3 => 'PENGAWAS_2',
          4 => 'ADMIN_AREA',
          5 => 'PENGAWAS_3',
          6 => 'ADMIN_KOPERASI',
          7 => 'MANAGER',
          8 => 'GENERAL_MANAGER',
        ),
      ),
      4 => 
      array (
        'text' => 'Simpan Pinjam',
        'icon' => 'handshake-o',
        'role' => 
        array (
          0 => 'MEMBER',
          1 => 'DANSEK',
          2 => 'ADMIN_AREA',
          3 => 'ADMIN_KOPERASI',
          4 => 'SUPERVISOR',
          5 => 'PENGAWAS_1',
          6 => 'PENGAWAS_2',
          7 => 'PENGAWAS_3',
          8 => 'PENGURUS_1',
          9 => 'PENGURUS_2',
          10 => 'GENERAL_MANAGER',
          11 => 'MANAGER',
          12 => 'DIREKTUR_UTAMA',
          13 => 'DIREKTUR',
        ),
        'submenu' => 
        array (
          0 => 
          array (
            'text' => 'Plafon Anggota',
            'url' => 'plafons',
            'can' => 'view.member.plafond',
          ),
          1 => 
          array (
            'text' => 'Pinjaman',
            'url' => 'member-loans',
          ),
          2 => 
          array (
            'text' => 'Pengajuan Pinjaman',
            'url' => 'loan-aggrement',
          ),
          3 => 
          array (
            'text' => 'Simpanan',
            'url' => 'member-deposits',
          ),
          4 => 
          array (
            'text' => 'Pengambilan Simpanan',
            'url' => 'retrieve-member-deposits',
          ),
          5 => 
          array (
            'text' => 'Perubahan Simpanan',
            'url' => 'change-member-deposits',
          ),
        ),
      ),
      5 => 
      array (
        'text' => 'Pengunduran Diri',
        'icon' => 'sign-out',
        'role' => 
        array (
          0 => 'MEMBER',
          1 => 'DANSEK',
          2 => 'ADMIN_AREA',
          3 => 'ADMIN_KOPERASI',
          4 => 'SUPERVISOR',
          5 => 'PENGAWAS_1',
          6 => 'PENGAWAS_2',
          7 => 'PENGAWAS_3',
          8 => 'PENGURUS_1',
          9 => 'PENGURUS_2',
          10 => 'GENERAL_MANAGER',
          11 => 'MANAGER',
          12 => 'DIREKTUR_UTAMA',
          13 => 'DIREKTUR',
        ),
        'submenu' => 
        array (
          0 => 
          array (
            'text' => 'Diajukan',
            'url' => 'resign',
            'can' => 'view.report.resign',
          ),
          1 => 
          array (
            'text' => 'Form Pengajuan',
            'url' => 'resign/create',
          ),
        ),
      ),
      6 => 
      array (
        'text' => 'Tanya Jawab',
        'url' => 'qna',
        'icon' => 'question-circle',
        'role' => 
        array (
          0 => 'MEMBER',
          1 => 'DANSEK',
        ),
      ),
      7 => 
      array (
        'text' => 'Master',
        'icon' => 'sitemap',
        'can' => 'view.member.bank',
        'submenu' => 
        array (
          0 => 
          array (
            'text' => 'Bank',
            'url' => 'member/bank',
          ),
        ),
      ),
      8 => 
      array (
        'text' => 'Transaksi',
        'icon' => 'briefcase',
        'can' => 'view.transaction.management',
        'submenu' => 
        array (
          0 => 
          array (
            'text' => 'Plafon Anggota',
            'url' => 'plafons',
            'can' => 'view.member.plafond',
          ),
          1 => 
          array (
            'text' => 'Pinjaman',
            'url' => 'get-loans',
            'can' => 'view.member.loan',
          ),
          2 => 
          array (
            'text' => 'Simpanan',
            'url' => 'get-deposits',
            'can' => 'view.member.deposit',
          ),
          3 => 
          array (
            'text' => 'Pengambilan Simpanan',
            'url' => 'pengambilan-simpanan',
            'can' => 'view.transaction.member.retrieve.deposit',
          ),
          4 => 
          array (
            'text' => 'Perubahan Simpanan',
            'url' => 'perubahan-simpanan',
            'can' => 'view.transaction.member.change.deposit',
          ),
          5 => 
          array (
            'text' => 'Persetujuan Pinjaman',
            'url' => 'persetujuan-pinjaman',
            'can' => 'view.transaction.member.loan',
          ),
          6 => 
          array (
            'text' => 'Tambah Simpanan',
            'url' => 'tambah-simpanan',
            'can' => 'create.member.deposit',
          ),
          7 => 
          array (
            'text' => 'Penambahan Simpanan',
            'url' => 'penambahan-simpanan',
            'can' => 'view.member.deposit',
          ),
        ),
      ),
      9 => 
      array (
        'text' => 'Generate Laporan',
        'icon' => 'folder',
        'can' => 'view.report',
        'submenu' => 
        array (
          0 => 
          array (
            'text' => 'Simpanan',
            'url' => 'generate/deposit-report',
            'can' => 'view.generate.report.deposit',
          ),
          1 => 
          array (
            'text' => 'Simpanan Anggota',
            'url' => 'generate/deposit-member-report',
            'can' => 'view.generate.report.deposit',
          ),
          2 => 
          array (
            'text' => 'Pinjaman',
            'url' => 'generate/loan-report',
            'can' => 'view.report.loan',
          ),
          3 => 
          array (
            'text' => 'Pencairan Pinjaman',
            'url' => 'generate/pencairan-pinjaman',
            'can' => 'view.report.loan',
          ),
          4 =>
          array (
            'text' => 'Piutang Pinjaman',
            'url' => 'generate/piutang-pinjaman',
            'can' => 'view.generate.report.member',
          ),
          5 => 
          array (
            'text' => 'Anggota',
            'url' => 'generate/member-report',
            'can' => 'view.generate.report.member',
          ),
          6 => 
          array (
            'text' => 'Anggota Area dan Proyek',
            'url' => 'generate/member-report-area-proyek',
            'can' => 'view.generate.report.member',
          ),
          7 => 
          array (
            'text' => 'Anggota Resign',
            'url' => 'generate/member-resign',
            'can' => 'view.generate.report.member',
          ),
          8 => 
          array (
            'text' => 'Rekap Anggota',
            'url' => 'generate/rekap-anggota',
            'can' => 'view.generate.report.member',
          ),
          9 => 
          array (
            'text' => 'Titipan Sisa Hak Anggota',
            'url' => 'generate/sisa-hak-anggota',
            'can' => 'view.generate.report.member',
          ),
          10 => 
          array (
            'text' => 'Kredit Bermasalah (NPL)',
            'url' => 'generate/kredit-bermasalah',
            'can' => 'view.generate.report.member',
          ),
          11 => 
          array (
            'text' => 'Potongan Simpan Pinjam',
            'url' => 'generate/potongan-simpan-pinjam',
            'can' => 'view.generate.report.member',
          ),
          12 => 
          array (
            'text' => 'Pendapatan Provisi,Jasa,Admin',
            'url' => 'generate/pendapatan-provisi-jasa-admin',
            'can' => 'view.generate.report.member',
          ),
          13 => 
          array (
            'text' => 'Pengambilan Sukarela',
            'url' => 'generate/pengambilan-sukarela',
            'can' => 'view.generate.report.member',
          ),
        ),
      ),
      10 => 
      array (
        'text' => 'Resign',
        'icon' => 'handshake-o',
        'url' => 'list-resign',
        'can' => 'view.member.resign',
      ),
      11 => 
      array (
        'text' => 'Berita',
        'url' => 'article',
        'icon' => 'newspaper-o',
        'can' => 'view.master.article',
      ),
      12 => 
      array (
        'text' => 'SHU',
        'url' => 'shu',
        'icon' => 'cog',
        'can' => 'view.master.shu',
      ),
      13 => 
      array (
        'text' => 'Tipe Transaksi',
        'icon' => 'briefcase',
        'can' => 'view.master.transaction-type',
        'submenu' => 
        array (
          0 => 
          array (
            'text' => 'Daftar Pinjaman',
            'url' => 'loans',
            'can' => 'view.master.loan',
          ),
          1 => 
          array (
            'text' => 'Tipe Deposito/Simpanan',
            'url' => 'deposits',
            'can' => 'view.master.deposit',
          ),
        ),
      ),
      14 => 
      array (
        'text' => 'Proyek',
        'icon' => 'file-powerpoint-o',
        'can' => 'view.project.management',
        'submenu' => 
        array (
          0 => 
          array (
            'text' => 'Daftar Proyek',
            'url' => 'projects',
            'can' => 'view.master.project',
          ),
          1 => 
          array (
            'text' => 'Daftar Area',
            'url' => 'regions',
            'can' => 'view.master.area',
          ),
          2 => 
          array (
            'text' => 'Daftar Cabang',
            'url' => 'branch',
            'can' => 'view.master.branch',
          ),
          3 => 
          array (
            'text' => 'Daftar Lokasi',
            'url' => 'locations',
            'can' => 'view.master.location',
          ),
        ),
      ),
      15 => 
      array (
        'text' => 'Posisi',
        'url' => 'positions',
        'icon' => 'sitemap',
        'can' => 'view.master.position',
      ),
      16 => 
      array (
        'text' => 'Privacy Policy',
        'url' => 'policy',
        'icon' => 'shield',
        'can' => 'view.master.policy',
      ),
      17 => 
      array (
        'text' => 'Pengaturan Akun',
        'icon' => 'address-card',
        'can' => 'update.account.configuration',
        'submenu' => 
        array (
          0 => 
          array (
            'text' => 'Ubah Password',
            'url' => 'account-setting/password',
            'can' => 'update.account.configuration',
          ),
          1 => 
          array (
            'text' => 'Ubah Email',
            'url' => 'account-setting/email',
            'can' => 'update.account.configuration',
          ),
        ),
      ),
      18 => 
      array (
        'text' => 'Otentifikasi',
        'icon' => 'lock',
        'can' => 'view.auth.management',
        'submenu' => 
        array (
          0 => 
          array (
            'text' => 'Daftar User',
            'url' => 'users',
            'can' => 'view.auth.user',
          ),
          1 => 
          array (
            'text' => 'Level User',
            'url' => 'levels',
            'can' => 'view.auth.level',
          ),
          2 => 
          array (
            'text' => 'Permisions User',
            'url' => 'permissions',
            'can' => 'view.auth.level',
          ),
        ),
      ),
      19 => 
      array (
        'text' => 'Import Data',
        'icon' => 'file-excel-o',
        'can' => 'view.management.import',
        'submenu' => 
        array (
          0 => 
          array (
            'text' => 'Anggota',
            'url' => 'importdata',
          ),
          1 => 
          array (
            'text' => 'Admin',
            'url' => 'importdata-admin',
          ),
        ),
      ),
      20 => 
      array (
        'text' => 'Tanya Jawab',
        'url' => 'qna',
        'icon' => 'question-circle',
        'can' => 'view.master.qna',
      ),
      21 => 
      array (
        'text' => 'General Setting',
        'url' => 'setting',
        'icon' => 'cog',
        'can' => 'view.general.setting',
      ),
    ),
    'filters' => 
    array (
      0 => 'JeroenNoten\\LaravelAdminLte\\Menu\\Filters\\HrefFilter',
      1 => 'JeroenNoten\\LaravelAdminLte\\Menu\\Filters\\ActiveFilter',
      2 => 'JeroenNoten\\LaravelAdminLte\\Menu\\Filters\\SubmenuFilter',
      3 => 'JeroenNoten\\LaravelAdminLte\\Menu\\Filters\\ClassesFilter',
      4 => 'App\\Helpers\\BSPMenuFilter',
    ),
    'plugins' => 
    array (
      'datatables' => true,
      'select2' => true,
      'chartjs' => true,
    ),
  ),
  'adminlte_' => 
  array (
    'title' => 'Bravo',
    'title_prefix' => '',
    'title_postfix' => '',
    'logo' => '<b>BSP</b>',
    'logo_mini' => '<b>B</b>SP',
    'skin' => 'red-light',
    'layout' => 'fixed',
    'collapse_sidebar' => false,
    'dashboard_url' => 'home',
    'logout_url' => 'logout',
    'logout_method' => NULL,
    'login_url' => 'login',
    'register_url' => 'register',
    'menu' => 
    array (
      0 => 
      array (
        'text' => 'Dashboard',
        'url' => 'dashboard',
        'icon' => 'tachometer',
      ),
      1 => 
      array (
        'text' => 'Anggota',
        'url' => 'members',
        'icon' => 'users',
        'label' => '',
        'label_color' => 'success',
        'can' => 'view.member.total_member',
      ),
      2 => 'TRANSAKSI ANGGOTA',
      3 => 
      array (
        'text' => 'Simpan Pinjam',
        'icon' => 'handshake-o',
        'submenu' => 
        array (
          0 => 
          array (
            'text' => 'Pinjaman',
            'url' => 'member-loans',
          ),
          1 => 
          array (
            'text' => 'Simpanan',
            'url' => 'member-deposits',
          ),
        ),
      ),
      4 => 
      array (
        'text' => 'Transactions',
        'icon' => 'briefcase',
        'can' => 'view.loan.ts_loan_all',
        'submenu' => 
        array (
          0 => 
          array (
            'text' => 'Plafon Anggota',
            'url' => 'plafons',
          ),
          1 => 
          array (
            'text' => 'Pinjaman',
            'url' => 'get-loans',
          ),
          2 => 
          array (
            'text' => 'Simpanan',
            'url' => 'ts-deposits',
          ),
        ),
      ),
      5 => 'KELOLA MASTER DATA',
      6 => 
      array (
        'text' => 'Tipe Transaksi',
        'icon' => 'briefcase',
        'can' => 'view.transaction.management',
        'submenu' => 
        array (
          0 => 
          array (
            'text' => 'Daftar Pinjaman',
            'url' => 'loans',
          ),
          1 => 
          array (
            'text' => 'Tipe Deposito/Simpanan',
            'url' => 'deposits',
          ),
        ),
      ),
      7 => 
      array (
        'text' => 'Proyek',
        'icon' => 'file-powerpoint-o',
        'can' => 'view.project.management',
        'submenu' => 
        array (
          0 => 
          array (
            'text' => 'Daftar Proyek',
            'url' => 'projects',
            'can' => 'view.master.project',
          ),
          1 => 
          array (
            'text' => 'Daftar Area',
            'url' => 'regions',
            'can' => 'view.master.area',
          ),
          2 => 
          array (
            'text' => 'Daftar Lokasi',
            'url' => 'locations',
            'can' => 'view.master.location',
          ),
        ),
      ),
      8 => 
      array (
        'text' => 'Posisi',
        'url' => 'positions',
        'icon' => 'sitemap',
        'can' => 'view.master.position',
      ),
      9 => 
      array (
        'text' => 'Otentifikasi',
        'icon' => 'lock',
        'can' => 'view.auth.management',
        'submenu' => 
        array (
          0 => 
          array (
            'text' => 'Daftar User',
            'url' => 'users',
            'can' => 'view.auth.user',
          ),
          1 => 
          array (
            'text' => 'Level User',
            'url' => 'levels',
            'can' => 'view.auth.level',
          ),
        ),
      ),
    ),
    'filters' => 
    array (
      0 => 'JeroenNoten\\LaravelAdminLte\\Menu\\Filters\\HrefFilter',
      1 => 'JeroenNoten\\LaravelAdminLte\\Menu\\Filters\\ActiveFilter',
      2 => 'JeroenNoten\\LaravelAdminLte\\Menu\\Filters\\SubmenuFilter',
      3 => 'JeroenNoten\\LaravelAdminLte\\Menu\\Filters\\ClassesFilter',
      4 => 'JeroenNoten\\LaravelAdminLte\\Menu\\Filters\\GateFilter',
    ),
    'plugins' => 
    array (
      'datatables' => true,
      'select2' => true,
      'chartjs' => true,
    ),
  ),
  'app' => 
  array (
    'db_fields' => 
    array (
      0 => 'status',
      1 => 'project',
      2 => 'no_koperasi',
      3 => 'nama',
      4 => 'no_register',
      5 => 'no_rekening',
      6 => 'bank',
      7 => 'pokok',
      8 => 'wajib',
      9 => 'sukarela',
      10 => 'angsuran_tunai',
      11 => 'jasa_tunai',
      12 => 'tenor_tunai',
      13 => 'bln_tunai',
      14 => 'tgl_tunai_cair',
      15 => 'mulai_tunai',
      16 => 'akhir_tunai',
      17 => 'angsuran_tunai1',
      18 => 'jasa_tunai1',
      19 => 'tenor_tunai1',
      20 => 'bln_tunai1',
      21 => 'tgl_tunai_cair1',
      22 => 'mulai_tunai1',
      23 => 'akhir_tunai1',
      24 => 'angsuran_tunai2',
      25 => 'jasa_tunai2',
      26 => 'tenor_tunai2',
      27 => 'bln_tunai2',
      28 => 'tgl_tunai_cair2',
      29 => 'mulai_tunai2',
      30 => 'akhir_tunai2',
      31 => 'angsuran_tunai3',
      32 => 'jasa_tunai3',
      33 => 'tenor_tunai3',
      34 => 'bln_tunai3',
      35 => 'tgl_tunai_cair3',
      36 => 'mulai_tunai3',
      37 => 'akhir_tunai3',
      38 => 'angsuran_tunai4',
      39 => 'jasa_tunai4',
      40 => 'tenor_tunai4',
      41 => 'bln_tunai4',
      42 => 'tgl_tunai_cair4',
      43 => 'mulai_tunai4',
      44 => 'akhir_tunai4',
      45 => 'angsuran_tunai5',
      46 => 'jasa_tunai5',
      47 => 'tenor_tunai5',
      48 => 'bln_tunai5',
      49 => 'tgl_tunai_cair5',
      50 => 'mulai_tunai5',
      51 => 'akhir_tunai5',
      52 => 'angsuran_tunai6',
      53 => 'jasa_tunai6',
      54 => 'tenor_tunai6',
      55 => 'bln_tunai6',
      56 => 'tgl_tunai_cair6',
      57 => 'mulai_tunai6',
      58 => 'akhir_tunai6',
      59 => 'angsuran_pinjaman_barang1',
      60 => 'jasa_pinjaman_barang1',
      61 => 'tenor_pinjaman_barang1',
      62 => 'bln_pinjaman_barang1',
      63 => 'tgl_pinjaman_barang1',
      64 => 'mulai_pinjaman_barang1',
      65 => 'akhir_pinjaman_barang1',
      66 => 'angsuran_pinjaman_barang2',
      67 => 'jasa_pinjaman_barang2',
      68 => 'tenor_pinjaman_barang2',
      69 => 'bln_pinjaman_barang2',
      70 => 'tgl_pinjaman_barang2',
      71 => 'mulai_pinjaman_barang2',
      72 => 'akhir_pinjaman_barang2',
      73 => 'angsuran_pinjaman_pendidikan',
      74 => 'jasa_pinjaman_pendidikan',
      75 => 'tenor_pinjaman_pendidikan',
      76 => 'bln_pinjaman_pendidikan',
      77 => 'tgl_pinjaman_pendidikan',
      78 => 'mulai_pinjaman_pendidikan',
      79 => 'akhir_pinjaman_pendidikan',
      80 => 'angsuran_pinjaman_darurat',
      81 => 'jasa_pinjaman_darurat',
      82 => 'tenor_pinjaman_darurat',
      83 => 'bln_pinjaman_darurat',
      84 => 'tgl_pinjaman_darurat',
      85 => 'mulai_pinjaman_darurat',
      86 => 'akhir_pinjaman_darurat',
      87 => 'angsuran_softloan',
      88 => 'jasa_softloan',
      89 => 'tenor_softloan',
      90 => 'bln_softloan',
      91 => 'tgl_softloan',
      92 => 'mulai_softloan',
      93 => 'akhir_softloan',
      94 => 'angsuran_motorloan',
      95 => 'jasa_motorloan',
      96 => 'tenor_motorloan',
      97 => 'bln_motorloan',
      98 => 'tgl_motorloan',
      99 => 'mulai_motorloan',
      100 => 'akhir_motorloan',
      101 => 'total',
      102 => 'lokasi_proyek',
      103 => 'mulai',
      104 => 'area',
      105 => 'pic',
      106 => 'payroll',
      107 => 'pokok',
      108 => 'wajib',
      109 => 'sukarela',
      110 => 'shu_ditahan',
      111 => 'lainnya',
      112 => 'ktp',
      113 => 'alamat',
      114 => 'no_hp',
      115 => 'tgl_bergabung',
      116 => 'end',
      117 => 'special_member',
      118 => 'plafon',
      119 => 'jabatan',
      120 => 'id',
      121 => 'code',
      122 => 'pks_awal',
      123 => 'pks_akhir',
      124 => 'spp_pks',
      125 => 'email',
    ),
    'name' => 'BSP Koperasi',
    'env' => 'local',
    'debug' => true,
    'url' => 'http://koperasibspguard.com',
    'timezone' => 'Asia/Jakarta',
    'locale' => 'id',
    'fallback_locale' => 'en',
    'faker_locale' => 'en_US',
    'key' => 'base64:7sRo/V2Ig/IFd67FdjId5h/1s7mVN34wUD3UURGIOLs=',
    'cipher' => 'AES-256-CBC',
    'providers' => 
    array (
      0 => 'Illuminate\\Auth\\AuthServiceProvider',
      1 => 'Illuminate\\Broadcasting\\BroadcastServiceProvider',
      2 => 'Illuminate\\Bus\\BusServiceProvider',
      3 => 'Illuminate\\Cache\\CacheServiceProvider',
      4 => 'Illuminate\\Foundation\\Providers\\ConsoleSupportServiceProvider',
      5 => 'Illuminate\\Cookie\\CookieServiceProvider',
      6 => 'Illuminate\\Database\\DatabaseServiceProvider',
      7 => 'Illuminate\\Encryption\\EncryptionServiceProvider',
      8 => 'Illuminate\\Filesystem\\FilesystemServiceProvider',
      9 => 'Illuminate\\Foundation\\Providers\\FoundationServiceProvider',
      10 => 'Illuminate\\Hashing\\HashServiceProvider',
      11 => 'Illuminate\\Mail\\MailServiceProvider',
      12 => 'Illuminate\\Notifications\\NotificationServiceProvider',
      13 => 'Illuminate\\Pagination\\PaginationServiceProvider',
      14 => 'Illuminate\\Pipeline\\PipelineServiceProvider',
      15 => 'Illuminate\\Queue\\QueueServiceProvider',
      16 => 'Illuminate\\Redis\\RedisServiceProvider',
      17 => 'Illuminate\\Auth\\Passwords\\PasswordResetServiceProvider',
      18 => 'Illuminate\\Session\\SessionServiceProvider',
      19 => 'Illuminate\\Translation\\TranslationServiceProvider',
      20 => 'Illuminate\\Validation\\ValidationServiceProvider',
      21 => 'Illuminate\\View\\ViewServiceProvider',
      22 => 'Spatie\\Permission\\PermissionServiceProvider',
      23 => 'Collective\\Html\\HtmlServiceProvider',
      24 => 'Yajra\\DataTables\\DataTablesServiceProvider',
      25 => 'App\\Providers\\AppServiceProvider',
      26 => 'App\\Providers\\AuthServiceProvider',
      27 => 'App\\Providers\\EventServiceProvider',
      28 => 'App\\Providers\\TelescopeServiceProvider',
      29 => 'App\\Providers\\RouteServiceProvider',
      30 => 'App\\Providers\\NavigationServiceProvider',
      31 => 'App\\Providers\\GeneralHelperServiceProvider',
      32 => 'Collective\\Html\\HtmlServiceProvider',
      33 => 'Spatie\\Permission\\PermissionServiceProvider',
      34 => 'Berkayk\\OneSignal\\OneSignalServiceProvider',
      35 => 'Unisharp\\Ckeditor\\ServiceProvider',
      36 => 'Maatwebsite\\Excel\\ExcelServiceProvider',
      37 => 'Tymon\\JWTAuth\\Providers\\LaravelServiceProvider',
      38 => 'Barryvdh\\DomPDF\\ServiceProvider',
    ),
    'aliases' => 
    array (
      'App' => 'Illuminate\\Support\\Facades\\App',
      'Artisan' => 'Illuminate\\Support\\Facades\\Artisan',
      'Auth' => 'Illuminate\\Support\\Facades\\Auth',
      'Blade' => 'Illuminate\\Support\\Facades\\Blade',
      'Broadcast' => 'Illuminate\\Support\\Facades\\Broadcast',
      'Bus' => 'Illuminate\\Support\\Facades\\Bus',
      'Cache' => 'Illuminate\\Support\\Facades\\Cache',
      'Config' => 'Illuminate\\Support\\Facades\\Config',
      'Cookie' => 'Illuminate\\Support\\Facades\\Cookie',
      'Crypt' => 'Illuminate\\Support\\Facades\\Crypt',
      'DB' => 'Illuminate\\Support\\Facades\\DB',
      'Eloquent' => 'Illuminate\\Database\\Eloquent\\Model',
      'Event' => 'Illuminate\\Support\\Facades\\Event',
      'File' => 'Illuminate\\Support\\Facades\\File',
      'Gate' => 'Illuminate\\Support\\Facades\\Gate',
      'Hash' => 'Illuminate\\Support\\Facades\\Hash',
      'Lang' => 'Illuminate\\Support\\Facades\\Lang',
      'Log' => 'Illuminate\\Support\\Facades\\Log',
      'Mail' => 'Illuminate\\Support\\Facades\\Mail',
      'Notification' => 'Illuminate\\Support\\Facades\\Notification',
      'Password' => 'Illuminate\\Support\\Facades\\Password',
      'Queue' => 'Illuminate\\Support\\Facades\\Queue',
      'Redirect' => 'Illuminate\\Support\\Facades\\Redirect',
      'Redis' => 'Illuminate\\Support\\Facades\\Redis',
      'Request' => 'Illuminate\\Support\\Facades\\Request',
      'Response' => 'Illuminate\\Support\\Facades\\Response',
      'Route' => 'Illuminate\\Support\\Facades\\Route',
      'Schema' => 'Illuminate\\Support\\Facades\\Schema',
      'Session' => 'Illuminate\\Support\\Facades\\Session',
      'Storage' => 'Illuminate\\Support\\Facades\\Storage',
      'URL' => 'Illuminate\\Support\\Facades\\URL',
      'Validator' => 'Illuminate\\Support\\Facades\\Validator',
      'View' => 'Illuminate\\Support\\Facades\\View',
      'Form' => 'Collective\\Html\\FormFacade',
      'Html' => 'Collective\\Html\\HtmlFacade',
      'DataTables' => 'Yajra\\Datatables\\Facades\\Datatables',
      'OneSignal' => 'Berkayk\\OneSignal\\OneSignalFacade',
      'Excel' => 'Maatwebsite\\Excel\\Facades\\Excel',
      'PDF' => 'Barryvdh\\DomPDF\\Facade',
    ),
  ),
  'auth' => 
  array (
    'defaults' => 
    array (
      'guard' => 'web',
      'passwords' => 'users',
    ),
    'guards' => 
    array (
      'web' => 
      array (
        'driver' => 'session',
        'provider' => 'users',
      ),
      'api' => 
      array (
        'driver' => 'jwt',
        'provider' => 'users',
      ),
    ),
    'providers' => 
    array (
      'users' => 
      array (
        'driver' => 'eloquent',
        'model' => 'App\\User',
      ),
    ),
    'passwords' => 
    array (
      'users' => 
      array (
        'provider' => 'users',
        'table' => 'password_resets',
        'expire' => 60,
      ),
    ),
    'url_local' => 'http://bravokoperasi.test/',
    'url_server' => 'http://115.124.64.250:84/',
    'level' => 
    array (
      'POWERADMIN' => 
      array (
        'name' => 'POWERADMIN',
        'friendly_name' => 'Power Admin',
      ),
      'SUPERADMIN' => 
      array (
        'name' => 'SUPERADMIN',
        'friendly_name' => 'Super Admin',
      ),
      'PENGAWAS1' => 
      array (
        'name' => 'PENGAWAS_1',
        'friendly_name' => 'Pengawas 1',
      ),
      'PENGAWAS2' => 
      array (
        'name' => 'PENGAWAS_2',
        'friendly_name' => 'Pengawas 2',
      ),
      'PENGAWAS3' => 
      array (
        'name' => 'PENGAWAS_3',
        'friendly_name' => 'Pengawas 3',
      ),
      'PENGURUS1' => 
      array (
        'name' => 'PENGURUS_1',
        'friendly_name' => 'Pengurus 1',
      ),
      'PENGURUS2' => 
      array (
        'name' => 'PENGURUS_2',
        'friendly_name' => 'Pengurus 2',
      ),
      'SUPERVISOR' => 
      array (
        'name' => 'SUPERVISOR',
        'friendly_name' => 'Supervisor',
      ),
      'ADMINKOPERASI' => 
      array (
        'name' => 'ADMIN_KOPERASI',
        'friendly_name' => 'Admin Koperasi',
      ),
      'GENERALMANAGER' => 
      array (
        'name' => 'GENERAL_MANAGER',
        'friendly_name' => 'General Manager',
      ),
      'MANAGER' => 
      array (
        'name' => 'MANAGER',
        'friendly_name' => 'Manager',
      ),
      'ADMINAREA' => 
      array (
        'name' => 'ADMIN_AREA',
        'friendly_name' => 'Admin Area',
      ),
      'DANSEK' => 
      array (
        'name' => 'DANSEK',
        'friendly_name' => 'Dansek',
      ),
      'ANGGOTA' => 
      array (
        'name' => 'MEMBER',
        'friendly_name' => 'Anggota',
      ),
      'DIREKTURUTAMA' => 
      array (
        'name' => 'DIREKTUR_UTAMA',
        'friendly_name' => 'Direktur Utama',
      ),
      'DIREKTUR' => 
      array (
        'name' => 'DIREKTUR',
        'friendly_name' => 'Direktur',
      ),
      'PENDIRI' => 
      array (
        'name' => 'PENDIRI',
        'friendly_name' => 'Pendiri',
      ),
      'KARYAWANPENGELOLA' => 
      array (
        'name' => 'KARYAWAN_PENGELOLA',
        'friendly_name' => 'Karyawan Pengelola',
      ),
      'KARYAWANKOPERASI' => 
      array (
        'name' => 'KARYAWAN_KOPERASI',
        'friendly_name' => 'Karyawan Koperasi',
      ),
      'PENGELOLAAREA' => 
      array (
        'name' => 'PENGELOLA_AREA',
        'friendly_name' => 'Pengelola Area',
      ),
      'KOMISARIS' => 
      array (
        'name' => 'KOMISARIS',
        'friendly_name' => 'Komisaris',
      ),
      'HRD' => 
      array (
        'name' => 'HRD',
        'friendly_name' => 'Hrd',
      ),
    ),
    'positions' => 
    array (
      0 => 
      array (
        'name' => 'power',
        'description' => 'power',
        'level_id' => '1',
        'order_level' => '0',
        'created_at' => 
        DateTime::__set_state(array(
           'date' => '2022-11-03 11:50:27.352186',
           'timezone_type' => 3,
           'timezone' => 'Asia/Jakarta',
        )),
        'updated_at' => 
        DateTime::__set_state(array(
           'date' => '2022-11-03 11:50:27.352189',
           'timezone_type' => 3,
           'timezone' => 'Asia/Jakarta',
        )),
      ),
      1 => 
      array (
        'name' => 'superadmin',
        'description' => 'superadmin',
        'level_id' => '2',
        'order_level' => '1',
        'created_at' => 
        DateTime::__set_state(array(
           'date' => '2022-11-03 11:50:27.352190',
           'timezone_type' => 3,
           'timezone' => 'Asia/Jakarta',
        )),
        'updated_at' => 
        DateTime::__set_state(array(
           'date' => '2022-11-03 11:50:27.352191',
           'timezone_type' => 3,
           'timezone' => 'Asia/Jakarta',
        )),
      ),
      2 => 
      array (
        'name' => 'pengawas_satu',
        'description' => 'pengawas 1',
        'level_id' => '3',
        'order_level' => '2',
        'created_at' => 
        DateTime::__set_state(array(
           'date' => '2022-11-03 11:50:27.352192',
           'timezone_type' => 3,
           'timezone' => 'Asia/Jakarta',
        )),
        'updated_at' => 
        DateTime::__set_state(array(
           'date' => '2022-11-03 11:50:27.352192',
           'timezone_type' => 3,
           'timezone' => 'Asia/Jakarta',
        )),
      ),
      3 => 
      array (
        'name' => 'pengawas_dua',
        'description' => 'pengawas 2',
        'level_id' => '4',
        'order_level' => '3',
        'created_at' => 
        DateTime::__set_state(array(
           'date' => '2022-11-03 11:50:27.352193',
           'timezone_type' => 3,
           'timezone' => 'Asia/Jakarta',
        )),
        'updated_at' => 
        DateTime::__set_state(array(
           'date' => '2022-11-03 11:50:27.352194',
           'timezone_type' => 3,
           'timezone' => 'Asia/Jakarta',
        )),
      ),
      4 => 
      array (
        'name' => 'pengawas_tiga',
        'description' => 'pengawas 3',
        'level_id' => '5',
        'order_level' => '4',
        'created_at' => 
        DateTime::__set_state(array(
           'date' => '2022-11-03 11:50:27.352195',
           'timezone_type' => 3,
           'timezone' => 'Asia/Jakarta',
        )),
        'updated_at' => 
        DateTime::__set_state(array(
           'date' => '2022-11-03 11:50:27.352195',
           'timezone_type' => 3,
           'timezone' => 'Asia/Jakarta',
        )),
      ),
      5 => 
      array (
        'name' => 'pengurus_satu',
        'description' => 'pengurus 1',
        'level_id' => '6',
        'order_level' => '5',
        'created_at' => 
        DateTime::__set_state(array(
           'date' => '2022-11-03 11:50:27.352196',
           'timezone_type' => 3,
           'timezone' => 'Asia/Jakarta',
        )),
        'updated_at' => 
        DateTime::__set_state(array(
           'date' => '2022-11-03 11:50:27.352197',
           'timezone_type' => 3,
           'timezone' => 'Asia/Jakarta',
        )),
      ),
      6 => 
      array (
        'name' => 'pengurus_dua',
        'description' => 'pengurus 2',
        'level_id' => '7',
        'order_level' => '6',
        'created_at' => 
        DateTime::__set_state(array(
           'date' => '2022-11-03 11:50:27.352197',
           'timezone_type' => 3,
           'timezone' => 'Asia/Jakarta',
        )),
        'updated_at' => 
        DateTime::__set_state(array(
           'date' => '2022-11-03 11:50:27.352198',
           'timezone_type' => 3,
           'timezone' => 'Asia/Jakarta',
        )),
      ),
      7 => 
      array (
        'name' => 'supervisor',
        'description' => 'supervisor',
        'level_id' => '8',
        'order_level' => '7',
        'created_at' => 
        DateTime::__set_state(array(
           'date' => '2022-11-03 11:50:27.352199',
           'timezone_type' => 3,
           'timezone' => 'Asia/Jakarta',
        )),
        'updated_at' => 
        DateTime::__set_state(array(
           'date' => '2022-11-03 11:50:27.352200',
           'timezone_type' => 3,
           'timezone' => 'Asia/Jakarta',
        )),
      ),
      8 => 
      array (
        'name' => 'admin_koperasi',
        'description' => 'admin koperasi',
        'level_id' => '9',
        'order_level' => '8',
        'created_at' => 
        DateTime::__set_state(array(
           'date' => '2022-11-03 11:50:27.352201',
           'timezone_type' => 3,
           'timezone' => 'Asia/Jakarta',
        )),
        'updated_at' => 
        DateTime::__set_state(array(
           'date' => '2022-11-03 11:50:27.352201',
           'timezone_type' => 3,
           'timezone' => 'Asia/Jakarta',
        )),
      ),
      9 => 
      array (
        'name' => 'general_manager',
        'description' => 'general manager',
        'level_id' => '10',
        'order_level' => '9',
        'created_at' => 
        DateTime::__set_state(array(
           'date' => '2022-11-03 11:50:27.352202',
           'timezone_type' => 3,
           'timezone' => 'Asia/Jakarta',
        )),
        'updated_at' => 
        DateTime::__set_state(array(
           'date' => '2022-11-03 11:50:27.352203',
           'timezone_type' => 3,
           'timezone' => 'Asia/Jakarta',
        )),
      ),
      10 => 
      array (
        'name' => 'manager',
        'description' => 'manager',
        'level_id' => '11',
        'order_level' => '10',
        'created_at' => 
        DateTime::__set_state(array(
           'date' => '2022-11-03 11:50:27.352204',
           'timezone_type' => 3,
           'timezone' => 'Asia/Jakarta',
        )),
        'updated_at' => 
        DateTime::__set_state(array(
           'date' => '2022-11-03 11:50:27.352204',
           'timezone_type' => 3,
           'timezone' => 'Asia/Jakarta',
        )),
      ),
      11 => 
      array (
        'name' => 'admin_area',
        'description' => 'admin area',
        'level_id' => '12',
        'order_level' => '11',
        'created_at' => 
        DateTime::__set_state(array(
           'date' => '2022-11-03 11:50:27.352205',
           'timezone_type' => 3,
           'timezone' => 'Asia/Jakarta',
        )),
        'updated_at' => 
        DateTime::__set_state(array(
           'date' => '2022-11-03 11:50:27.352206',
           'timezone_type' => 3,
           'timezone' => 'Asia/Jakarta',
        )),
      ),
      12 => 
      array (
        'name' => 'dansek',
        'description' => 'dansek',
        'level_id' => '13',
        'order_level' => '12',
        'created_at' => 
        DateTime::__set_state(array(
           'date' => '2022-11-03 11:50:27.352206',
           'timezone_type' => 3,
           'timezone' => 'Asia/Jakarta',
        )),
        'updated_at' => 
        DateTime::__set_state(array(
           'date' => '2022-11-03 11:50:27.352207',
           'timezone_type' => 3,
           'timezone' => 'Asia/Jakarta',
        )),
      ),
      13 => 
      array (
        'name' => 'anggota',
        'description' => 'anggota security',
        'level_id' => '14',
        'order_level' => '13',
        'created_at' => 
        DateTime::__set_state(array(
           'date' => '2022-11-03 11:50:27.352208',
           'timezone_type' => 3,
           'timezone' => 'Asia/Jakarta',
        )),
        'updated_at' => 
        DateTime::__set_state(array(
           'date' => '2022-11-03 11:50:27.352209',
           'timezone_type' => 3,
           'timezone' => 'Asia/Jakarta',
        )),
      ),
      14 => 
      array (
        'name' => 'direktur_utama',
        'description' => 'direktur utama',
        'level_id' => '15',
        'order_level' => '14',
        'created_at' => 
        DateTime::__set_state(array(
           'date' => '2022-11-03 11:50:27.352210',
           'timezone_type' => 3,
           'timezone' => 'Asia/Jakarta',
        )),
        'updated_at' => 
        DateTime::__set_state(array(
           'date' => '2022-11-03 11:50:27.352210',
           'timezone_type' => 3,
           'timezone' => 'Asia/Jakarta',
        )),
      ),
      15 => 
      array (
        'name' => 'direktur',
        'description' => 'direktur',
        'level_id' => '16',
        'order_level' => '15',
        'created_at' => 
        DateTime::__set_state(array(
           'date' => '2022-11-03 11:50:27.352211',
           'timezone_type' => 3,
           'timezone' => 'Asia/Jakarta',
        )),
        'updated_at' => 
        DateTime::__set_state(array(
           'date' => '2022-11-03 11:50:27.352212',
           'timezone_type' => 3,
           'timezone' => 'Asia/Jakarta',
        )),
      ),
      16 => 
      array (
        'name' => 'pendiri',
        'description' => 'pendiri',
        'level_id' => '17',
        'order_level' => '16',
        'created_at' => 
        DateTime::__set_state(array(
           'date' => '2022-11-03 11:50:27.352213',
           'timezone_type' => 3,
           'timezone' => 'Asia/Jakarta',
        )),
        'updated_at' => 
        DateTime::__set_state(array(
           'date' => '2022-11-03 11:50:27.352213',
           'timezone_type' => 3,
           'timezone' => 'Asia/Jakarta',
        )),
      ),
      17 => 
      array (
        'name' => 'karyawan_pengelola',
        'description' => 'karyawan pengelola',
        'level_id' => '18',
        'order_level' => '17',
        'created_at' => 
        DateTime::__set_state(array(
           'date' => '2022-11-03 11:50:27.352214',
           'timezone_type' => 3,
           'timezone' => 'Asia/Jakarta',
        )),
        'updated_at' => 
        DateTime::__set_state(array(
           'date' => '2022-11-03 11:50:27.352215',
           'timezone_type' => 3,
           'timezone' => 'Asia/Jakarta',
        )),
      ),
      18 => 
      array (
        'name' => 'karyawan_koperasi',
        'description' => 'karyawan koperasi',
        'level_id' => '19',
        'order_level' => '20',
        'created_at' => 
        DateTime::__set_state(array(
           'date' => '2022-11-03 11:50:27.352216',
           'timezone_type' => 3,
           'timezone' => 'Asia/Jakarta',
        )),
        'updated_at' => 
        DateTime::__set_state(array(
           'date' => '2022-11-03 11:50:27.352216',
           'timezone_type' => 3,
           'timezone' => 'Asia/Jakarta',
        )),
      ),
      19 => 
      array (
        'name' => 'pengelola_area',
        'description' => 'pengelola area',
        'level_id' => '20',
        'order_level' => '19',
        'created_at' => 
        DateTime::__set_state(array(
           'date' => '2022-11-03 11:50:27.352217',
           'timezone_type' => 3,
           'timezone' => 'Asia/Jakarta',
        )),
        'updated_at' => 
        DateTime::__set_state(array(
           'date' => '2022-11-03 11:50:27.352218',
           'timezone_type' => 3,
           'timezone' => 'Asia/Jakarta',
        )),
      ),
      20 => 
      array (
        'name' => 'komisaris',
        'description' => 'komisaris',
        'level_id' => '21',
        'order_level' => '20',
        'created_at' => 
        DateTime::__set_state(array(
           'date' => '2022-11-03 11:50:27.352219',
           'timezone_type' => 3,
           'timezone' => 'Asia/Jakarta',
        )),
        'updated_at' => 
        DateTime::__set_state(array(
           'date' => '2022-11-03 11:50:27.352219',
           'timezone_type' => 3,
           'timezone' => 'Asia/Jakarta',
        )),
      ),
      21 => 
      array (
        'name' => 'hrd',
        'description' => 'hrd',
        'level_id' => '22',
        'order_level' => '21',
        'created_at' => 
        DateTime::__set_state(array(
           'date' => '2022-11-03 11:50:27.352220',
           'timezone_type' => 3,
           'timezone' => 'Asia/Jakarta',
        )),
        'updated_at' => 
        DateTime::__set_state(array(
           'date' => '2022-11-03 11:50:27.352221',
           'timezone_type' => 3,
           'timezone' => 'Asia/Jakarta',
        )),
      ),
    ),
    'permissions' => 
    array (
      0 => 'account.register',
      1 => 'account.login',
      2 => 'account.forgot_password',
      3 => 'account.bank_data',
      4 => 'dashboard.loan_total',
      5 => 'dashboard.deposit_total',
      6 => 'dashboard.member_card',
      7 => 'dashboard.news',
      8 => 'deposit.main_deposit',
      9 => 'deposit.required_deposit',
      10 => 'deposit.voluntary_deposit',
      11 => 'deposit.shu',
      12 => 'deposit_transaction.deposit_application',
      13 => 'deposit_transaction.cashing_application',
      14 => 'deposit_transaction.cashing',
      15 => 'deposit_transaction.history',
      16 => 'loan.application',
      17 => 'loan.approval',
      18 => 'loan.detail',
      19 => 'loan.history',
      20 => 'loan.transaction',
      21 => 'shu.config',
      22 => 'shu.history',
      23 => 'member',
      24 => 'member.total_member',
      25 => 'member.configuration',
      26 => 'member.registration',
      27 => 'member.work_configuration',
      28 => 'member.bank',
      29 => 'member.resign',
      30 => 'member.profile',
      31 => 'member.loan',
      32 => 'member.deposit',
      33 => 'member.staffinfo',
      34 => 'member.plafond',
      35 => 'member.qna',
      36 => 'approve.deposit',
      37 => 'approve.loan',
      38 => 'generate.report.deposit',
      39 => 'generate.report.loan',
      40 => 'generate.report.member',
      41 => 'report',
      42 => 'report.deposit',
      43 => 'report.loan',
      44 => 'report.member',
      45 => 'report.npl',
      46 => 'auth.level',
      47 => 'auth.permission',
      48 => 'auth.user',
      49 => 'auth.management',
      50 => 'project.management',
      51 => 'transaction.management',
      52 => 'master.area',
      53 => 'master.branch',
      54 => 'master.transaction-type',
      55 => 'master.location',
      56 => 'master.project',
      57 => 'master.position',
      58 => 'master.setting',
      59 => 'master.policy',
      60 => 'report.per_member',
      61 => 'report.per_area',
      62 => 'report.annual_loan',
      63 => 'report.annual_deposit',
      64 => 'report.annual_shu',
      65 => 'master.shu',
      66 => 'master.deposit',
      67 => 'master.loan',
      68 => 'transaction.member.plafond',
      69 => 'transaction.member.loan',
      70 => 'transaction.member.deposit',
      71 => 'transaction.member.retrieve.deposit',
      72 => 'transaction.member.change.deposit',
      73 => 'transaction.deposit',
      74 => 'management.import',
      75 => 'account.configuration',
    ),
    'privileges' => 
    array (
      'SUPERADMIN' => 
      array (
        0 => 'view.transaction.management',
        1 => 'view.account.register',
        2 => 'create.account.register',
        3 => 'update.account.register',
        4 => 'delete.account.register',
        5 => 'view.account.login',
        6 => 'create.account.login',
        7 => 'update.account.login',
        8 => 'delete.account.login',
        9 => 'view.account.forgot_password',
        10 => 'create.account.forgot_password',
        11 => 'update.account.forgot_password',
        12 => 'delete.account.forgot_password',
        13 => 'view.account.bank_data',
        14 => 'create.account.bank_data',
        15 => 'update.account.bank_data',
        16 => 'delete.account.bank_data',
        17 => 'view.dashboard.loan_total',
        18 => 'update.dashboard.loan_total',
        19 => 'view.dashboard.deposit_total',
        20 => 'update.dashboard.deposit_total',
        21 => 'view.dashboard.member_card',
        22 => 'update.dashboard.member_card',
        23 => 'view.dashboard.news',
        24 => 'update.dashboard.news',
        25 => 'view.deposit.main_deposit',
        26 => 'update.deposit.main_deposit',
        27 => 'view.deposit.required_deposit',
        28 => 'update.deposit.required_deposit',
        29 => 'view.deposit.voluntary_deposit',
        30 => 'update.deposit.voluntary_deposit',
        31 => 'view.deposit.shu',
        32 => 'update.deposit.shu',
        33 => 'view.deposit_transaction.deposit_application',
        34 => 'update.deposit_transaction.deposit_application',
        35 => 'view.deposit_transaction.cashing_application',
        36 => 'update.deposit_transaction.cashing_application',
        37 => 'view.deposit_transaction.cashing',
        38 => 'update.deposit_transaction.cashing',
        39 => 'view.deposit_transaction.history',
        40 => 'update.deposit_transaction.history',
        41 => 'view.loan.approval',
        42 => 'update.loan.approval',
        43 => 'view.loan.detail',
        44 => 'update.loan.detail',
        45 => 'view.shu.config',
        46 => 'update.shu.config',
        47 => 'view.shu.history',
        48 => 'update.shu.history',
        49 => 'view.report.per_member',
        50 => 'create.report.per_member',
        51 => 'update.report.per_member',
        52 => 'view.report.per_area',
        53 => 'create.report.per_area',
        54 => 'update.report.per_area',
        55 => 'view.report.annual_loan',
        56 => 'create.report.annual_loan',
        57 => 'update.report.annual_loan',
        58 => 'view.report.annual_deposit',
        59 => 'create.report.annual_deposit',
        60 => 'update.report.annual_deposit',
        61 => 'view.report.annual_shu',
        62 => 'create.report.annual_shu',
        63 => 'update.report.annual_shu',
        64 => 'view.loan.transaction',
        65 => 'view.auth.management',
        66 => 'view.auth.user',
        67 => 'create.auth.user',
        68 => 'update.auth.user',
        69 => 'delete.auth.user',
        70 => 'view.member.profile',
        71 => 'create.member.profile',
        72 => 'update.member.profile',
        73 => 'delete.member.profile',
        74 => 'view.approve.deposit',
        75 => 'create.approve.deposit',
        76 => 'update.approve.deposit',
        77 => 'delete.approve.deposit',
        78 => 'view.approve.loan',
        79 => 'create.approve.loan',
        80 => 'update.approve.loan',
        81 => 'delete.approve.loan',
        82 => 'view.member.loan',
        83 => 'create.member.loan',
        84 => 'update.member.loan',
        85 => 'delete.member.loan',
        86 => 'view.member.deposit',
        87 => 'create.member.deposit',
        88 => 'update.member.deposit',
        89 => 'delete.member.deposit',
        90 => 'view.member.profile',
        91 => 'create.member.profile',
        92 => 'update.member.profile',
        93 => 'delete.member.profile',
        94 => 'view.report',
        95 => 'create.report',
        96 => 'update.report',
        97 => 'delete.report',
        98 => 'view.report.deposit',
        99 => 'create.report.deposit',
        100 => 'update.report.deposit',
        101 => 'delete.report.deposit',
        102 => 'view.report.loan',
        103 => 'create.report.loan',
        104 => 'update.report.loan',
        105 => 'delete.report.loan',
        106 => 'view.project.management',
        107 => 'create.project.management',
        108 => 'update.project.management',
        109 => 'delete.project.management',
        110 => 'view.member',
        111 => 'create.member',
        112 => 'update.member',
        113 => 'delete.member',
        114 => 'view.member.staffinfo',
        115 => 'create.member.staffinfo',
        116 => 'update.member.staffinfo',
        117 => 'delete.member.staffinfo',
        118 => 'view.master.project',
        119 => 'create.master.project',
        120 => 'update.master.project',
        121 => 'delete.master.project',
        122 => 'view.master.area',
        123 => 'create.master.area',
        124 => 'update.master.area',
        125 => 'delete.master.area',
        126 => 'view.master.branch',
        127 => 'create.master.branch',
        128 => 'update.master.branch',
        129 => 'delete.master.branch',
        130 => 'view.master.location',
        131 => 'create.master.location',
        132 => 'update.master.location',
        133 => 'delete.master.location',
        134 => 'view.master.position',
        135 => 'create.master.position',
        136 => 'update.master.position',
        137 => 'delete.master.position',
        138 => 'view.master.policy',
        139 => 'create.master.policy',
        140 => 'update.master.policy',
        141 => 'delete.master.policy',
        142 => 'view.management.import',
        143 => 'create.management.import',
        144 => 'update.management.import',
        145 => 'delete.management.import',
        146 => 'view.member.resign',
        147 => 'create.member.resign',
        148 => 'update.member.resign',
        149 => 'delete.member.resign',
        150 => 'view.member.plafond',
        151 => 'create.member.plafond',
        152 => 'update.member.plafond',
        153 => 'delete.member.plafond',
        154 => 'view.auth.level',
        155 => 'create.auth.level',
        156 => 'update.auth.level',
        157 => 'delete.auth.level',
        158 => 'view.master.deposit',
        159 => 'create.master.deposit',
        160 => 'update.master.deposit',
        161 => 'delete.master.deposit',
        162 => 'view.master.loan',
        163 => 'create.master.loan',
        164 => 'update.master.loan',
        165 => 'delete.master.loan',
        166 => 'view.transaction.member.plafond',
        167 => 'create.transaction.member.plafond',
        168 => 'update.transaction.member.plafond',
        169 => 'delete.transaction.member.plafond',
        170 => 'view.transaction.member.loan',
        171 => 'create.transaction.member.loan',
        172 => 'update.transaction.member.loan',
        173 => 'delete.transaction.member.loan',
        174 => 'view.transaction.member.deposit',
        175 => 'create.transaction.member.deposit',
        176 => 'update.transaction.member.deposit',
        177 => 'delete.transaction.member.deposit',
        178 => 'view.transaction.member.retrieve.deposit',
        179 => 'create.transaction.member.retrieve.deposit',
        180 => 'update.transaction.member.retrieve.deposit',
        181 => 'delete.transaction.member.retrieve.deposit',
        182 => 'view.transaction.member.change.deposit',
        183 => 'create.transaction.member.change.deposit',
        184 => 'update.transaction.member.change.deposit',
        185 => 'delete.transaction.member.change.deposit',
        186 => 'view.generate.report.deposit',
        187 => 'create.generate.report.deposit',
        188 => 'update.generate.report.deposit',
        189 => 'delete.generate.report.deposit',
        190 => 'view.generate.report.loan',
        191 => 'create.generate.report.loan',
        192 => 'update.generate.report.loan',
        193 => 'delete.generate.report.loan',
        194 => 'view.generate.report.member',
        195 => 'create.generate.report.member',
        196 => 'update.generate.report.member',
        197 => 'delete.generate.report.member',
        198 => 'view.account.configuration',
        199 => 'create.account.configuration',
        200 => 'update.account.configuration',
        201 => 'delete.account.configuration',
        202 => 'view.master.qna',
        203 => 'create.master.qna',
        204 => 'update.master.qna',
        205 => 'delete.master.qna',
      ),
      'ADMIN_KOPERASI' => 
      array (
        0 => 'view.transaction.management',
        1 => 'view.account.register',
        2 => 'view.account.login',
        3 => 'view.account.forgot_password',
        4 => 'view.account.bank_data',
        5 => 'view.dashboard.loan_total',
        6 => 'create.dashboard.loan_total',
        7 => 'view.dashboard.deposit_total',
        8 => 'create.dashboard.deposit_total',
        9 => 'view.dashboard.member_card',
        10 => 'create.dashboard.member_card',
        11 => 'view.dashboard.news',
        12 => 'create.dashboard.news',
        13 => 'view.deposit.main_deposit',
        14 => 'update.deposit.main_deposit',
        15 => 'view.deposit.required_deposit',
        16 => 'update.deposit.required_deposit',
        17 => 'view.deposit.voluntary_deposit',
        18 => 'update.deposit.voluntary_deposit',
        19 => 'view.deposit.shu',
        20 => 'update.deposit.shu',
        21 => 'view.deposit_transaction.deposit_application',
        22 => 'create.deposit_transaction.deposit_application',
        23 => 'update.deposit_transaction.deposit_application',
        24 => 'view.deposit_transaction.cashing_application',
        25 => 'create.deposit_transaction.cashing_application',
        26 => 'update.deposit_transaction.cashing_application',
        27 => 'view.deposit_transaction.cashing',
        28 => 'create.deposit_transaction.cashing',
        29 => 'update.deposit_transaction.cashing',
        30 => 'view.deposit_transaction.history',
        31 => 'create.deposit_transaction.history',
        32 => 'update.deposit_transaction.history',
        33 => 'view.loan.application',
        34 => 'create.loan.application',
        35 => 'update.loan.application',
        36 => 'view.loan.approval',
        37 => 'create.loan.approval',
        38 => 'update.loan.approval',
        39 => 'view.loan.detail',
        40 => 'create.loan.detail',
        41 => 'update.loan.detail',
        42 => 'view.loan.history',
        43 => 'create.loan.history',
        44 => 'update.loan.history',
        45 => 'view.shu.config',
        46 => 'create.shu.config',
        47 => 'update.shu.config',
        48 => 'delete.shu.config',
        49 => 'view.shu.history',
        50 => 'create.shu.history',
        51 => 'update.shu.history',
        52 => 'delete.shu.history',
        53 => 'view.member.total_member',
        54 => 'view.member.configuration',
        55 => 'create.member.configuration',
        56 => 'update.member.configuration',
        57 => 'view.member.registration',
        58 => 'create.member.registration',
        59 => 'update.member.registration',
        60 => 'delete.member.registration',
        61 => 'view.member.bank',
        62 => 'create.member.bank',
        63 => 'update.member.bank',
        64 => 'view.member.resign',
        65 => 'create.member.resign',
        66 => 'update.member.resign',
        67 => 'view.master.area',
        68 => 'create.master.area',
        69 => 'update.master.area',
        70 => 'delete.master.area',
        71 => 'view.master.transaction-type',
        72 => 'create.master.transaction-type',
        73 => 'update.master.transaction-type',
        74 => 'delete.master.transaction-type',
        75 => 'view.master.location',
        76 => 'create.master.location',
        77 => 'update.master.location',
        78 => 'delete.master.location',
        79 => 'view.master.project',
        80 => 'create.master.project',
        81 => 'update.master.project',
        82 => 'delete.master.project',
        83 => 'view.master.position',
        84 => 'create.master.position',
        85 => 'update.master.position',
        86 => 'delete.master.position',
        87 => 'view.report.per_member',
        88 => 'view.report.per_area',
        89 => 'view.report.annual_shu',
        90 => 'create.report.annual_shu',
        91 => 'update.report.annual_shu',
        92 => 'delete.report.annual_shu',
        93 => 'view.loan.transaction',
        94 => 'view.auth.user',
        95 => 'create.auth.user',
        96 => 'update.auth.user',
        97 => 'delete.auth.user',
        98 => 'view.project.management',
        99 => 'view.master.branch',
        100 => 'create.master.branch',
        101 => 'update.master.branch',
        102 => 'delete.master.branch',
        103 => 'view.master.policy',
        104 => 'create.master.policy',
        105 => 'update.master.policy',
        106 => 'delete.master.policy',
        107 => 'view.member.plafond',
        108 => 'create.member.plafond',
        109 => 'update.member.plafond',
        110 => 'delete.member.plafond',
        111 => 'view.auth.level',
        112 => 'create.auth.level',
        113 => 'update.auth.level',
        114 => 'delete.auth.level',
        115 => 'view.master.deposit',
        116 => 'create.master.deposit',
        117 => 'update.master.deposit',
        118 => 'delete.master.deposit',
        119 => 'view.master.loan',
        120 => 'create.master.loan',
        121 => 'update.master.loan',
        122 => 'delete.master.loan',
        123 => 'view.transaction.member.plafond',
        124 => 'create.transaction.member.plafond',
        125 => 'update.transaction.member.plafond',
        126 => 'delete.transaction.member.plafond',
        127 => 'view.transaction.member.loan',
        128 => 'create.transaction.member.loan',
        129 => 'update.transaction.member.loan',
        130 => 'delete.transaction.member.loan',
        131 => 'view.transaction.member.deposit',
        132 => 'create.transaction.member.deposit',
        133 => 'update.transaction.member.deposit',
        134 => 'delete.transaction.member.deposit',
        135 => 'view.transaction.member.retrieve.deposit',
        136 => 'create.transaction.member.retrieve.deposit',
        137 => 'update.transaction.member.retrieve.deposit',
        138 => 'delete.transaction.member.retrieve.deposit',
        139 => 'view.transaction.member.change.deposit',
        140 => 'create.transaction.member.change.deposit',
        141 => 'update.transaction.member.change.deposit',
        142 => 'delete.transaction.member.change.deposit',
        143 => 'view.generate.report.deposit',
        144 => 'create.generate.report.deposit',
        145 => 'update.generate.report.deposit',
        146 => 'delete.generate.report.deposit',
        147 => 'view.generate.report.loan',
        148 => 'create.generate.report.loan',
        149 => 'update.generate.report.loan',
        150 => 'delete.generate.report.loan',
        151 => 'view.generate.report.member',
        152 => 'create.generate.report.member',
        153 => 'update.generate.report.member',
        154 => 'delete.generate.report.member',
        155 => 'view.account.configuration',
        156 => 'create.account.configuration',
        157 => 'update.account.configuration',
        158 => 'delete.account.configuration',
        159 => 'view.master.qna',
        160 => 'create.master.qna',
        161 => 'update.master.qna',
        162 => 'delete.master.qna',
      ),
      'MEMBER' => 
      array (
        0 => 'view.account.register',
        1 => 'create.account.register',
        2 => 'view.account.login',
        3 => 'create.account.login',
        4 => 'view.account.forgot_password',
        5 => 'create.account.forgot_password',
        6 => 'create.account.bank_data',
        7 => 'view.dashboard.loan_total',
        8 => 'view.dashboard.deposit_total',
        9 => 'view.dashboard.member_card',
        10 => 'view.dashboard.news',
        11 => 'view.deposit.main_deposit',
        12 => 'view.deposit.required_deposit',
        13 => 'view.deposit.voluntary_deposit',
        14 => 'view.deposit.shu',
        15 => 'create.deposit_transaction.deposit_application',
        16 => 'view.deposit_transaction.cashing_application',
        17 => 'create.deposit_transaction.cashing_application',
        18 => 'view.deposit_transaction.cashing',
        19 => 'view.deposit_transaction.history',
        20 => 'view.loan.application',
        21 => 'create.loan.application',
        22 => 'update.loan.application',
        23 => 'delete.loan.application',
        24 => 'view.loan.history',
        25 => 'create.member.resign',
        26 => 'view.account.configuration',
        27 => 'create.account.configuration',
        28 => 'update.account.configuration',
        29 => 'delete.account.configuration',
        30 => 'view.master.qna',
        31 => 'create.master.qna',
        32 => 'update.master.qna',
        33 => 'delete.master.qna',
      ),
      'DANSEK' => 
      array (
        0 => 'create.account.register',
        1 => 'create.account.login',
        2 => 'create.account.forgot_password',
        3 => 'view.dashboard.loan_total',
        4 => 'view.dashboard.deposit_total',
        5 => 'view.dashboard.member_card',
        6 => 'view.dashboard.news',
        7 => 'view.deposit.main_deposit',
        8 => 'view.deposit.required_deposit',
        9 => 'view.deposit.voluntary_deposit',
        10 => 'view.deposit.shu',
        11 => 'create.deposit_transaction.deposit_application',
        12 => 'create.deposit_transaction.cashing_application',
        13 => 'view.deposit_transaction.cashing',
        14 => 'view.deposit_transaction.history',
        15 => 'update.loan.approval',
        16 => 'view.loan.detail',
        17 => 'update.loan.history',
        18 => 'view.member.resign',
        19 => 'create.member.resign',
        20 => 'view.account.configuration',
        21 => 'create.account.configuration',
        22 => 'update.account.configuration',
        23 => 'delete.account.configuration',
        24 => 'view.master.qna',
        25 => 'create.master.qna',
        26 => 'update.master.qna',
        27 => 'delete.master.qna',
      ),
      'ADMIN_AREA' => 
      array (
        0 => 'view.transaction.management',
        1 => 'create.account.register',
        2 => 'create.account.login',
        3 => 'create.account.forgot_password',
        4 => 'create.account.bank_data',
        5 => 'view.dashboard.news',
        6 => 'view.deposit.main_deposit',
        7 => 'view.deposit.required_deposit',
        8 => 'view.deposit.voluntary_deposit',
        9 => 'view.deposit.shu',
        10 => 'view.deposit_transaction.deposit_application',
        11 => 'create.deposit_transaction.deposit_application',
        12 => 'view.deposit_transaction.cashing_application',
        13 => 'create.deposit_transaction.cashing_application',
        14 => 'view.deposit_transaction.cashing',
        15 => 'create.deposit_transaction.cashing',
        16 => 'view.deposit_transaction.history',
        17 => 'create.deposit_transaction.history',
        18 => 'view.loan.approval',
        19 => 'update.loan.approval',
        20 => 'view.loan.history',
        21 => 'view.member.total_member',
        22 => 'create.member.registration',
        23 => 'view.member.bank',
        24 => 'create.member.bank',
        25 => 'view.member.resign',
        26 => 'create.member.resign',
        27 => 'update.member.resign',
        28 => 'view.member.plafond',
        29 => 'view.member.loan',
        30 => 'update.member.loan',
        31 => 'view.transaction.member.retrieve.deposit',
        32 => 'update.transaction.member.retrieve.deposit',
        33 => 'view.transaction.member.change.deposit',
        34 => 'update.transaction.member.change.deposit',
        35 => 'view.member.resign',
        36 => 'update.member.resign',
        37 => 'view.account.configuration',
        38 => 'create.account.configuration',
        39 => 'update.account.configuration',
        40 => 'delete.account.configuration',
        41 => 'view.master.qna',
        42 => 'create.master.qna',
        43 => 'update.master.qna',
        44 => 'delete.master.qna',
      ),
      'HO.AREA' => 
      array (
        0 => 'view.deposit_transaction.deposit_application',
        1 => 'update.deposit_transaction.deposit_application',
        2 => 'view.deposit_transaction.cashing_application',
        3 => 'update.deposit_transaction.cashing_application',
        4 => 'view.deposit_transaction.cashing',
        5 => 'update.deposit_transaction.cashing',
        6 => 'view.deposit_transaction.history',
        7 => 'update.deposit_transaction.history',
        8 => 'view.loan.application',
        9 => 'update.loan.application',
        10 => 'view.loan.approval',
        11 => 'update.loan.approval',
        12 => 'view.loan.detail',
        13 => 'update.loan.detail',
        14 => 'view.loan.history',
        15 => 'update.loan.history',
        16 => 'view.member.bank',
        17 => 'view.member.resign',
        18 => 'create.member.resign',
        19 => 'update.member.resign',
        20 => 'view.loan.transaction',
        21 => 'view.account.configuration',
        22 => 'create.account.configuration',
        23 => 'update.account.configuration',
        24 => 'delete.account.configuration',
        25 => 'view.master.qna',
        26 => 'create.master.qna',
        27 => 'update.master.qna',
        28 => 'delete.master.qna',
      ),
      'SUPERVISOR' => 
      array (
        0 => 'view.dashboard.loan_total',
        1 => 'view.dashboard.deposit_total',
        2 => 'view.dashboard.member_card',
        3 => 'view.dashboard.news',
        4 => 'view.deposit.main_deposit',
        5 => 'view.deposit.required_deposit',
        6 => 'view.deposit.voluntary_deposit',
        7 => 'view.deposit.shu',
        8 => 'view.deposit_transaction.deposit_application',
        9 => 'update.deposit_transaction.deposit_application',
        10 => 'view.deposit_transaction.cashing_application',
        11 => 'update.deposit_transaction.cashing_application',
        12 => 'view.deposit_transaction.cashing',
        13 => 'update.deposit_transaction.cashing',
        14 => 'view.deposit_transaction.history',
        15 => 'update.deposit_transaction.history',
        16 => 'view.loan.application',
        17 => 'update.loan.application',
        18 => 'view.loan.approval',
        19 => 'update.loan.approval',
        20 => 'view.shu.config',
        21 => 'update.shu.config',
        22 => 'view.shu.history',
        23 => 'update.shu.history',
        24 => 'view.account.configuration',
        25 => 'create.account.configuration',
        26 => 'update.account.configuration',
        27 => 'delete.account.configuration',
        28 => 'view.master.qna',
        29 => 'create.master.qna',
        30 => 'update.master.qna',
        31 => 'delete.master.qna',
      ),
      'PENGURUS_1' => 
      array (
        0 => 'view.dashboard.loan_total',
        1 => 'view.dashboard.deposit_total',
        2 => 'view.dashboard.member_card',
        3 => 'view.dashboard.news',
        4 => 'view.deposit.main_deposit',
        5 => 'view.deposit.required_deposit',
        6 => 'view.deposit.voluntary_deposit',
        7 => 'view.deposit.shu',
        8 => 'view.deposit_transaction.deposit_application',
        9 => 'update.deposit_transaction.deposit_application',
        10 => 'view.deposit_transaction.cashing_application',
        11 => 'update.deposit_transaction.cashing_application',
        12 => 'view.deposit_transaction.cashing',
        13 => 'update.deposit_transaction.cashing',
        14 => 'view.deposit_transaction.history',
        15 => 'update.deposit_transaction.history',
        16 => 'view.loan.application',
        17 => 'update.loan.application',
        18 => 'view.loan.approval',
        19 => 'update.loan.approval',
        20 => 'view.shu.config',
        21 => 'update.shu.config',
        22 => 'view.shu.history',
        23 => 'update.shu.history',
        24 => 'view.account.configuration',
        25 => 'create.account.configuration',
        26 => 'update.account.configuration',
        27 => 'delete.account.configuration',
        28 => 'view.master.qna',
        29 => 'create.master.qna',
        30 => 'update.master.qna',
        31 => 'delete.master.qna',
      ),
      'PENGURUS_2' => 
      array (
        0 => 'view.dashboard.loan_total',
        1 => 'view.dashboard.deposit_total',
        2 => 'view.dashboard.member_card',
        3 => 'view.dashboard.news',
        4 => 'view.deposit.main_deposit',
        5 => 'view.deposit.required_deposit',
        6 => 'view.deposit.voluntary_deposit',
        7 => 'view.deposit.shu',
        8 => 'view.deposit_transaction.deposit_application',
        9 => 'update.deposit_transaction.deposit_application',
        10 => 'view.deposit_transaction.cashing_application',
        11 => 'update.deposit_transaction.cashing_application',
        12 => 'view.deposit_transaction.cashing',
        13 => 'update.deposit_transaction.cashing',
        14 => 'view.deposit_transaction.history',
        15 => 'update.deposit_transaction.history',
        16 => 'view.loan.application',
        17 => 'update.loan.application',
        18 => 'view.loan.approval',
        19 => 'update.loan.approval',
        20 => 'view.shu.config',
        21 => 'update.shu.config',
        22 => 'view.shu.history',
        23 => 'update.shu.history',
        24 => 'view.account.configuration',
        25 => 'create.account.configuration',
        26 => 'update.account.configuration',
        27 => 'delete.account.configuration',
        28 => 'view.master.qna',
        29 => 'create.master.qna',
        30 => 'update.master.qna',
        31 => 'delete.master.qna',
      ),
      'PENGAWAS_1' => 
      array (
        0 => 'view.dashboard.loan_total',
        1 => 'view.dashboard.deposit_total',
        2 => 'view.dashboard.member_card',
        3 => 'view.dashboard.news',
        4 => 'view.deposit.main_deposit',
        5 => 'view.deposit.required_deposit',
        6 => 'view.deposit.voluntary_deposit',
        7 => 'view.deposit.shu',
        8 => 'view.deposit_transaction.deposit_application',
        9 => 'update.deposit_transaction.deposit_application',
        10 => 'view.deposit_transaction.cashing_application',
        11 => 'update.deposit_transaction.cashing_application',
        12 => 'view.deposit_transaction.cashing',
        13 => 'update.deposit_transaction.cashing',
        14 => 'view.deposit_transaction.history',
        15 => 'update.deposit_transaction.history',
        16 => 'view.loan.application',
        17 => 'update.loan.application',
        18 => 'view.loan.approval',
        19 => 'update.loan.approval',
        20 => 'view.shu.config',
        21 => 'update.shu.config',
        22 => 'view.shu.history',
        23 => 'update.shu.history',
        24 => 'view.account.configuration',
        25 => 'create.account.configuration',
        26 => 'update.account.configuration',
        27 => 'delete.account.configuration',
        28 => 'view.master.qna',
        29 => 'create.master.qna',
        30 => 'update.master.qna',
        31 => 'delete.master.qna',
      ),
      'PENGAWAS_2' => 
      array (
        0 => 'view.dashboard.loan_total',
        1 => 'view.dashboard.deposit_total',
        2 => 'view.dashboard.member_card',
        3 => 'view.dashboard.news',
        4 => 'view.deposit.main_deposit',
        5 => 'view.deposit.required_deposit',
        6 => 'view.deposit.voluntary_deposit',
        7 => 'view.deposit.shu',
        8 => 'view.deposit_transaction.deposit_application',
        9 => 'update.deposit_transaction.deposit_application',
        10 => 'view.deposit_transaction.cashing_application',
        11 => 'update.deposit_transaction.cashing_application',
        12 => 'view.deposit_transaction.cashing',
        13 => 'update.deposit_transaction.cashing',
        14 => 'view.deposit_transaction.history',
        15 => 'update.deposit_transaction.history',
        16 => 'view.loan.application',
        17 => 'update.loan.application',
        18 => 'view.loan.approval',
        19 => 'update.loan.approval',
        20 => 'view.shu.config',
        21 => 'update.shu.config',
        22 => 'view.shu.history',
        23 => 'update.shu.history',
        24 => 'view.account.configuration',
        25 => 'create.account.configuration',
        26 => 'update.account.configuration',
        27 => 'delete.account.configuration',
        28 => 'view.master.qna',
        29 => 'create.master.qna',
        30 => 'update.master.qna',
        31 => 'delete.master.qna',
      ),
      'PENGAWAS_3' => 
      array (
        0 => 'create.account.register',
        1 => 'create.account.login',
        2 => 'create.account.forgot_password',
        3 => 'create.account.bank_data',
        4 => 'view.dashboard.loan_total',
        5 => 'create.dashboard.loan_total',
        6 => 'view.dashboard.deposit_total',
        7 => 'create.dashboard.deposit_total',
        8 => 'view.dashboard.member_card',
        9 => 'create.dashboard.member_card',
        10 => 'view.dashboard.news',
        11 => 'create.dashboard.news',
        12 => 'view.deposit.main_deposit',
        13 => 'update.deposit.main_deposit',
        14 => 'view.deposit.required_deposit',
        15 => 'update.deposit.required_deposit',
        16 => 'view.deposit.voluntary_deposit',
        17 => 'update.deposit.voluntary_deposit',
        18 => 'view.deposit.shu',
        19 => 'update.deposit.shu',
        20 => 'view.deposit_transaction.deposit_application',
        21 => 'create.deposit_transaction.deposit_application',
        22 => 'update.deposit_transaction.deposit_application',
        23 => 'view.deposit_transaction.cashing_application',
        24 => 'create.deposit_transaction.cashing_application',
        25 => 'update.deposit_transaction.cashing_application',
        26 => 'view.deposit_transaction.cashing',
        27 => 'create.deposit_transaction.cashing',
        28 => 'update.deposit_transaction.cashing',
        29 => 'view.deposit_transaction.history',
        30 => 'create.deposit_transaction.history',
        31 => 'update.deposit_transaction.history',
        32 => 'view.loan.application',
        33 => 'create.loan.application',
        34 => 'update.loan.application',
        35 => 'view.loan.approval',
        36 => 'create.loan.approval',
        37 => 'update.loan.approval',
        38 => 'view.loan.detail',
        39 => 'create.loan.detail',
        40 => 'update.loan.detail',
        41 => 'view.loan.history',
        42 => 'create.loan.history',
        43 => 'update.loan.history',
        44 => 'view.shu.config',
        45 => 'create.shu.config',
        46 => 'update.shu.config',
        47 => 'view.shu.history',
        48 => 'create.shu.history',
        49 => 'update.shu.history',
        50 => 'view.member.total_member',
        51 => 'view.member.configuration',
        52 => 'create.member.configuration',
        53 => 'update.member.configuration',
        54 => 'view.member.registration',
        55 => 'create.member.registration',
        56 => 'update.member.registration',
        57 => 'delete.member.registration',
        58 => 'view.member.work_configuration',
        59 => 'create.member.work_configuration',
        60 => 'update.member.work_configuration',
        61 => 'view.member.bank',
        62 => 'create.member.bank',
        63 => 'update.member.bank',
        64 => 'view.member.resign',
        65 => 'create.member.resign',
        66 => 'update.member.resign',
        67 => 'view.master.area',
        68 => 'create.master.area',
        69 => 'update.master.area',
        70 => 'delete.master.area',
        71 => 'view.master.location',
        72 => 'create.master.location',
        73 => 'update.master.location',
        74 => 'delete.master.location',
        75 => 'view.master.project',
        76 => 'create.master.project',
        77 => 'update.master.project',
        78 => 'delete.master.project',
        79 => 'view.master.position',
        80 => 'create.master.position',
        81 => 'update.master.position',
        82 => 'delete.master.position',
        83 => 'view.report.per_member',
        84 => 'create.report.per_member',
        85 => 'update.report.per_member',
        86 => 'view.report.per_area',
        87 => 'create.report.per_area',
        88 => 'update.report.per_area',
        89 => 'view.report.annual_loan',
        90 => 'view.report.annual_deposit',
        91 => 'view.report.annual_shu',
        92 => 'create.report.annual_shu',
        93 => 'update.report.annual_shu',
        94 => 'view.loan.transaction',
        95 => 'view.account.configuration',
        96 => 'create.account.configuration',
        97 => 'update.account.configuration',
        98 => 'delete.account.configuration',
        99 => 'view.master.qna',
        100 => 'create.master.qna',
        101 => 'update.master.qna',
        102 => 'delete.master.qna',
      ),
      'GENERAL_MANAGER' => 
      array (
        0 => 'view.dashboard.loan_total',
        1 => 'view.dashboard.deposit_total',
        2 => 'view.dashboard.member_card',
        3 => 'view.dashboard.news',
        4 => 'view.deposit.main_deposit',
        5 => 'view.deposit.required_deposit',
        6 => 'view.deposit.voluntary_deposit',
        7 => 'view.deposit.shu',
        8 => 'view.deposit_transaction.deposit_application',
        9 => 'update.deposit_transaction.deposit_application',
        10 => 'view.deposit_transaction.cashing_application',
        11 => 'update.deposit_transaction.cashing_application',
        12 => 'view.deposit_transaction.cashing',
        13 => 'update.deposit_transaction.cashing',
        14 => 'view.deposit_transaction.history',
        15 => 'update.deposit_transaction.history',
        16 => 'view.loan.application',
        17 => 'update.loan.application',
        18 => 'view.loan.approval',
        19 => 'update.loan.approval',
        20 => 'view.shu.config',
        21 => 'update.shu.config',
        22 => 'view.shu.history',
        23 => 'update.shu.history',
        24 => 'view.account.configuration',
        25 => 'create.account.configuration',
        26 => 'update.account.configuration',
        27 => 'delete.account.configuration',
        28 => 'view.master.qna',
        29 => 'create.master.qna',
        30 => 'update.master.qna',
        31 => 'delete.master.qna',
      ),
      'MANAGER' => 
      array (
        0 => 'view.dashboard.loan_total',
        1 => 'view.dashboard.deposit_total',
        2 => 'view.dashboard.member_card',
        3 => 'view.dashboard.news',
        4 => 'view.deposit.main_deposit',
        5 => 'view.deposit.required_deposit',
        6 => 'view.deposit.voluntary_deposit',
        7 => 'view.deposit.shu',
        8 => 'view.deposit_transaction.deposit_application',
        9 => 'update.deposit_transaction.deposit_application',
        10 => 'view.deposit_transaction.cashing_application',
        11 => 'update.deposit_transaction.cashing_application',
        12 => 'view.deposit_transaction.cashing',
        13 => 'update.deposit_transaction.cashing',
        14 => 'view.deposit_transaction.history',
        15 => 'update.deposit_transaction.history',
        16 => 'view.loan.application',
        17 => 'update.loan.application',
        18 => 'view.loan.approval',
        19 => 'update.loan.approval',
        20 => 'view.shu.config',
        21 => 'update.shu.config',
        22 => 'view.shu.history',
        23 => 'update.shu.history',
        24 => 'view.account.configuration',
        25 => 'create.account.configuration',
        26 => 'update.account.configuration',
        27 => 'delete.account.configuration',
        28 => 'view.master.qna',
        29 => 'create.master.qna',
        30 => 'update.master.qna',
        31 => 'delete.master.qna',
      ),
      'DIREKTUR_UTAMA' => 
      array (
        0 => 'view.transaction.management',
        1 => 'view.account.register',
        2 => 'view.account.login',
        3 => 'view.account.forgot_password',
        4 => 'view.account.bank_data',
        5 => 'view.dashboard.loan_total',
        6 => 'create.dashboard.loan_total',
        7 => 'view.dashboard.deposit_total',
        8 => 'create.dashboard.deposit_total',
        9 => 'view.dashboard.member_card',
        10 => 'create.dashboard.member_card',
        11 => 'view.dashboard.news',
        12 => 'create.dashboard.news',
        13 => 'view.deposit.main_deposit',
        14 => 'update.deposit.main_deposit',
        15 => 'view.deposit.required_deposit',
        16 => 'update.deposit.required_deposit',
        17 => 'view.deposit.voluntary_deposit',
        18 => 'update.deposit.voluntary_deposit',
        19 => 'view.deposit.shu',
        20 => 'update.deposit.shu',
        21 => 'view.deposit_transaction.deposit_application',
        22 => 'create.deposit_transaction.deposit_application',
        23 => 'update.deposit_transaction.deposit_application',
        24 => 'view.deposit_transaction.cashing_application',
        25 => 'create.deposit_transaction.cashing_application',
        26 => 'update.deposit_transaction.cashing_application',
        27 => 'view.deposit_transaction.cashing',
        28 => 'create.deposit_transaction.cashing',
        29 => 'update.deposit_transaction.cashing',
        30 => 'view.deposit_transaction.history',
        31 => 'create.deposit_transaction.history',
        32 => 'update.deposit_transaction.history',
        33 => 'view.loan.application',
        34 => 'create.loan.application',
        35 => 'update.loan.application',
        36 => 'view.loan.approval',
        37 => 'create.loan.approval',
        38 => 'update.loan.approval',
        39 => 'view.loan.detail',
        40 => 'create.loan.detail',
        41 => 'update.loan.detail',
        42 => 'view.loan.history',
        43 => 'create.loan.history',
        44 => 'update.loan.history',
        45 => 'view.shu.config',
        46 => 'create.shu.config',
        47 => 'update.shu.config',
        48 => 'delete.shu.config',
        49 => 'view.shu.history',
        50 => 'create.shu.history',
        51 => 'update.shu.history',
        52 => 'delete.shu.history',
        53 => 'view.member.total_member',
        54 => 'view.member.configuration',
        55 => 'create.member.configuration',
        56 => 'update.member.configuration',
        57 => 'view.member.registration',
        58 => 'create.member.registration',
        59 => 'update.member.registration',
        60 => 'delete.member.registration',
        61 => 'view.member.bank',
        62 => 'create.member.bank',
        63 => 'update.member.bank',
        64 => 'view.member.resign',
        65 => 'create.member.resign',
        66 => 'update.member.resign',
        67 => 'view.master.area',
        68 => 'create.master.area',
        69 => 'update.master.area',
        70 => 'delete.master.area',
        71 => 'view.master.transaction-type',
        72 => 'create.master.transaction-type',
        73 => 'update.master.transaction-type',
        74 => 'delete.master.transaction-type',
        75 => 'view.master.location',
        76 => 'create.master.location',
        77 => 'update.master.location',
        78 => 'delete.master.location',
        79 => 'view.master.project',
        80 => 'create.master.project',
        81 => 'update.master.project',
        82 => 'delete.master.project',
        83 => 'view.master.position',
        84 => 'create.master.position',
        85 => 'update.master.position',
        86 => 'delete.master.position',
        87 => 'view.report.per_member',
        88 => 'view.report.per_area',
        89 => 'view.report.annual_shu',
        90 => 'create.report.annual_shu',
        91 => 'update.report.annual_shu',
        92 => 'delete.report.annual_shu',
        93 => 'view.loan.transaction',
        94 => 'view.auth.management',
        95 => 'view.auth.user',
        96 => 'create.auth.user',
        97 => 'update.auth.user',
        98 => 'delete.auth.user',
        99 => 'view.project.management',
        100 => 'view.master.branch',
        101 => 'create.master.branch',
        102 => 'update.master.branch',
        103 => 'delete.master.branch',
        104 => 'view.master.policy',
        105 => 'create.master.policy',
        106 => 'update.master.policy',
        107 => 'delete.master.policy',
        108 => 'view.member.plafond',
        109 => 'create.member.plafond',
        110 => 'update.member.plafond',
        111 => 'delete.member.plafond',
        112 => 'view.auth.level',
        113 => 'create.auth.level',
        114 => 'update.auth.level',
        115 => 'delete.auth.level',
        116 => 'view.master.deposit',
        117 => 'create.master.deposit',
        118 => 'update.master.deposit',
        119 => 'delete.master.deposit',
        120 => 'view.master.loan',
        121 => 'create.master.loan',
        122 => 'update.master.loan',
        123 => 'delete.master.loan',
        124 => 'view.transaction.member.plafond',
        125 => 'create.transaction.member.plafond',
        126 => 'update.transaction.member.plafond',
        127 => 'delete.transaction.member.plafond',
        128 => 'view.transaction.member.loan',
        129 => 'create.transaction.member.loan',
        130 => 'update.transaction.member.loan',
        131 => 'delete.transaction.member.loan',
        132 => 'view.transaction.member.deposit',
        133 => 'create.transaction.member.deposit',
        134 => 'update.transaction.member.deposit',
        135 => 'delete.transaction.member.deposit',
        136 => 'view.transaction.member.retrieve.deposit',
        137 => 'create.transaction.member.retrieve.deposit',
        138 => 'update.transaction.member.retrieve.deposit',
        139 => 'delete.transaction.member.retrieve.deposit',
        140 => 'view.transaction.member.change.deposit',
        141 => 'create.transaction.member.change.deposit',
        142 => 'update.transaction.member.change.deposit',
        143 => 'delete.transaction.member.change.deposit',
        144 => 'view.account.configuration',
        145 => 'create.account.configuration',
        146 => 'update.account.configuration',
        147 => 'delete.account.configuration',
        148 => 'view.master.qna',
        149 => 'create.master.qna',
        150 => 'update.master.qna',
        151 => 'delete.master.qna',
      ),
      'DIREKTUR' => 
      array (
        0 => 'view.transaction.management',
        1 => 'view.account.register',
        2 => 'view.account.login',
        3 => 'view.account.forgot_password',
        4 => 'view.account.bank_data',
        5 => 'view.dashboard.loan_total',
        6 => 'create.dashboard.loan_total',
        7 => 'view.dashboard.deposit_total',
        8 => 'create.dashboard.deposit_total',
        9 => 'view.dashboard.member_card',
        10 => 'create.dashboard.member_card',
        11 => 'view.dashboard.news',
        12 => 'create.dashboard.news',
        13 => 'view.deposit.main_deposit',
        14 => 'update.deposit.main_deposit',
        15 => 'view.deposit.required_deposit',
        16 => 'update.deposit.required_deposit',
        17 => 'view.deposit.voluntary_deposit',
        18 => 'update.deposit.voluntary_deposit',
        19 => 'view.deposit.shu',
        20 => 'update.deposit.shu',
        21 => 'view.deposit_transaction.deposit_application',
        22 => 'create.deposit_transaction.deposit_application',
        23 => 'update.deposit_transaction.deposit_application',
        24 => 'view.deposit_transaction.cashing_application',
        25 => 'create.deposit_transaction.cashing_application',
        26 => 'update.deposit_transaction.cashing_application',
        27 => 'view.deposit_transaction.cashing',
        28 => 'create.deposit_transaction.cashing',
        29 => 'update.deposit_transaction.cashing',
        30 => 'view.deposit_transaction.history',
        31 => 'create.deposit_transaction.history',
        32 => 'update.deposit_transaction.history',
        33 => 'view.loan.application',
        34 => 'create.loan.application',
        35 => 'update.loan.application',
        36 => 'view.loan.approval',
        37 => 'create.loan.approval',
        38 => 'update.loan.approval',
        39 => 'view.loan.detail',
        40 => 'create.loan.detail',
        41 => 'update.loan.detail',
        42 => 'view.loan.history',
        43 => 'create.loan.history',
        44 => 'update.loan.history',
        45 => 'view.shu.config',
        46 => 'create.shu.config',
        47 => 'update.shu.config',
        48 => 'delete.shu.config',
        49 => 'view.shu.history',
        50 => 'create.shu.history',
        51 => 'update.shu.history',
        52 => 'delete.shu.history',
        53 => 'view.member.total_member',
        54 => 'view.member.configuration',
        55 => 'create.member.configuration',
        56 => 'update.member.configuration',
        57 => 'view.member.registration',
        58 => 'create.member.registration',
        59 => 'update.member.registration',
        60 => 'delete.member.registration',
        61 => 'view.member.bank',
        62 => 'create.member.bank',
        63 => 'update.member.bank',
        64 => 'view.member.resign',
        65 => 'create.member.resign',
        66 => 'update.member.resign',
        67 => 'view.master.area',
        68 => 'create.master.area',
        69 => 'update.master.area',
        70 => 'delete.master.area',
        71 => 'view.master.transaction-type',
        72 => 'create.master.transaction-type',
        73 => 'update.master.transaction-type',
        74 => 'delete.master.transaction-type',
        75 => 'view.master.location',
        76 => 'create.master.location',
        77 => 'update.master.location',
        78 => 'delete.master.location',
        79 => 'view.master.project',
        80 => 'create.master.project',
        81 => 'update.master.project',
        82 => 'delete.master.project',
        83 => 'view.master.position',
        84 => 'create.master.position',
        85 => 'update.master.position',
        86 => 'delete.master.position',
        87 => 'view.report.per_member',
        88 => 'view.report.per_area',
        89 => 'view.report.annual_shu',
        90 => 'create.report.annual_shu',
        91 => 'update.report.annual_shu',
        92 => 'delete.report.annual_shu',
        93 => 'view.loan.transaction',
        94 => 'view.auth.management',
        95 => 'view.auth.user',
        96 => 'create.auth.user',
        97 => 'update.auth.user',
        98 => 'delete.auth.user',
        99 => 'view.project.management',
        100 => 'view.master.branch',
        101 => 'create.master.branch',
        102 => 'update.master.branch',
        103 => 'delete.master.branch',
        104 => 'view.master.policy',
        105 => 'create.master.policy',
        106 => 'update.master.policy',
        107 => 'delete.master.policy',
        108 => 'view.member.plafond',
        109 => 'create.member.plafond',
        110 => 'update.member.plafond',
        111 => 'delete.member.plafond',
        112 => 'view.auth.level',
        113 => 'create.auth.level',
        114 => 'update.auth.level',
        115 => 'delete.auth.level',
        116 => 'view.master.deposit',
        117 => 'create.master.deposit',
        118 => 'update.master.deposit',
        119 => 'delete.master.deposit',
        120 => 'view.master.loan',
        121 => 'create.master.loan',
        122 => 'update.master.loan',
        123 => 'delete.master.loan',
        124 => 'view.transaction.member.plafond',
        125 => 'create.transaction.member.plafond',
        126 => 'update.transaction.member.plafond',
        127 => 'delete.transaction.member.plafond',
        128 => 'view.transaction.member.loan',
        129 => 'create.transaction.member.loan',
        130 => 'update.transaction.member.loan',
        131 => 'delete.transaction.member.loan',
        132 => 'view.transaction.member.deposit',
        133 => 'create.transaction.member.deposit',
        134 => 'update.transaction.member.deposit',
        135 => 'delete.transaction.member.deposit',
        136 => 'view.transaction.member.retrieve.deposit',
        137 => 'create.transaction.member.retrieve.deposit',
        138 => 'update.transaction.member.retrieve.deposit',
        139 => 'delete.transaction.member.retrieve.deposit',
        140 => 'view.transaction.member.change.deposit',
        141 => 'create.transaction.member.change.deposit',
        142 => 'update.transaction.member.change.deposit',
        143 => 'delete.transaction.member.change.deposit',
        144 => 'view.account.configuration',
        145 => 'create.account.configuration',
        146 => 'update.account.configuration',
        147 => 'delete.account.configuration',
        148 => 'view.master.qna',
        149 => 'create.master.qna',
        150 => 'update.master.qna',
        151 => 'delete.master.qna',
      ),
    ),
  ),
  'broadcasting' => 
  array (
    'default' => 'log',
    'connections' => 
    array (
      'pusher' => 
      array (
        'driver' => 'pusher',
        'key' => '',
        'secret' => '',
        'app_id' => '',
        'options' => 
        array (
          'cluster' => 'mt1',
          'encrypted' => true,
        ),
      ),
      'redis' => 
      array (
        'driver' => 'redis',
        'connection' => 'default',
      ),
      'log' => 
      array (
        'driver' => 'log',
      ),
      'null' => 
      array (
        'driver' => 'null',
      ),
    ),
  ),
  'cache' => 
  array (
    'default' => 'file',
    'stores' => 
    array (
      'apc' => 
      array (
        'driver' => 'apc',
      ),
      'array' => 
      array (
        'driver' => 'array',
      ),
      'database' => 
      array (
        'driver' => 'database',
        'table' => 'cache',
        'connection' => NULL,
      ),
      'file' => 
      array (
        'driver' => 'file',
        'path' => '/var/www/bsp/storage/framework/cache/data',
      ),
      'memcached' => 
      array (
        'driver' => 'memcached',
        'persistent_id' => NULL,
        'sasl' => 
        array (
          0 => NULL,
          1 => NULL,
        ),
        'options' => 
        array (
        ),
        'servers' => 
        array (
          0 => 
          array (
            'host' => '127.0.0.1',
            'port' => 11211,
            'weight' => 100,
          ),
        ),
      ),
      'redis' => 
      array (
        'driver' => 'redis',
        'connection' => 'cache',
      ),
    ),
    'prefix' => 'bsp_koperasi_cache',
  ),
  'cors' => 
  array (
    'cors_profile' => 'Spatie\\Cors\\CorsProfile\\DefaultProfile',
    'default_profile' => 
    array (
      'allow_credentials' => false,
      'allow_origins' => 
      array (
        0 => '*',
      ),
      'allow_methods' => 
      array (
        0 => 'POST',
        1 => 'GET',
        2 => 'OPTIONS',
        3 => 'PUT',
        4 => 'PATCH',
        5 => 'DELETE',
      ),
      'allow_headers' => 
      array (
        0 => 'Content-Type',
        1 => 'X-Auth-Token',
        2 => 'Origin',
        3 => 'Authorization',
      ),
      'expose_headers' => 
      array (
        0 => 'Cache-Control',
        1 => 'Content-Language',
        2 => 'Content-Type',
        3 => 'Expires',
        4 => 'Last-Modified',
        5 => 'Pragma',
      ),
      'forbidden_response' => 
      array (
        'message' => 'Forbidden (cors).',
        'status' => 403,
      ),
      'max_age' => 86400,
    ),
  ),
  'database' => 
  array (
    'default' => 'mysql',
    'connections' => 
    array (
      'sqlite' => 
      array (
        'driver' => 'sqlite',
        'database' => 'bsp_ags',
        'prefix' => '',
        'foreign_key_constraints' => true,
      ),
      'mysql' => 
      array (
        'driver' => 'mysql',
        'host' => '127.0.0.1',
        'port' => '3306',
        'database' => 'bsp_ags',
        'username' => 'root',
        'password' => 'f0rPC0nly',
        'unix_socket' => '',
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
        'prefix' => '',
        'prefix_indexes' => true,
        'strict' => true,
        'engine' => NULL,
      ),
      'pgsql' => 
      array (
        'driver' => 'pgsql',
        'host' => '127.0.0.1',
        'port' => '3306',
        'database' => 'bsp_ags',
        'username' => 'root',
        'password' => 'f0rPC0nly',
        'charset' => 'utf8',
        'prefix' => '',
        'prefix_indexes' => true,
        'schema' => 'public',
        'sslmode' => 'prefer',
      ),
      'sqlsrv' => 
      array (
        'driver' => 'sqlsrv',
        'host' => '127.0.0.1',
        'port' => '3306',
        'database' => 'bsp_ags',
        'username' => 'root',
        'password' => 'f0rPC0nly',
        'charset' => 'utf8',
        'prefix' => '',
        'prefix_indexes' => true,
      ),
    ),
    'migrations' => 'migrations',
    'redis' => 
    array (
      'client' => 'predis',
      'default' => 
      array (
        'host' => '127.0.0.1',
        'password' => NULL,
        'port' => '6379',
        'database' => 0,
      ),
      'cache' => 
      array (
        'host' => '127.0.0.1',
        'password' => NULL,
        'port' => '6379',
        'database' => 1,
      ),
    ),
  ),
  'datatables' => 
  array (
    'search' => 
    array (
      'smart' => true,
      'multi_term' => true,
      'case_insensitive' => true,
      'use_wildcards' => false,
    ),
    'index_column' => 'DT_RowIndex',
    'engines' => 
    array (
      'eloquent' => 'Yajra\\DataTables\\EloquentDataTable',
      'query' => 'Yajra\\DataTables\\QueryDataTable',
      'collection' => 'Yajra\\DataTables\\CollectionDataTable',
      'resource' => 'Yajra\\DataTables\\ApiResourceDataTable',
    ),
    'builders' => 
    array (
    ),
    'nulls_last_sql' => '%s %s NULLS LAST',
    'error' => NULL,
    'columns' => 
    array (
      'excess' => 
      array (
        0 => 'rn',
        1 => 'row_num',
      ),
      'escape' => '*',
      'raw' => 
      array (
        0 => 'action',
        1 => 'Aksi',
        2 => 'Action',
        3 => 'aksi',
        4 => 'tags',
        5 => 'status',
        6 => 'Status',
        7 => 'deposit',
        8 => 'Deposit',
        9 => 'loan',
        10 => 'Loan',
      ),
      'blacklist' => 
      array (
        0 => 'password',
        1 => 'remember_token',
      ),
      'whitelist' => '*',
    ),
    'json' => 
    array (
      'header' => 
      array (
      ),
      'options' => 0,
    ),
  ),
  'dompdf' => 
  array (
    'show_warnings' => false,
    'orientation' => 'portrait',
    'defines' => 
    array (
      'font_dir' => '/var/www/bsp/storage/fonts/',
      'font_cache' => '/var/www/bsp/storage/fonts/',
      'temp_dir' => '/tmp',
      'chroot' => '/var/www/bsp',
      'enable_font_subsetting' => false,
      'pdf_backend' => 'CPDF',
      'default_media_type' => 'screen',
      'default_paper_size' => 'a4',
      'default_font' => 'serif',
      'dpi' => 96,
      'enable_php' => false,
      'enable_javascript' => true,
      'enable_remote' => true,
      'font_height_ratio' => 1.1,
      'enable_html5_parser' => false,
    ),
  ),
  'excel' => 
  array (
    'cache' => 
    array (
      'enable' => true,
      'driver' => 'memory',
      'settings' => 
      array (
        'memoryCacheSize' => '32MB',
        'cacheTime' => 600,
      ),
      'memcache' => 
      array (
        'host' => 'localhost',
        'port' => 11211,
      ),
      'dir' => '/var/www/bsp/storage/cache',
    ),
    'properties' => 
    array (
      'creator' => 'BSP',
      'lastModifiedBy' => 'Bravo Satria Perkasa',
      'title' => 'Spreadsheet',
      'description' => 'Default spreadsheet export',
      'subject' => 'Spreadsheet export',
      'keywords' => 'bravo, excel, export',
      'category' => 'Excel',
      'manager' => 'BSP',
      'company' => 'BSP',
    ),
    'sheets' => 
    array (
      'pageSetup' => 
      array (
        'orientation' => 'portrait',
        'paperSize' => '9',
        'scale' => '100',
        'fitToPage' => false,
        'fitToHeight' => true,
        'fitToWidth' => true,
        'columnsToRepeatAtLeft' => 
        array (
          0 => '',
          1 => '',
        ),
        'rowsToRepeatAtTop' => 
        array (
          0 => 0,
          1 => 0,
        ),
        'horizontalCentered' => false,
        'verticalCentered' => false,
        'printArea' => NULL,
        'firstPageNumber' => NULL,
      ),
    ),
    'creator' => 'Maatwebsite',
    'csv' => 
    array (
      'delimiter' => ',',
      'enclosure' => '"',
      'line_ending' => '
',
      'use_bom' => false,
    ),
    'export' => 
    array (
      'autosize' => true,
      'autosize-method' => 'approx',
      'generate_heading_by_indices' => true,
      'merged_cell_alignment' => 'left',
      'calculate' => false,
      'includeCharts' => false,
      'sheets' => 
      array (
        'page_margin' => false,
        'nullValue' => NULL,
        'startCell' => 'A1',
        'strictNullComparison' => false,
      ),
      'store' => 
      array (
        'path' => '/var/www/bsp/storage/exports',
        'returnInfo' => false,
      ),
      'pdf' => 
      array (
        'driver' => 'mPDF',
        'drivers' => 
        array (
          'DomPDF' => 
          array (
            'path' => '/var/www/bsp/vendor/dompdf/dompdf/',
          ),
          'tcPDF' => 
          array (
            'path' => '/var/www/bsp/vendor/tecnick.com/tcpdf/',
          ),
          'mPDF' => 
          array (
            'path' => '/var/www/bsp/vendor/mpdf/mpdf/',
          ),
        ),
      ),
    ),
    'filters' => 
    array (
      'registered' => 
      array (
        'chunk' => 'Maatwebsite\\Excel\\Filters\\ChunkReadFilter',
      ),
      'enabled' => 
      array (
      ),
    ),
    'import' => 
    array (
      'heading' => 'slugged',
      'startRow' => 1,
      'separator' => '_',
      'slug_whitelist' => '._',
      'includeCharts' => false,
      'to_ascii' => true,
      'encoding' => 
      array (
        'input' => 'UTF-8',
        'output' => 'UTF-8',
      ),
      'calculate' => true,
      'ignoreEmpty' => false,
      'force_sheets_collection' => false,
      'dates' => 
      array (
        'enabled' => true,
        'format' => false,
        'columns' => 
        array (
        ),
      ),
      'sheets' => 
      array (
        'test' => 
        array (
          'firstname' => 'A2',
        ),
      ),
    ),
    'views' => 
    array (
      'styles' => 
      array (
        'th' => 
        array (
          'font' => 
          array (
            'bold' => true,
            'size' => 12,
          ),
        ),
        'strong' => 
        array (
          'font' => 
          array (
            'bold' => true,
            'size' => 12,
          ),
        ),
        'b' => 
        array (
          'font' => 
          array (
            'bold' => true,
            'size' => 12,
          ),
        ),
        'i' => 
        array (
          'font' => 
          array (
            'italic' => true,
            'size' => 12,
          ),
        ),
        'h1' => 
        array (
          'font' => 
          array (
            'bold' => true,
            'size' => 24,
          ),
        ),
        'h2' => 
        array (
          'font' => 
          array (
            'bold' => true,
            'size' => 18,
          ),
        ),
        'h3' => 
        array (
          'font' => 
          array (
            'bold' => true,
            'size' => 13.5,
          ),
        ),
        'h4' => 
        array (
          'font' => 
          array (
            'bold' => true,
            'size' => 12,
          ),
        ),
        'h5' => 
        array (
          'font' => 
          array (
            'bold' => true,
            'size' => 10,
          ),
        ),
        'h6' => 
        array (
          'font' => 
          array (
            'bold' => true,
            'size' => 7.5,
          ),
        ),
        'a' => 
        array (
          'font' => 
          array (
            'underline' => true,
            'color' => 
            array (
              'argb' => 'FF0000FF',
            ),
          ),
        ),
        'hr' => 
        array (
          'borders' => 
          array (
            'bottom' => 
            array (
              'style' => 'thin',
              'color' => 
              array (
                0 => 'FF000000',
              ),
            ),
          ),
        ),
      ),
    ),
  ),
  'filesystems' => 
  array (
    'default' => 'local',
    'cloud' => 's3',
    'disks' => 
    array (
      'local' => 
      array (
        'driver' => 'local',
        'root' => '/var/www/bsp/storage/app',
      ),
      'public' => 
      array (
        'driver' => 'local',
        'root' => '/var/www/bsp/storage/app/public',
        'url' => 'http://koperasibspguard.com/storage',
        'visibility' => 'public',
      ),
      'deposit' => 
      array (
        'driver' => 'local',
        'root' => '/var/www/bsp/storage/app/report/deposit',
        'empty_at' => '00:00',
        'empty_period' => 'daily',
      ),
      'template' => 
      array (
        'driver' => 'local',
        'root' => '/var/www/bsp/storage/app/template',
      ),
      's3' => 
      array (
        'driver' => 's3',
        'key' => NULL,
        'secret' => NULL,
        'region' => NULL,
        'bucket' => NULL,
        'url' => NULL,
      ),
    ),
  ),
  'hashing' => 
  array (
    'driver' => 'bcrypt',
    'bcrypt' => 
    array (
      'rounds' => 10,
    ),
    'argon' => 
    array (
      'memory' => 1024,
      'threads' => 2,
      'time' => 2,
    ),
  ),
  'javascript' => 
  array (
    'bind_js_vars_to_this_view' => 'layouts.partials.footer',
    'js_namespace' => 'develoop',
  ),
  'jwt' => 
  array (
    'secret' => 'TYY9ORXOgYLejiopLgf3x3VGyGzVcKew',
    'keys' => 
    array (
      'public' => NULL,
      'private' => NULL,
      'passphrase' => NULL,
    ),
    'ttl' => 2880,
    'refresh_ttl' => 20160,
    'algo' => 'HS256',
    'required_claims' => 
    array (
      0 => 'iss',
      1 => 'iat',
      2 => 'exp',
      3 => 'nbf',
      4 => 'sub',
      5 => 'jti',
    ),
    'persistent_claims' => 
    array (
    ),
    'lock_subject' => true,
    'leeway' => 0,
    'blacklist_enabled' => true,
    'blacklist_grace_period' => 0,
    'decrypt_cookies' => false,
    'providers' => 
    array (
      'jwt' => 'Tymon\\JWTAuth\\Providers\\JWT\\Lcobucci',
      'auth' => 'Tymon\\JWTAuth\\Providers\\Auth\\Illuminate',
      'storage' => 'Tymon\\JWTAuth\\Providers\\Storage\\Illuminate',
    ),
  ),
  'logging' => 
  array (
    'default' => 'stack',
    'channels' => 
    array (
      'stack' => 
      array (
        'driver' => 'stack',
        'channels' => 
        array (
          0 => 'daily',
        ),
      ),
      'single' => 
      array (
        'driver' => 'single',
        'path' => '/var/www/bsp/storage/logs/laravel.log',
        'level' => 'debug',
      ),
      'daily' => 
      array (
        'driver' => 'daily',
        'path' => '/var/www/bsp/storage/logs/laravel.log',
        'level' => 'debug',
        'days' => 14,
      ),
      'slack' => 
      array (
        'driver' => 'slack',
        'url' => NULL,
        'username' => 'Laravel Log',
        'emoji' => ':boom:',
        'level' => 'critical',
      ),
      'papertrail' => 
      array (
        'driver' => 'monolog',
        'level' => 'debug',
        'handler' => 'Monolog\\Handler\\SyslogUdpHandler',
        'handler_with' => 
        array (
          'host' => NULL,
          'port' => NULL,
        ),
      ),
      'stderr' => 
      array (
        'driver' => 'monolog',
        'handler' => 'Monolog\\Handler\\StreamHandler',
        'with' => 
        array (
          'stream' => 'php://stderr',
        ),
      ),
      'syslog' => 
      array (
        'driver' => 'syslog',
        'level' => 'debug',
      ),
      'errorlog' => 
      array (
        'driver' => 'errorlog',
        'level' => 'debug',
      ),
    ),
  ),
  'mail' => 
  array (
    'driver' => 'smtp',
    'host' => 'mail.bspguard.com',
    'port' => '587',
    'from' => 
    array (
      'address' => 'no-reply@bspguard.com',
      'name' => 'BSP Koperasi',
    ),
    'encryption' => NULL,
    'username' => 'koperasi@bspguard.com',
    'password' => 'koperasi2023',
    'sendmail' => '/usr/sbin/sendmail -bs',
    'markdown' => 
    array (
      'theme' => 'default',
      'paths' => 
      array (
        0 => '/var/www/bsp/resources/views/vendor/mail',
      ),
    ),
  ),
  'menu' => 
  array (
    'SUPERADMIN' => 
    array (
      0 => 'NAVIGATION 1',
      1 => 
      array (
        'text' => 'Dashboard',
        'url' => 'dashboard',
      ),
      2 => 
      array (
        'text' => 'Anggota',
        'url' => 'members',
        'icon' => 'users',
        'label' => '',
        'label_color' => 'success',
      ),
      3 => 'Kelola M Data',
      4 => 
      array (
        'text' => 'Transaksi',
        'icon' => 'briefcase',
        'submenu' => 
        array (
          0 => 
          array (
            'text' => 'Daftar Pinjaman',
            'url' => 'loans',
          ),
        ),
      ),
      5 => 
      array (
        'text' => 'Proyek',
        'icon' => 'briefcase',
        'submenu' => 
        array (
          0 => 
          array (
            'text' => 'Daftar Proyek',
            'url' => 'projects',
          ),
          1 => 
          array (
            'text' => 'Daftar Area',
            'url' => 'regions',
          ),
          2 => 
          array (
            'text' => 'Daftar Lokasi',
            'url' => 'locations',
          ),
        ),
      ),
      6 => 
      array (
        'text' => 'Posisi',
        'url' => 'positions',
        'icon' => 'sitemap',
      ),
      7 => 
      array (
        'text' => 'Tipe Deposito/Simpanan',
        'url' => 'deposits',
        'icon' => 'book',
      ),
      8 => 
      array (
        'text' => 'Otentifikasi',
        'icon' => 'lock',
        'submenu' => 
        array (
          0 => 
          array (
            'text' => 'Daftar User',
            'url' => 'users',
          ),
          1 => 
          array (
            'text' => 'Level User',
            'url' => 'levels',
          ),
          2 => 
          array (
            'text' => 'Hak User',
            'url' => 'privileges',
          ),
        ),
      ),
    ),
  ),
  'onesignal' => 
  array (
    'app_id' => 'bd074873-34b8-4884-8893-726d866600a1',
    'rest_api_key' => 'ZTA2NDI5NjgtNWQ4Mi00ZWVjLTlhZWEtN2FiZGZkNWE5NjQy',
    'user_auth_key' => 'NDJiYTQzMjAtNjE5Yy00Yzg3LWI4YWItNTA2NTlmNGVhNGJi',
  ),
  'permission' => 
  array (
    'models' => 
    array (
      'permission' => 'Spatie\\Permission\\Models\\Permission',
      'role' => 'Spatie\\Permission\\Models\\Role',
    ),
    'table_names' => 
    array (
      'roles' => 'levels',
      'permissions' => 'permissions',
      'model_has_permissions' => 'model_has_permissions',
      'model_has_roles' => 'model_has_roles',
      'role_has_permissions' => 'role_has_permissions',
    ),
    'column_names' => 
    array (
      'model_morph_key' => 'model_id',
    ),
    'display_permission_in_exception' => false,
    'cache' => 
    array (
      'expiration_time' => 1440,
      'key' => 'spatie.permission.cache',
      'model_key' => 'name',
      'store' => 'default',
    ),
  ),
  'queue' => 
  array (
    'default' => 'sync',
    'connections' => 
    array (
      'sync' => 
      array (
        'driver' => 'sync',
      ),
      'database' => 
      array (
        'driver' => 'database',
        'table' => 'jobs',
        'queue' => 'default',
        'retry_after' => 90,
      ),
      'beanstalkd' => 
      array (
        'driver' => 'beanstalkd',
        'host' => 'localhost',
        'queue' => 'default',
        'retry_after' => 90,
      ),
      'sqs' => 
      array (
        'driver' => 'sqs',
        'key' => 'your-public-key',
        'secret' => 'your-secret-key',
        'prefix' => 'https://sqs.us-east-1.amazonaws.com/your-account-id',
        'queue' => 'your-queue-name',
        'region' => 'us-east-1',
      ),
      'redis' => 
      array (
        'driver' => 'redis',
        'connection' => 'default',
        'queue' => 'default',
        'retry_after' => 90,
        'block_for' => NULL,
      ),
    ),
    'failed' => 
    array (
      'database' => 'mysql',
      'table' => 'failed_jobs',
    ),
  ),
  'services' => 
  array (
    'mailgun' => 
    array (
      'domain' => NULL,
      'secret' => NULL,
      'endpoint' => 'api.mailgun.net',
    ),
    'onesignal' => 
    array (
      'app_id' => NULL,
      'rest_api_key' => NULL,
    ),
    'ses' => 
    array (
      'key' => NULL,
      'secret' => NULL,
      'region' => 'us-east-1',
    ),
    'sparkpost' => 
    array (
      'secret' => NULL,
    ),
    'stripe' => 
    array (
      'model' => 'App\\User',
      'key' => NULL,
      'secret' => NULL,
      'webhook' => 
      array (
        'secret' => NULL,
        'tolerance' => 300,
      ),
    ),
  ),
  'session' => 
  array (
    'driver' => 'file',
    'lifetime' => '120',
    'expire_on_close' => false,
    'encrypt' => false,
    'files' => '/var/www/bsp/storage/framework/sessions',
    'connection' => NULL,
    'table' => 'sessions',
    'store' => NULL,
    'lottery' => 
    array (
      0 => 2,
      1 => 100,
    ),
    'cookie' => 'bsp_koperasi_session',
    'path' => '/',
    'domain' => NULL,
    'secure' => false,
    'http_only' => true,
    'same_site' => NULL,
  ),
  'telescope' => 
  array (
    'domain' => NULL,
    'path' => 'telescope',
    'driver' => 'database',
    'storage' => 
    array (
      'database' => 
      array (
        'connection' => 'mysql',
      ),
    ),
    'enabled' => true,
    'middleware' => 
    array (
      0 => 'web',
      1 => 'Laravel\\Telescope\\Http\\Middleware\\Authorize',
    ),
    'ignore_paths' => 
    array (
    ),
    'ignore_commands' => 
    array (
    ),
    'watchers' => 
    array (
      'Laravel\\Telescope\\Watchers\\CacheWatcher' => true,
      'Laravel\\Telescope\\Watchers\\CommandWatcher' => 
      array (
        'enabled' => true,
        'ignore' => 
        array (
        ),
      ),
      'Laravel\\Telescope\\Watchers\\DumpWatcher' => true,
      'Laravel\\Telescope\\Watchers\\EventWatcher' => true,
      'Laravel\\Telescope\\Watchers\\ExceptionWatcher' => true,
      'Laravel\\Telescope\\Watchers\\JobWatcher' => true,
      'Laravel\\Telescope\\Watchers\\LogWatcher' => true,
      'Laravel\\Telescope\\Watchers\\MailWatcher' => true,
      'Laravel\\Telescope\\Watchers\\ModelWatcher' => 
      array (
        'enabled' => true,
        'events' => 
        array (
          0 => 'eloquent.*',
        ),
      ),
      'Laravel\\Telescope\\Watchers\\NotificationWatcher' => true,
      'Laravel\\Telescope\\Watchers\\QueryWatcher' => 
      array (
        'enabled' => true,
        'ignore_packages' => true,
        'slow' => 100,
      ),
      'Laravel\\Telescope\\Watchers\\RedisWatcher' => true,
      'Laravel\\Telescope\\Watchers\\RequestWatcher' => 
      array (
        'enabled' => true,
        'size_limit' => 64,
      ),
      'Laravel\\Telescope\\Watchers\\GateWatcher' => 
      array (
        'enabled' => true,
        'ignore_abilities' => 
        array (
        ),
        'ignore_packages' => true,
      ),
      'Laravel\\Telescope\\Watchers\\ScheduleWatcher' => true,
    ),
  ),
  'view' => 
  array (
    'paths' => 
    array (
      0 => '/var/www/bsp/resources/views',
    ),
    'compiled' => '/var/www/bsp/storage/framework/views',
  ),
  'debug-server' => 
  array (
    'host' => 'tcp://127.0.0.1:9912',
  ),
  'ide-helper' => 
  array (
    'filename' => '_ide_helper',
    'format' => 'php',
    'meta_filename' => '.phpstorm.meta.php',
    'include_fluent' => false,
    'include_factory_builders' => false,
    'write_model_magic_where' => true,
    'write_model_relation_count_properties' => true,
    'write_eloquent_model_mixins' => false,
    'include_helpers' => false,
    'helper_files' => 
    array (
      0 => '/var/www/bsp/vendor/laravel/framework/src/Illuminate/Support/helpers.php',
    ),
    'model_locations' => 
    array (
      0 => 'app',
    ),
    'ignored_models' => 
    array (
    ),
    'extra' => 
    array (
      'Eloquent' => 
      array (
        0 => 'Illuminate\\Database\\Eloquent\\Builder',
        1 => 'Illuminate\\Database\\Query\\Builder',
      ),
      'Session' => 
      array (
        0 => 'Illuminate\\Session\\Store',
      ),
    ),
    'magic' => 
    array (
    ),
    'interfaces' => 
    array (
    ),
    'custom_db_types' => 
    array (
    ),
    'model_camel_case_properties' => false,
    'type_overrides' => 
    array (
      'integer' => 'int',
      'boolean' => 'bool',
    ),
    'include_class_docblocks' => false,
  ),
  'trustedproxy' => 
  array (
    'proxies' => NULL,
    'headers' => 30,
  ),
  'tinker' => 
  array (
    'commands' => 
    array (
    ),
    'dont_alias' => 
    array (
      0 => 'App\\Nova',
    ),
  ),
);
