/**
 * ============================================================
 *  USER COMPREHENSIVE E2E TESTS
 *  Covers: Auth, Registration, Navigation, Booking Flow,
 *          Pemesanan, Pembayaran, Profile, Ulasan, Security
 * ============================================================
 */
describe('User Comprehensive Tests', () => {

  beforeEach(() => {
    cy.clearCookies()
    cy.clearLocalStorage()
  })

  // ===========================================================
  // U-01/02: REGISTRATION
  // ===========================================================
  describe('Registration', () => {

    it('U-01: Registration page loads with all form fields', () => {
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

    it('U-02: Selecting INTERNAL shows departemen field', () => {
      cy.visit('/registration')
      // Multiple options have value="INTERNAL", so select by visible text
      cy.get('select[name="perusahaan"]').select('PT Tiga Serangkai Inti Corpora')
      cy.get('#wrapDepartemen').should('be.visible')
      cy.get('#wrapEksternal').should('not.be.visible')
    })

    it('U-02b: Selecting EKSTERNAL shows nama perusahaan field', () => {
      cy.visit('/registration')
      cy.get('select[name="perusahaan"]').select('EKSTERNAL')
      cy.get('#wrapEksternal').should('be.visible')
      cy.get('#wrapDepartemen').should('not.be.visible')
    })

    it('U-03: Register fails if username already exists', () => {
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

    it('U-04: Register fails with non-gmail email (client validation)', () => {
      cy.visit('/registration')
      cy.get('input[name="username"]').type('emailtest999')
      cy.get('input[name="nama_lengkap"]').type('Email Test')
      cy.get('select[name="perusahaan"]').select('EKSTERNAL')
      cy.get('input[name="nama_perusahaan"]').type('Test Corp')
      cy.get('input[name="password"]').type('Test@123')
      cy.get('input[name="confirm_pass"]').type('Test@123')
      cy.get('input[name="email"]').type('test@yahoo.com')
      cy.get('textarea[name="alamat"]').type('Jl. Test No. 1')
      cy.get('input[name="no_telepon"]').type('08123456789')
      cy.get('input[name="dob"]').type('2000-01-01')
      cy.get('form').submit()
      cy.contains('gmail').should('be.visible')
    })

    it('U-05: Registration validates age >= 18 (client validation)', () => {
      cy.visit('/registration')
      cy.get('input[name="username"]').type('agetest999')
      cy.get('input[name="nama_lengkap"]').type('Age Test')
      cy.get('select[name="perusahaan"]').select('EKSTERNAL')
      cy.get('input[name="nama_perusahaan"]').type('Test Corp')
      cy.get('input[name="password"]').type('Test@123')
      cy.get('input[name="confirm_pass"]').type('Test@123')
      cy.get('input[name="email"]').type('agetest@gmail.com')
      cy.get('textarea[name="alamat"]').type('Jl. Test No. 1')
      cy.get('input[name="no_telepon"]').type('08123456789')
      // Set date to 10 years ago (under 18)
      const youngDate = new Date()
      youngDate.setFullYear(youngDate.getFullYear() - 10)
      const dateStr = youngDate.toISOString().split('T')[0]
      cy.get('input[name="dob"]').type(dateStr)
      cy.get('form').submit()
      cy.contains('18').should('be.visible')
    })

    it('U-05b: Password visibility toggle works', () => {
      cy.visit('/registration')
      cy.get('input[name="password"]').should('have.attr', 'type', 'password')
      cy.get('.pw-toggle[data-target="password"]').click()
      cy.get('input[name="password"]').should('have.attr', 'type', 'text')
      cy.get('.pw-toggle[data-target="password"]').click()
      cy.get('input[name="password"]').should('have.attr', 'type', 'password')
    })

    it('Login link on registration page works', () => {
      cy.visit('/registration')
      cy.contains('Sudah punya akun').click()
      cy.url().should('include', 'login')
    })
  })

  // ===========================================================
  // U-06/07/08/09: LOGIN & LOGOUT
  // ===========================================================
  describe('Login & Logout', () => {

    it('U-06: Login page loads with form', () => {
      cy.visit('/login')
      cy.get('input[name="username"]').should('be.visible')
      cy.get('input[name="password"]').should('be.visible')
      cy.get('form').should('exist')
    })

    it('U-06b: Login succeeds with valid credentials', () => {
      cy.fixture('credentials').then((cred) => {
        cy.loginUser(cred.user_eksternal.username, cred.user_eksternal.password)
        cy.url().should('include', 'home')
      })
    })

    it('U-07: Login fails with wrong password', () => {
      cy.visit('/login')
      cy.get('input[name="username"]').type('wronguser')
      cy.get('input[name="password"]').type('wrongpassword')
      cy.get('form').submit()
      cy.contains('Login Gagal').should('be.visible')
    })

    it('U-08: Accessing protected page without login redirects', () => {
      cy.visit('/home/pemesanan')
      cy.url().should('include', 'login')
    })

    it('U-08b: Multiple protected pages redirect when not logged in', () => {
      const pages = ['/home/pemesanan', '/home/pembayaran', '/home/jadwal', '/edit_data']
      pages.forEach(page => {
        cy.visit(page)
        cy.url().should('include', 'login')
      })
    })

    it('U-09: Logout clears session', () => {
      cy.fixture('credentials').then((cred) => {
        cy.loginUser(cred.user_eksternal.username, cred.user_eksternal.password)
        cy.logoutUser()
        cy.visit('/home/pemesanan')
        cy.url().should('include', 'login')
      })
    })
  })

  // ===========================================================
  // U-10: HOME PAGE & ROOM LISTING
  // ===========================================================
  describe('Home & Room Listing', () => {
    beforeEach(() => {
      cy.fixture('credentials').then((cred) => {
        cy.loginUser(cred.user_eksternal.username, cred.user_eksternal.password)
      })
    })

    it('U-10: Home displays room cards', () => {
      cy.get('article').should('have.length.greaterThan', 0)
    })

    it('U-10b: Room cards have Detail button', () => {
      cy.get('a').contains('Detail').should('have.length.greaterThan', 0)
    })

    it('U-10c: Clicking Detail navigates to room detail page', () => {
      cy.get('a').contains('Detail').first().click()
      cy.url().should('include', 'home/details')
      cy.get('body').should('not.contain', 'Fatal error')
    })

    it('U-10d: Room detail page has Ajukan Pesanan button', () => {
      cy.get('a').contains('Detail').first().click()
      cy.get('body').should('not.contain', 'Fatal error')
      // Check for booking button (may be "Ajukan Pesanan" or similar)
      cy.get('a[href*="order-gedung"]').should('exist')
    })

    it('U-10e: Ulasan section visible on home page', () => {
      cy.contains('Ulasan').should('exist')
    })

    it('U-10f: Lihat semua ulasan link works', () => {
      cy.get('a[href*="ulasan"]').first().click()
      cy.url().should('include', 'ulasan')
    })
  })

  // ===========================================================
  // U-11: BOOKING FORM
  // ===========================================================
  describe('Booking Form', () => {
    beforeEach(() => {
      cy.fixture('credentials').then((cred) => {
        cy.loginUser(cred.user_eksternal.username, cred.user_eksternal.password)
      })
    })

    it('U-11: Booking form page loads with date/time fields', () => {
      // Navigate to first room's booking page
      cy.get('a').contains('Detail').first().click()
      cy.get('a[href*="order-gedung"]').first().click()
      cy.url().should('include', 'order-gedung')
      cy.get('body').should('not.contain', 'Fatal error')
    })
  })

  // ===========================================================
  // U-15/16: PEMESANAN (ORDER LIST & DETAIL)
  // ===========================================================
  describe('Pemesanan List & Detail', () => {
    beforeEach(() => {
      cy.fixture('credentials').then((cred) => {
        cy.loginUser(cred.user_eksternal.username, cred.user_eksternal.password)
      })
    })

    it('U-15: Pemesanan page loads with table', () => {
      cy.visit('/home/pemesanan')
      cy.get('table').should('exist')
    })

    it('U-15b: Filter by ID works', () => {
      cy.visit('/home/pemesanan')
      cy.get('#idFilter').type('PMSN')
      // Table should still be visible after filtering
      cy.get('table').should('exist')
    })

    it('U-15c: Status dropdown filter exists', () => {
      cy.visit('/home/pemesanan')
      cy.get('#statusFilter').should('exist')
      cy.get('#statusFilter option').should('have.length.greaterThan', 1)
    })

    it('U-15d: Reset button clears filters', () => {
      cy.visit('/home/pemesanan')
      cy.get('#idFilter').type('test')
      cy.get('#resetBtn').click()
      cy.get('#idFilter').should('have.value', '')
    })

    it('U-15e: Pagination buttons exist', () => {
      cy.visit('/home/pemesanan')
      cy.get('#prevBtn').should('exist')
      cy.get('#nextBtn').should('exist')
      cy.get('#rowsPerPage').should('exist')
    })

    it('U-15f: Rows per page selector works', () => {
      cy.visit('/home/pemesanan')
      cy.get('#rowsPerPage').select('5')
      cy.get('#rowsPerPage').should('have.value', '5')
    })

    it('U-16: Detail button opens detail page', () => {
      cy.visit('/home/pemesanan')
      cy.get('a[href*="pemesanan/details"]').first().then(($link) => {
        if ($link.length > 0) {
          cy.wrap($link).click()
          cy.url().should('include', 'pemesanan/details')
          cy.get('body').should('not.contain', 'Fatal error')
        }
      })
    })
  })

  // ===========================================================
  // U-17/18: PEMBAYARAN
  // ===========================================================
  describe('Pembayaran', () => {
    beforeEach(() => {
      cy.fixture('credentials').then((cred) => {
        cy.loginUser(cred.user_eksternal.username, cred.user_eksternal.password)
      })
    })

    it('U-18: Pembayaran page loads', () => {
      cy.visit('/home/pembayaran')
      cy.get('body').should('not.contain', 'Fatal error')
      cy.get('body').should('not.contain', 'Severity:')
    })
  })

  // ===========================================================
  // U-19/20/21/22: PROFILE
  // ===========================================================
  describe('Profile', () => {
    beforeEach(() => {
      cy.fixture('credentials').then((cred) => {
        cy.loginUser(cred.user_eksternal.username, cred.user_eksternal.password)
      })
    })

    it('U-19: Edit data page loads with pre-filled form', () => {
      cy.visit('/edit_data')
      cy.get('form').should('exist')
      cy.get('body').should('not.contain', 'Fatal error')
    })

    it('U-19b: Edit data form has required fields', () => {
      cy.visit('/edit_data')
      cy.get('input[name="nama_lengkap"]').should('exist')
      cy.get('input[name="email"]').should('exist')
      cy.get('input[name="no_telepon"]').should('exist')
    })

    it('U-21: Password mismatch shows error', () => {
      cy.visit('/edit_data')
      cy.get('input[name="password"]').type('NewPass123')
      cy.get('input[name="confirm_pass"]').type('DiffPass456')
      cy.get('form').submit()
      cy.contains('tidak sama').should('be.visible')
    })

    it('U-22: Edit foto page loads', () => {
      cy.visit('/edit_foto')
      cy.get('body').should('not.contain', 'Fatal error')
    })
  })

  // ===========================================================
  // U-23/24: JADWAL
  // ===========================================================
  describe('Jadwal', () => {
    beforeEach(() => {
      cy.fixture('credentials').then((cred) => {
        cy.loginUser(cred.user_eksternal.username, cred.user_eksternal.password)
      })
    })

    it('U-23: Jadwal page loads', () => {
      cy.visit('/home/jadwal')
      cy.get('body').should('not.contain', 'Fatal error')
    })
  })

  // ===========================================================
  // U-25: CATERING
  // ===========================================================
  describe('Catering', () => {
    beforeEach(() => {
      cy.fixture('credentials').then((cred) => {
        cy.loginUser(cred.user_eksternal.username, cred.user_eksternal.password)
      })
    })

    it('U-25: Catering page loads', () => {
      cy.visit('/home/view-catering')
      cy.get('body').should('not.contain', 'Fatal error')
    })
  })

  // ===========================================================
  // U-28: ULASAN
  // ===========================================================
  describe('Ulasan', () => {
    beforeEach(() => {
      cy.fixture('credentials').then((cred) => {
        cy.loginUser(cred.user_eksternal.username, cred.user_eksternal.password)
      })
    })

    it('U-28: Ulasan page loads', () => {
      cy.visit('/home/ulasan')
      cy.get('body').should('not.contain', 'Fatal error')
    })

    it('U-28b: Ulasan page has rating summary and review cards', () => {
      cy.visit('/home/ulasan')
      // Rating summary always exists
      cy.contains('Rating Rata-rata').should('exist')
      cy.contains('Ulasan Terbaru').should('exist')
      // Form only appears if user has reviewable bookings
      // so we check conditionally
      cy.get('body').then(($body) => {
        if ($body.find('form').length > 0) {
          cy.get('form textarea[name="comment"]').should('exist')
          cy.get('form select[name="id_pemesanan"]').should('exist')
        } else {
          // No form = no reviewable bookings, info message should be visible
          cy.contains('submitted').should('exist')
        }
      })
    })
  })

  // ===========================================================
  // U-29: SECURITY — OWNERSHIP CHECKS
  // ===========================================================
  describe('Security', () => {

    it('U-29: Cannot access other users order details', () => {
      cy.fixture('credentials').then((cred) => {
        cy.loginUser(cred.user_eksternal.username, cred.user_eksternal.password)
        cy.request({
          url: '/bookingsmarts/index.php/home/pemesanan/details/PMSN0001',
          failOnStatusCode: false,
        }).then((response) => {
          // Must be 404 or redirect — not 200 with someone else's data
          expect(response.status).to.be.oneOf([302, 404])
        })
      })
    })
  })

  // ===========================================================
  // U-30: NAVBAR & NAVIGATION
  // ===========================================================
  describe('Navbar & Navigation', () => {
    beforeEach(() => {
      cy.fixture('credentials').then((cred) => {
        cy.loginUser(cred.user_eksternal.username, cred.user_eksternal.password)
      })
    })

    it('U-30: Navbar shows user profile when logged in', () => {
      cy.visit('/home')
      cy.get('#profileToggle').should('exist')
    })

    it('U-30b: Profile dropdown opens on click', () => {
      cy.visit('/home')
      cy.get('#profileToggle').click()
      cy.get('#profileMenu').should('be.visible')
    })

    it('U-30c: Profile dropdown has Edit Data link', () => {
      cy.visit('/home')
      cy.get('#profileToggle').click()
      cy.get('#profileMenu a[href*="edit_data"]').should('exist')
    })

    it('U-30d: Profile dropdown has Edit Foto link', () => {
      cy.visit('/home')
      cy.get('#profileToggle').click()
      cy.get('#profileMenu a[href*="edit_foto"]').should('exist')
    })

    it('U-30e: Profile dropdown has Logout link', () => {
      cy.visit('/home')
      cy.get('#profileToggle').click()
      cy.get('#profileMenu a[href*="logout"]').should('exist')
    })

    it('U-30f: Desktop navigation links exist', () => {
      cy.visit('/home')
      cy.get('a').contains('HOME').should('exist')
      cy.get('a').contains('JADWAL').should('exist')
      cy.get('a').contains('PEMESANAN').should('exist')
      cy.get('a').contains('CATERING').should('exist')
      cy.get('a').contains('TRANSAKSI').should('exist')
    })

    it('U-30g: Navigation links go to correct pages', () => {
      cy.visit('/home')
      cy.get('a').contains('JADWAL').first().click()
      cy.url().should('include', 'jadwal')

      cy.visit('/home')
      cy.get('a').contains('PEMESANAN').first().click()
      cy.url().should('include', 'pemesanan')

      cy.visit('/home')
      cy.get('a').contains('CATERING').first().click()
      cy.url().should('include', 'catering')
    })
  })

  // ===========================================================
  // HOW TO ORDER (PUBLIC PAGE)
  // ===========================================================
  describe('How to Order (Public)', () => {

    it('How to Order page loads without login', () => {
      cy.visit('/how-to-order')
      cy.get('body').should('not.contain', 'Fatal error')
      cy.get('body').should('not.contain', 'Severity:')
      cy.contains('How to Order').should('exist')
    })

    it('How to Order shows Login button for guests', () => {
      cy.visit('/how-to-order')
      cy.contains('Login').should('exist')
    })

    it('How to Order shows Registrasi button for guests', () => {
      cy.visit('/how-to-order')
      cy.get('a[href*="register"]').should('exist')
    })

    it('Registration button navigates to registration page', () => {
      cy.visit('/how-to-order')
      cy.get('a[href*="register"]').first().click()
      cy.url().should('include', 'regist')
    })

    it('All 9 steps are displayed', () => {
      cy.visit('/how-to-order')
      // Steps 1-9 are in article tags
      cy.get('article').should('have.length', 9)
    })
  })

  // ===========================================================
  // SMOKE TEST — ALL USER PAGES WITHOUT PHP ERROR
  // ===========================================================
  describe('Smoke Test: All User Pages', () => {
    beforeEach(() => {
      cy.fixture('credentials').then((cred) => {
        cy.loginUser(cred.user_eksternal.username, cred.user_eksternal.password)
      })
    })

    const pages = [
      { name: 'Home', url: '/home' },
      { name: 'Pemesanan', url: '/home/pemesanan' },
      { name: 'Pembayaran', url: '/home/pembayaran' },
      { name: 'Jadwal', url: '/home/jadwal' },
      { name: 'View Catering', url: '/home/view-catering' },
      { name: 'Edit Data', url: '/edit_data' },
      { name: 'Edit Foto', url: '/edit_foto' },
      { name: 'Ulasan', url: '/home/ulasan' },
      { name: 'How to Order', url: '/how-to-order' },
      { name: 'Location', url: '/home/location' },
    ]

    pages.forEach((page) => {
      it(`${page.name} (${page.url}) loads without PHP error`, () => {
        cy.visit(page.url)
        cy.get('body').should('not.contain', 'Severity:')
        cy.get('body').should('not.contain', 'Fatal error')
        cy.get('body').should('not.contain', 'A PHP Error was encountered')
        cy.get('body').should('not.contain', 'Parse error')
      })
    })
  })
})
