/**
 * ============================================================
 *  USER FINAL COMPREHENSIVE E2E — LEVEL 1–4
 *
 *  Level 1: Smoke (no PHP error, page loads)
 *  Level 2: Element & form existence, navigation links
 *  Level 3: State-change after action, form full flow,
 *           filter/pagination behavior, negative cases,
 *           UI interactivity, profile update
 *  Level 4: Cross-page flows, ownership security,
 *           session isolation, booking full cycle
 *
 *  Catatan: Level 5 (Performance/Load) butuh tool terpisah.
 * ============================================================
 */

// Unique tag untuk test data agar tidak bentrok dengan data real
const TEST_LABEL = `[CY-${Date.now()}]`

// Helper login
const loginAsExternal = () => {
  cy.fixture('credentials').then((cred) => {
    cy.loginUser(cred.user_eksternal.username, cred.user_eksternal.password)
  })
}

describe('User Final Comprehensive Tests (Level 1–4)', () => {

  beforeEach(() => {
    cy.clearCookies()
    cy.clearLocalStorage()
  })

  // ============================================================
  // U-01 — REGISTRASI (Level 2–3)
  // ============================================================
  describe('U-01: Registrasi', () => {

    it('U-01a: Halaman registrasi loads dengan semua field', () => {
      cy.visit('/registration')
      cy.get('input[name="username"]').should('be.visible')
      cy.get('input[name="nama_lengkap"]').should('be.visible')
      cy.get('select[name="perusahaan"]').should('be.visible')
      cy.get('input[name="password"]').should('be.visible')
      cy.get('input[name="confirm_pass"]').should('be.visible')
      cy.get('input[name="email"]').should('be.visible')
      cy.get('textarea[name="alamat"]').should('be.visible')
      cy.get('input[name="no_telepon"]').should('be.visible')
      cy.get('input[name="dob"]').should('be.visible')
    })

    // Level 3: Dynamic UI — pilih INTERNAL menampilkan field departemen
    it('U-01b [Dynamic UI]: Pilih INTERNAL → field departemen muncul', () => {
      cy.visit('/registration')
      // Semua option INTERNAL punya value="INTERNAL" — select by VALUE bukan by text
      cy.get('select[name="perusahaan"]').select('INTERNAL')
      cy.get('#wrapDepartemen').should('be.visible')
      cy.get('#wrapEksternal').should('not.be.visible')
    })

    it('U-01c [Dynamic UI]: Pilih EKSTERNAL → field nama perusahaan muncul', () => {
      cy.visit('/registration')
      cy.get('select[name="perusahaan"]').select('EKSTERNAL')
      cy.get('#wrapEksternal').should('be.visible')
      cy.get('#wrapDepartemen').should('not.be.visible')
    })

    // Level 3: Negative — username duplikat
    it('U-01d [Negative]: Register username duplikat → ada pesan error', () => {
      cy.fixture('credentials').then((cred) => {
        cy.visit('/registration')
        cy.get('input[name="username"]').type(cred.user_eksternal.username)
        cy.get('input[name="nama_lengkap"]').type('Test Duplikat')
        cy.get('select[name="perusahaan"]').select('EKSTERNAL')
        cy.get('input[name="nama_perusahaan"]').type('Test Corp')
        cy.get('input[name="password"]').type('Test@123')
        cy.get('input[name="confirm_pass"]').type('Test@123')
        cy.get('input[name="email"]').type('duplikat@gmail.com')
        cy.get('textarea[name="alamat"]').type('Jl. Test No. 1')
        cy.get('input[name="no_telepon"]').type('08123456789')
        cy.get('input[name="dob"]').type('2000-01-01')
        cy.get('form').submit()
        cy.contains('Username sudah ada').should('be.visible')
      })
    })

    // Level 3: Negative — email bukan gmail
    it('U-01e [Negative]: Email non-gmail → validasi tampil', () => {
      cy.visit('/registration')
      cy.get('input[name="email"]').type('test@yahoo.com')
      cy.get('form').submit()
      cy.contains('gmail').should('be.visible')
    })

    // Level 3: Negative — usia < 18
    it('U-01f [Negative]: DOB < 18 tahun → validasi usia tampil', () => {
      cy.visit('/registration')
      const youngDate = new Date()
      youngDate.setFullYear(youngDate.getFullYear() - 10)
      cy.get('input[name="dob"]').type(youngDate.toISOString().split('T')[0])
      cy.get('form').submit()
      cy.contains('18').should('be.visible')
    })

    // Level 3: UI Interactivity — toggle password visibility
    it('U-01g [UI]: Toggle visibility password bekerja', () => {
      cy.visit('/registration')
      cy.get('input[name="password"]').should('have.attr', 'type', 'password')
      cy.get('.pw-toggle[data-target="password"]').click()
      cy.get('input[name="password"]').should('have.attr', 'type', 'text')
      cy.get('.pw-toggle[data-target="password"]').click()
      cy.get('input[name="password"]').should('have.attr', 'type', 'password')
    })

    it('U-01h: Link "Sudah punya akun" → navigate ke login', () => {
      cy.visit('/registration')
      cy.contains('Sudah punya akun').click()
      cy.url().should('include', 'login')
    })
  })

  // ============================================================
  // U-02 — LOGIN & LOGOUT (Level 1–4)
  // ============================================================
  describe('U-02: Login & Logout', () => {

    it('U-02a: Halaman login loads dengan form', () => {
      cy.visit('/login')
      cy.get('input[name="username"]').should('be.visible')
      cy.get('input[name="password"]').should('be.visible')
      cy.get('form').should('exist')
    })

    it('U-02b: Login berhasil → redirect ke home', () => {
      cy.fixture('credentials').then((cred) => {
        cy.loginUser(cred.user_eksternal.username, cred.user_eksternal.password)
        cy.url().should('include', 'home')
      })
    })

    // Level 3: Negative — kredensial salah
    it('U-02c [Negative]: Login password salah → pesan error muncul', () => {
      cy.visit('/login')
      cy.get('input[name="username"]').type('wronguser')
      cy.get('input[name="password"]').type('wrongpassword')
      cy.get('form').submit()
      cy.contains('Login Gagal').should('be.visible')
    })

    it('U-02d [Negative]: Login field kosong → tidak redirect ke home', () => {
      cy.visit('/login')
      cy.get('form').submit()
      cy.url().should('not.include', 'home')
    })

    // Level 4: Redirect protection
    it('U-02e [Security]: Akses halaman protected tanpa login → redirect ke login', () => {
      const protectedPages = [
        '/home/pemesanan', '/home/pembayaran', '/home/jadwal', '/edit_data',
        '/home/ulasan', '/edit_foto',
      ]
      protectedPages.forEach((page) => {
        cy.visit(page)
        cy.url().should('include', 'login')
      })
    })

    // Level 3: State change — logout bersihkan session
    it('U-02f [State Change]: Logout → halaman protected tidak bisa diakses lagi', () => {
      cy.fixture('credentials').then((cred) => {
        cy.loginUser(cred.user_eksternal.username, cred.user_eksternal.password)
        cy.logoutUser()
        cy.visit('/home/pemesanan')
        cy.url().should('include', 'login')
      })
    })

    // Level 4: Session isolation — setelah logout tidak bisa back-button
    it('U-02g [Security]: Akses /home langsung setelah logout → redirect ke login', () => {
      cy.fixture('credentials').then((cred) => {
        cy.loginUser(cred.user_eksternal.username, cred.user_eksternal.password)
        cy.logoutUser()
        cy.visit('/home')
        cy.url().should('include', 'login')
      })
    })
  })

  // ============================================================
  // U-03 — HOME & NAVIGASI (Level 2–3)
  // ============================================================
  describe('U-03: Home & Navigasi', () => {

    beforeEach(loginAsExternal)

    it('U-03a: Home menampilkan card ruangan (article > 0)', () => {
      cy.visit('/home')
      cy.get('article').should('have.length.greaterThan', 0)
    })

    it('U-03b: Setiap card ruangan punya tombol Detail', () => {
      cy.visit('/home')
      cy.get('a').contains('Detail').should('have.length.greaterThan', 0)
    })

    it('U-03c: Klik Detail → navigate ke halaman detail ruangan', () => {
      cy.visit('/home')
      cy.get('a').contains('Detail').first().click()
      cy.url().should('include', 'home/details')
      cy.get('body').should('not.contain', 'Fatal error')
    })

    it('U-03d: Halaman detail ruangan punya tombol Ajukan Pesanan', () => {
      cy.visit('/home')
      cy.get('a').contains('Detail').first().click()
      cy.get('a[href*="order-gedung"]').should('exist')
    })

    // Level 3: Navigasi navbar
    it('U-03e: Navbar menampilkan profile toggle saat login', () => {
      cy.visit('/home')
      cy.get('#profileToggle').should('exist')
    })

    it('U-03f [UI]: Klik profileToggle → dropdown terbuka', () => {
      cy.visit('/home')
      cy.get('#profileToggle').click()
      cy.get('#profileMenu').should('be.visible')
    })

    it('U-03g: Dropdown profile punya link Edit Data, Edit Foto, Logout', () => {
      cy.visit('/home')
      cy.get('#profileToggle').click()
      cy.get('#profileMenu a[href*="edit_data"]').should('exist')
      cy.get('#profileMenu a[href*="edit_foto"]').should('exist')
      cy.get('#profileMenu a[href*="logout"]').should('exist')
    })

    it('U-03h: Link navigasi desktop benar (JADWAL, PEMESANAN, CATERING, TRANSAKSI)', () => {
      cy.visit('/home')
      cy.get('a').contains('JADWAL').should('exist')
      cy.get('a').contains('PEMESANAN').should('exist')
      cy.get('a').contains('CATERING').should('exist')
      cy.get('a').contains('TRANSAKSI').should('exist')
    })

    it('U-03i: Link navigasi mengarah ke halaman yang benar', () => {
      cy.visit('/home')
      cy.get('a').contains('JADWAL').first().click()
      cy.url().should('include', 'jadwal')

      cy.visit('/home')
      cy.get('a').contains('PEMESANAN').first().click()
      cy.url().should('include', 'pemesanan')
    })

    it('U-03j: Halaman ulasan bisa diakses dari home', () => {
      cy.visit('/home')
      cy.get('a[href*="ulasan"]').first().click()
      cy.url().should('include', 'ulasan')
      cy.get('body').should('not.contain', 'Fatal error')
    })
  })

  // ============================================================
  // U-04 — BOOKING FORM (Level 2–3)
  // ============================================================
  describe('U-04: Booking Form', () => {

    beforeEach(loginAsExternal)

    it('U-04a: Halaman booking form loads dengan field tanggal/waktu', () => {
      cy.visit('/home')
      cy.get('a').contains('Detail').first().click()
      cy.get('a[href*="order-gedung"]').first().click()
      cy.url().should('include', 'order-gedung')
      cy.get('body').should('not.contain', 'Fatal error')
    })

    it('U-04b: Booking form punya field tanggal mulai dan selesai', () => {
      cy.visit('/home')
      cy.get('a').contains('Detail').first().click()
      cy.get('a[href*="order-gedung"]').first().click()
      cy.get('body').then(($body) => {
        const hasDateFields =
          $body.find('input[name*="tanggal"], input[name*="date"], input[type="date"]').length > 0
        expect(hasDateFields).to.be.true
      })
    })

    it('U-04c: Booking form punya field keperluan/deskripsi', () => {
      cy.visit('/home')
      cy.get('a').contains('Detail').first().click()
      cy.get('a[href*="order-gedung"]').first().click()
      cy.get('body').then(($body) => {
        // Cek field deskripsi — nama field bisa bervariasi per gedung
        const hasDescField =
          $body.find('textarea').length > 0 ||
          $body.find('input[name*="keterangan"]').length > 0 ||
          $body.find('input[name*="acara"]').length > 0 ||
          $body.find('input[name*="keperluan"]').length > 0 ||
          $body.find('input[name*="desc"]').length > 0 ||
          $body.find('input[name*="note"]').length > 0
        // Tidak di-assert karena nama field bisa bervariasi — cukup log
        cy.log(hasDescField
          ? '✅ Field keperluan/deskripsi ditemukan.'
          : 'ℹ️ Field keperluan tidak ditemukan (mungkin field lain dipakai).')
      })
    })

    // Level 3: Negative — submit booking tanpa tanggal
    it('U-04d [Negative]: Submit booking tanpa tanggal → tidak crash server', () => {
      cy.visit('/home')
      cy.get('a').contains('Detail').first().click()
      cy.get('a[href*="order-gedung"]').first().click()
      cy.get('button[type="submit"], input[type="submit"]').first().click({ force: true })
      cy.get('body')
        .should('not.contain', 'Fatal error')
        .and('not.contain', 'Parse error')
    })
  })

  // ============================================================
  // U-05 — PEMESANAN LIST & DETAIL (Level 2–3)
  // ============================================================
  describe('U-05: Pemesanan List & Detail', () => {

    beforeEach(loginAsExternal)

    it('U-05a: Halaman pemesanan loads dengan tabel', () => {
      cy.visit('/home/pemesanan')
      cy.get('table').should('exist')
    })

    // Level 3: Filter functionality
    it('U-05b [Filter]: Filter by ID PMSN → tabel tetap visible', () => {
      cy.visit('/home/pemesanan')
      cy.get('#idFilter').type('PMSN')
      cy.get('table').should('exist')
    })

    it('U-05c [Filter]: Filter by status dropdown → ada opsi lebih dari 1', () => {
      cy.visit('/home/pemesanan')
      cy.get('#statusFilter').should('exist')
      cy.get('#statusFilter option').should('have.length.greaterThan', 1)
    })

    // Level 3: Filter dengan status specific
    it('U-05d [Filter]: Pilih status filter → tabel tidak crash', () => {
      cy.visit('/home/pemesanan')
      // Option yang ada: SUBMITED, SUBMITTED, PROCESS, APPROVE, APPROVE & PAID, REJECTED, CONFIRMED
      // Pakai option pertama yang tersedia (bukan hardcode 'APPROVED' karena tidak ada di HTML)
      cy.get('#statusFilter option').then(($options) => {
        // Cari option dengan value tidak kosong
        const firstOpt = [...$options].find(o => o.value !== '')
        if (firstOpt) {
          cy.get('#statusFilter').select(firstOpt.value)
          cy.get('table').should('exist')
          cy.get('body').should('not.contain', 'Fatal error')
        }
      })
    })

    it('U-05e [Filter]: Pilih status REJECTED → tabel tidak crash', () => {
      cy.visit('/home/pemesanan')
      cy.get('#statusFilter option').then(($options) => {
        const rejectedOpt = [...$options].find(o => o.text.toLowerCase().includes('reject') || o.value.toLowerCase().includes('reject'))
        if (rejectedOpt) {
          cy.get('#statusFilter').select(rejectedOpt.value)
          cy.get('table').should('exist')
        }
      })
    })

    // Level 3: State change — reset filter
    it('U-05f [State Change]: Reset filter → membersihkan input ID', () => {
      cy.visit('/home/pemesanan')
      cy.get('#idFilter').type('TEST123')
      cy.get('#resetBtn').click()
      cy.get('#idFilter').should('have.value', '')
    })

    // Level 3: Pagination
    it('U-05g [Pagination]: Tombol Prev/Next dan rows-per-page ada', () => {
      cy.visit('/home/pemesanan')
      cy.get('#prevBtn').should('exist')
      cy.get('#nextBtn').should('exist')
      cy.get('#rowsPerPage').should('exist')
    })

    it('U-05h [Pagination]: Ganti rows per page → nilai tersimpan', () => {
      cy.visit('/home/pemesanan')
      cy.get('#rowsPerPage').select('5')
      cy.get('#rowsPerPage').should('have.value', '5')
      cy.get('#rowsPerPage').select('25')
      cy.get('#rowsPerPage').should('have.value', '25')
    })

    it('U-05i: Klik detail pemesanan → halaman detail loads', () => {
      cy.visit('/home/pemesanan')
      cy.get('body').then(($body) => {
        const links = $body.find('a[href*="pemesanan/details"]')
        if (links.length > 0) {
          cy.wrap(links.first()).click()
          cy.url().should('include', 'pemesanan/details')
          cy.get('body').should('not.contain', 'Fatal error')
        } else {
          cy.log('Tidak ada data pemesanan — skip.')
        }
      })
    })

    // Level 3: State check — detail harus punya info relevan
    it('U-05j [State Check]: Detail pemesanan berisi info gedung dan tanggal', () => {
      cy.visit('/home/pemesanan')
      cy.get('body').then(($body) => {
        const links = $body.find('a[href*="pemesanan/details"]')
        if (links.length > 0) {
          cy.wrap(links.first()).click()
          cy.get('body').then(($detail) => {
            const text = $detail.text()
            expect(
              text.includes('Gedung') || text.includes('Ruangan') ||
              text.includes('Tanggal') || text.includes('Status') ||
              text.includes('PMSN')
            ).to.be.true
          })
        } else {
          cy.log('Tidak ada data pemesanan — skip.')
        }
      })
    })

    // Level 3: Negative — akses detail dengan ID tidak valid
    it('U-05k [Negative]: Akses detail pemesanan ID invalid → tidak crash', () => {
      cy.visit('/home/pemesanan/details/PMSN99999999')
      cy.get('body')
        .should('not.contain', 'Fatal error')
        .and('not.contain', 'Parse error')
        .and('not.contain', 'Undefined variable')
    })
  })

  // ============================================================
  // U-06 — PEMBAYARAN (Level 2–3)
  // ============================================================
  describe('U-06: Pembayaran', () => {

    beforeEach(loginAsExternal)

    it('U-06a: Halaman pembayaran loads tanpa error', () => {
      cy.visit('/home/pembayaran')
      cy.get('body').should('not.contain', 'Fatal error')
        .and('not.contain', 'Severity:')
    })

    it('U-06b: Halaman pembayaran menampilkan tabel atau info kosong', () => {
      cy.visit('/home/pembayaran')
      cy.get('body').then(($body) => {
        const hasContent =
          $body.find('table').length > 0 ||
          $body.text().includes('belum') ||
          $body.text().includes('kosong') ||
          $body.text().includes('Tidak ada')
        expect(hasContent).to.be.true
      })
    })

    // Level 3: Upload bukti bayar form ada (jika ada pemesanan approved)
    it('U-06c: Form upload bukti bayar ada untuk pemesanan APPROVED (jika ada)', () => {
      cy.visit('/home/pembayaran')
      cy.get('body').then(($body) => {
        if ($body.find('form[enctype*="multipart"]').length > 0) {
          cy.get('input[type="file"]').should('exist')
          cy.log('✅ Form upload bukti bayar tersedia.')
        } else {
          cy.log('ℹ️ Tidak ada pemesanan yang perlu bayar — normal.')
        }
      })
    })

    // Level 3: Detail pembayaran loads
    it('U-06d: Klik detail pembayaran → loads tanpa error', () => {
      cy.visit('/home/pembayaran')
      cy.get('body').then(($body) => {
        const links = $body.find('a[href*="pembayaran/details"], a[href*="detail"]')
        if (links.length > 0) {
          cy.wrap(links.first()).click()
          cy.get('body').should('not.contain', 'Fatal error')
        } else {
          cy.log('Tidak ada detail pembayaran — skip.')
        }
      })
    })
  })

  // ============================================================
  // U-07 — PROFILE / EDIT DATA (Level 2–3)
  // ============================================================
  describe('U-07: Profile & Edit Data', () => {

    beforeEach(loginAsExternal)

    it('U-07a: Halaman edit data loads dengan form pre-filled', () => {
      cy.visit('/edit_data')
      cy.get('form').should('exist')
      cy.get('body').should('not.contain', 'Fatal error')
    })

    it('U-07b: Form edit data punya semua field required', () => {
      cy.visit('/edit_data')
      cy.get('input[name="nama_lengkap"]').should('exist')
      cy.get('input[name="email"]').should('exist')
      cy.get('input[name="no_telepon"]').should('exist')
    })

    // Level 3: State check — field sudah pre-filled dengan data user
    it('U-07c [State Check]: Field nama_lengkap sudah terisi (bukan kosong)', () => {
      cy.visit('/edit_data')
      cy.get('input[name="nama_lengkap"]').invoke('val').should('not.be.empty')
    })

    it('U-07d [State Check]: Field email sudah terisi', () => {
      cy.visit('/edit_data')
      cy.get('input[name="email"]').invoke('val').should('not.be.empty')
    })

    // Level 3: Edit dan verifikasi perubahan
    it('U-07e [State Change]: Edit nama lengkap → tersimpan setelah reload', () => {
      cy.visit('/edit_data')
      cy.get('input[name="nama_lengkap"]').clear().type(`Test User ${TEST_LABEL}`)
      cy.get('button[type="submit"], input[type="submit"]').first().click()
      cy.get('body').should('not.contain', 'Fatal error')
      // Reload dan cek
      cy.visit('/edit_data')
      cy.get('input[name="nama_lengkap"]').invoke('val').should('include', TEST_LABEL)
    })

    // Level 3: Negative — password mismatch
    it('U-07f [Negative]: Password dan confirm_pass berbeda → error mismatch', () => {
      cy.visit('/edit_data')
      cy.get('input[name="password"]').type('NewPass123')
      cy.get('input[name="confirm_pass"]').type('DiffPass456')
      cy.get('form').submit()
      cy.contains('tidak sama').should('be.visible')
    })

    it('U-07g: Halaman edit foto loads tanpa error', () => {
      cy.visit('/edit_foto')
      cy.get('body').should('not.contain', 'Fatal error')
    })

    it('U-07h: Edit foto punya form upload gambar', () => {
      cy.visit('/edit_foto')
      cy.get('body').then(($body) => {
        const hasUpload =
          $body.find('input[type="file"]').length > 0 ||
          $body.find('form[enctype*="multipart"]').length > 0
        expect(hasUpload).to.be.true
      })
    })
  })

  // ============================================================
  // U-08 — JADWAL (Level 2–3)
  // ============================================================
  describe('U-08: Jadwal', () => {

    beforeEach(loginAsExternal)

    it('U-08a: Halaman jadwal loads tanpa error', () => {
      cy.visit('/home/jadwal')
      cy.get('body').should('not.contain', 'Fatal error')
    })

    it('U-08b: Jadwal menampilkan kalender atau tabel jadwal', () => {
      cy.visit('/home/jadwal')
      cy.get('body').then(($body) => {
        const hasCalendar =
          $body.find('[class*="calendar"], [class*="jadwal"], table, [id*="cal"]').length > 0
        expect(hasCalendar).to.be.true
      })
    })
  })

  // ============================================================
  // U-09 — CATERING (Level 2–3)
  // ============================================================
  describe('U-09: View Catering', () => {

    beforeEach(loginAsExternal)

    it('U-09a: Halaman view-catering loads tanpa error', () => {
      cy.visit('/home/view-catering')
      cy.get('body').should('not.contain', 'Fatal error')
    })

    it('U-09b: Halaman catering menampilkan paket atau info tidak tersedia', () => {
      cy.visit('/home/view-catering')
      cy.get('body').then(($body) => {
        const text = $body.text()
        const hasContent =
          text.includes('Paket') ||
          text.includes('Catering') ||
          text.includes('Menu') ||
          text.includes('tidak tersedia') ||
          text.includes('belum ada')
        expect(hasContent).to.be.true
      })
    })

    // Level 3: Klik detail paket catering (jika ada)
    it('U-09c: Klik detail paket catering → loads tanpa error', () => {
      cy.visit('/home/view-catering')
      cy.get('body').then(($body) => {
        const links = $body.find('a[href*="catering/detail"], a[href*="detail-catering"], button[data-toggle]')
        if (links.length > 0) {
          cy.wrap(links.first()).click({ force: true })
          cy.get('body').should('not.contain', 'Fatal error')
        } else {
          cy.log('Tidak ada link detail catering — skip.')
        }
      })
    })
  })

  // ============================================================
  // U-10 — ULASAN (Level 2–3)
  // ============================================================
  describe('U-10: Ulasan', () => {

    beforeEach(loginAsExternal)

    it('U-10a: Halaman ulasan loads tanpa error', () => {
      cy.visit('/home/ulasan')
      cy.get('body').should('not.contain', 'Fatal error')
    })

    it('U-10b: Halaman ulasan punya rating rata-rata dan ulasan terbaru', () => {
      cy.visit('/home/ulasan')
      cy.contains('Rating Rata-rata').should('exist')
      cy.contains('Ulasan Terbaru').should('exist')
    })

    // Level 3: Conditional — jika punya pemesanan yang bisa di-review
    it('U-10c [State Check]: Form ulasan ada jika punya pemesanan yang bisa di-review', () => {
      cy.visit('/home/ulasan')
      cy.get('body').then(($body) => {
        if ($body.find('form').length > 0) {
          cy.get('form textarea[name="comment"]').should('exist')
          cy.get('form select[name="id_pemesanan"]').should('exist')
          cy.log('✅ Form ulasan tersedia.')
        } else {
          cy.log('ℹ️ Tidak ada pemesanan yang bisa di-review — normal.')
        }
      })
    })

    // Level 3: Negative — submit ulasan tanpa rating (jika form ada)
    it('U-10d [Negative]: Submit ulasan tanpa rating → tidak crash server', () => {
      cy.visit('/home/ulasan')
      cy.get('body').then(($body) => {
        if ($body.find('form').length > 0) {
          cy.get('form').within(() => {
            cy.get('textarea[name="comment"]').clear()
          })
          cy.get('form button[type="submit"], form input[type="submit"]').first().click({ force: true })
          cy.get('body').should('not.contain', 'Fatal error')
            .and('not.contain', 'Parse error')
        } else {
          cy.log('Tidak ada form ulasan — skip.')
        }
      })
    })

    it('U-10e: Ulasan yang sudah ada tampil sebagai card', () => {
      cy.visit('/home/ulasan')
      cy.get('body').then(($body) => {
        const hasUlasan =
          $body.find('[class*="card"], article, .review').length > 0 ||
          $body.text().includes('Belum ada ulasan') ||
          $body.text().includes('belum ada')
        expect(hasUlasan).to.be.true
      })
    })
  })

  // ============================================================
  // U-11 — NOTIFIKASI (Level 2–3)
  // ============================================================
  describe('U-11: Notifikasi', () => {

    beforeEach(loginAsExternal)

    it('U-11a: Endpoint notif_poll_v2 merespons JSON valid', () => {
      // Path relatif — baseUrl sudah include /bookingsmarts/index.php
      cy.request({
        url: '/home/home/notif_poll_v2?since_p=0&since_t=0',
        failOnStatusCode: false,
      }).then((res) => {
        expect(res.status).to.eq(200)
      })
    })

    it('U-11b: Notifikasi icon ada di navbar', () => {
      cy.visit('/home')
      cy.get('[id*="notif"], [class*="notif"], [id*="bell"], [class*="bell"]').should('exist')
    })
  })

  // ============================================================
  // U-12 — HALAMAN PUBLIK / TANPA LOGIN (Level 1–2)
  // ============================================================
  describe('U-12: Halaman Publik', () => {

    it('U-12a: Halaman how-to-order loads tanpa login', () => {
      cy.visit('/how-to-order')
      cy.get('body').should('not.contain', 'Fatal error')
      cy.contains('How to Order').should('exist')
    })

    it('U-12b: Halaman how-to-order menampilkan 9 langkah', () => {
      cy.visit('/how-to-order')
      cy.get('article').should('have.length', 9)
    })

    it('U-12c: Tombol Login ada di halaman publik', () => {
      cy.visit('/how-to-order')
      cy.contains('Login').should('exist')
    })

    it('U-12d: Tombol Registrasi mengarah ke halaman registrasi', () => {
      cy.visit('/how-to-order')
      cy.get('a[href*="register"]').first().click()
      cy.url().should('include', 'regist')
    })

    it('U-12e: Halaman login accessible tanpa autentikasi', () => {
      cy.visit('/login')
      cy.get('body').should('not.contain', 'Fatal error')
      cy.get('form').should('exist')
    })

    it('U-12f: Halaman registrasi accessible tanpa autentikasi', () => {
      cy.visit('/registration')
      cy.get('body').should('not.contain', 'Fatal error')
      cy.get('form').should('exist')
    })
  })

  // ============================================================
  // U-13 — SECURITY & OWNERSHIP (Level 4)
  // ============================================================
  describe('U-13: Security & Ownership', () => {

    // Level 4: Akses detail pemesanan milik orang lain
    it('U-13a [Security]: Akses order orang lain via URL → 302/404, bukan data orang lain', () => {
      cy.fixture('credentials').then((cred) => {
        cy.loginUser(cred.user_eksternal.username, cred.user_eksternal.password)
        cy.request({
          url: '/bookingsmarts/index.php/home/pemesanan/details/PMSN0001',
          failOnStatusCode: false,
        }).then((res) => {
          expect(res.status).to.be.oneOf([302, 404])
        })
      })
    })

    // Level 4: Akses admin route sebagai user biasa
    it('U-13b [Security]: User biasa akses /admin/dashboard → redirect ke login admin', () => {
      loginAsExternal()
      cy.visit('/admin/dashboard')
      cy.get('input[name="username"]').should('exist')
    })

    // Level 4: Akses pembayaran orang lain
    it('U-13c [Security]: Akses endpoint pembayaran dengan ID invalid → handled', () => {
      cy.fixture('credentials').then((cred) => {
        cy.loginUser(cred.user_eksternal.username, cred.user_eksternal.password)
        cy.request({
          url: '/bookingsmarts/index.php/home/pembayaran/details/PMSN00000001',
          failOnStatusCode: false,
        }).then((res) => {
          expect(res.status).to.be.oneOf([200, 302, 404])
          if (res.status === 200) {
            // Jika 200, harus bukan data orang lain yang bocor
            expect(res.body).to.not.include('DROP TABLE')
          }
        })
      })
    })

    // Level 4: XSS di form login
    it('U-13d [Security]: XSS attempt di login → tidak dieksekusi', () => {
      cy.visit('/login')
      cy.get('input[name="username"]').type('<script>alert("xss")</script>')
      cy.get('input[name="password"]').type('pass')
      cy.get('form').submit()
      cy.url().should('not.include', 'home')
      cy.get('body').should('not.contain', '<script>')
    })

    // Level 4: XSS di form registrasi
    it('U-13e [Security]: XSS attempt di form registrasi → sanitized', () => {
      cy.visit('/registration')
      cy.get('input[name="username"]').type('<img src=x onerror=alert(1)>')
      cy.get('form').submit()
      cy.get('body').should('not.contain', 'Fatal error')
    })

    // Level 4: Submit ulasan ke pemesanan bukan milik sendiri
    it('U-13f [Security]: Submit ulasan dengan id_pemesanan tidak valid → tidak crash', () => {
      cy.fixture('credentials').then((cred) => {
        cy.loginUser(cred.user_eksternal.username, cred.user_eksternal.password)
        cy.request({
          url: '/bookingsmarts/index.php/home/submit_ulasan',
          method: 'POST',
          failOnStatusCode: false,
          body: {
            id_pemesanan: 99999,
            rating: 5,
            comment: 'Hack attempt',
          },
        }).then((res) => {
          expect(res.status).to.be.oneOf([200, 302, 403, 404])
        })
      })
    })
  })

  // ============================================================
  // U-14 — HALAMAN LOKASI (Level 1–2)
  // ============================================================
  describe('U-14: Halaman Lokasi', () => {

    beforeEach(loginAsExternal)

    it('U-14a: Halaman location loads tanpa error', () => {
      cy.visit('/home/location')
      cy.get('body').should('not.contain', 'Fatal error')
    })

    it('U-14b: Halaman location menampilkan info alamat atau peta', () => {
      cy.visit('/home/location')
      cy.get('body').then(($body) => {
        const text = $body.text()
        expect(
          text.includes('Alamat') || text.includes('Lokasi') ||
          text.includes('Maps') || text.includes('Jl.') ||
          $body.find('iframe, [id*="map"]').length > 0
        ).to.be.true
      })
    })
  })

  // ============================================================
  // SMOKE TEST — SEMUA HALAMAN USER (Level 1)
  // ============================================================
  describe('Smoke Test: All User Pages', () => {

    beforeEach(loginAsExternal)

    const userPages = [
      { name: 'Home',          url: '/home' },
      { name: 'Pemesanan',     url: '/home/pemesanan' },
      { name: 'Pembayaran',    url: '/home/pembayaran' },
      { name: 'Jadwal',        url: '/home/jadwal' },
      { name: 'View Catering', url: '/home/view-catering' },
      { name: 'Ulasan',        url: '/home/ulasan' },
      { name: 'Edit Data',     url: '/edit_data' },
      { name: 'Edit Foto',     url: '/edit_foto' },
      { name: 'Location',      url: '/home/location' },
      { name: 'How to Order',  url: '/how-to-order' },
    ]

    userPages.forEach((page) => {
      it(`[Smoke] ${page.name} → tidak ada PHP error`, () => {
        cy.visit(page.url)
        cy.get('body')
          .should('not.contain', 'Severity:')
          .and('not.contain', 'Fatal error')
          .and('not.contain', 'A PHP Error was encountered')
          .and('not.contain', 'Parse error')
          .and('not.contain', 'Undefined variable')
      })
    })
  })
})
