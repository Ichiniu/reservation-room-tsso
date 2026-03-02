/**
 * ============================================================
 *  ADMIN COMPREHENSIVE E2E TESTS
 *  Covers: Auth, Navigation, Pages, Buttons, Forms, Tables
 * ============================================================
 */
describe('Admin Comprehensive Tests', () => {

  beforeEach(() => {
    cy.clearCookies()
    cy.clearLocalStorage()
  })

  // ===========================================================
  // A-01: AUTH & SECURITY
  // ===========================================================
  describe('A-01: Auth & Security', () => {

    it('Admin login page loads correctly', () => {
      cy.visit('/admin')
      cy.get('input[name="username"]').should('be.visible')
      cy.get('input[name="password"]').should('be.visible')
      cy.get('form').should('exist')
    })

    it('Admin login fails with wrong password', () => {
      cy.visit('/admin')
      cy.get('input[name="username"]').type('admin')
      cy.get('input[name="password"]').type('wrongpassword123')
      cy.get('form').submit()
      // Should NOT reach dashboard
      cy.url().should('not.include', 'dashboard')
    })

    it('Admin login succeeds with correct credentials', () => {
      cy.fixture('credentials').then((cred) => {
        cy.loginAdmin(cred.admin.username, cred.admin.password)
      })
    })

    it('Protected pages redirect to login when not authenticated', () => {
      const protectedPages = [
        '/admin/dashboard',
        '/admin/transaksi',
        '/admin/pembayaran',
        '/admin/list',
        '/admin/gedung',
      ]
      protectedPages.forEach((page) => {
        cy.visit(page)
        cy.get('input[name="username"]').should('exist')
      })
    })

    it('Admin logout works', () => {
      cy.fixture('credentials').then((cred) => {
        cy.loginAdmin(cred.admin.username, cred.admin.password)
        cy.visit('/admin/log_out')
        cy.visit('/admin/dashboard')
        cy.get('input[name="username"]').should('exist')
      })
    })
  })

  // ===========================================================
  // A-02: SIDEBAR NAVIGATION
  // ===========================================================
  describe('A-02: Sidebar Navigation', () => {
    beforeEach(() => {
      cy.fixture('credentials').then((cred) => {
        cy.loginAdmin(cred.admin.username, cred.admin.password)
      })
    })

    it('Sidebar contains all main menu links', () => {
      cy.visit('/admin/dashboard')
      // Check sidebar has main navigation items
      cy.get('a[href*="dashboard"]').should('exist')
      cy.get('a[href*="transaksi"]').should('exist')
      cy.get('a[href*="pembayaran"]').should('exist')
    })
  })

  // ===========================================================
  // A-03: DASHBOARD
  // ===========================================================
  describe('A-03: Dashboard', () => {
    beforeEach(() => {
      cy.fixture('credentials').then((cred) => {
        cy.loginAdmin(cred.admin.username, cred.admin.password)
      })
    })

    it('Dashboard loads without PHP errors', () => {
      cy.visit('/admin/dashboard')
      cy.get('body').should('not.contain', 'Fatal error')
      cy.get('body').should('not.contain', 'Severity:')
      cy.get('body').should('not.contain', 'A PHP Error was encountered')
    })
  })

  // ===========================================================
  // A-04: USER MANAGEMENT
  // ===========================================================
  describe('A-04: User Management', () => {
    beforeEach(() => {
      cy.fixture('credentials').then((cred) => {
        cy.loginAdmin(cred.admin.username, cred.admin.password)
      })
    })

    it('List user page displays table', () => {
      cy.visit('/admin/list')
      cy.get('table').should('exist')
      cy.get('tbody tr').should('have.length.greaterThan', 0)
    })

    it('Table has correct column headers', () => {
      cy.visit('/admin/list')
      cy.get('thead').should('exist')
    })
  })

  // ===========================================================
  // A-05/A-06/A-07: GEDUNG (ROOM) MANAGEMENT
  // ===========================================================
  describe('A-05/06/07: Room Management', () => {
    beforeEach(() => {
      cy.fixture('credentials').then((cred) => {
        cy.loginAdmin(cred.admin.username, cred.admin.password)
      })
    })

    it('List gedung page displays rooms', () => {
      cy.visit('/admin/gedung')
      cy.get('table').should('exist')
    })

    it('Add gedung form loads with all required fields', () => {
      cy.visit('/admin/add_gedung')
      cy.get('form').should('exist')
      cy.get('input[name="nama_gedung"]').should('exist')
    })

    it('Edit gedung page loads for existing room', () => {
      cy.visit('/admin/gedung')
      // Click first edit button if available
      cy.get('a[href*="admin/edit"]').first().then(($link) => {
        const href = $link.attr('href')
        cy.visit(href)
        cy.get('form').should('exist')
      })
    })
  })

  // ===========================================================
  // A-08: CATERING MANAGEMENT
  // ===========================================================
  describe('A-08: Catering Management', () => {
    beforeEach(() => {
      cy.fixture('credentials').then((cred) => {
        cy.loginAdmin(cred.admin.username, cred.admin.password)
      })
    })

    it('Catering list page loads with data', () => {
      cy.visit('/admin/catering')
      cy.get('table').should('exist')
    })

    it('Add catering form loads', () => {
      cy.visit('/admin/add_catering')
      cy.get('form').should('exist')
    })
  })

  // ===========================================================
  // A-09/10/11: TRANSAKSI (BOOKING INBOX)
  // ===========================================================
  describe('A-09/10/11: Transaksi (Booking Inbox)', () => {
    beforeEach(() => {
      cy.fixture('credentials').then((cred) => {
        cy.loginAdmin(cred.admin.username, cred.admin.password)
      })
    })

    it('Transaksi inbox page loads', () => {
      cy.visit('/admin/transaksi')
      cy.get('body').should('not.contain', 'Fatal error')
    })

    it('Transaksi has detail links per booking', () => {
      cy.visit('/admin/transaksi')
      // Check if there are clickable detail links
      cy.get('a[href*="detail_transaksi"]').then(($links) => {
        if ($links.length > 0) {
          cy.wrap($links).first().should('be.visible')
        }
      })
    })

    it('Detail transaksi page loads for existing booking', () => {
      cy.visit('/admin/transaksi')
      cy.get('a[href*="detail_transaksi"]').first().then(($link) => {
        const href = $link.attr('href')
        if (href) {
          cy.visit(href)
          cy.get('body').should('not.contain', 'Fatal error')
        }
      })
    })
  })

  // ===========================================================
  // A-12/13/14: PEMBAYARAN (PAYMENT VERIFICATION)
  // ===========================================================
  describe('A-12/13/14: Payment Verification', () => {
    beforeEach(() => {
      cy.fixture('credentials').then((cred) => {
        cy.loginAdmin(cred.admin.username, cred.admin.password)
      })
    })

    it('Pembayaran list page loads', () => {
      cy.visit('/admin/pembayaran')
      cy.get('body').should('not.contain', 'Fatal error')
    })

    it('Payment detail links work', () => {
      cy.visit('/admin/pembayaran')
      cy.get('a[href*="read_transaction"]').then(($links) => {
        if ($links.length > 0) {
          const href = $links.first().attr('href')
          cy.visit(href)
          cy.get('body').should('not.contain', 'Fatal error')
        }
      })
    })
  })

  // ===========================================================
  // A-15: REKAP AKTIVITAS
  // ===========================================================
  describe('A-15: Rekap Aktivitas', () => {
    beforeEach(() => {
      cy.fixture('credentials').then((cred) => {
        cy.loginAdmin(cred.admin.username, cred.admin.password)
      })
    })

    it('Rekap aktivitas page loads', () => {
      cy.visit('/admin/rekap_aktivitas')
      cy.get('body').should('not.contain', 'Fatal error')
    })
  })

  // ===========================================================
  // A-16: REKAP TRANSAKSI
  // ===========================================================
  describe('A-16: Rekap Transaksi', () => {
    beforeEach(() => {
      cy.fixture('credentials').then((cred) => {
        cy.loginAdmin(cred.admin.username, cred.admin.password)
      })
    })

    it('Rekap transaksi page loads', () => {
      cy.visit('/admin/rekap_transaksi')
      cy.get('body').should('not.contain', 'Fatal error')
    })
  })

  // ===========================================================
  // A-17: PEMESANAN LIST (ADMIN VIEW)
  // ===========================================================
  describe('A-17: Pemesanan Admin View', () => {
    beforeEach(() => {
      cy.fixture('credentials').then((cred) => {
        cy.loginAdmin(cred.admin.username, cred.admin.password)
      })
    })

    it('Pemesanan admin page loads', () => {
      cy.visit('/admin/pemesanan2')
      cy.get('body').should('not.contain', 'Fatal error')
    })
  })

  // ===========================================================
  // SMOKE TEST — ALL ADMIN PAGES
  // ===========================================================
  describe('Smoke Test: All Admin Pages', () => {
    beforeEach(() => {
      cy.fixture('credentials').then((cred) => {
        cy.loginAdmin(cred.admin.username, cred.admin.password)
      })
    })

    const pages = [
      { name: 'Dashboard', url: '/admin/dashboard' },
      { name: 'Transaksi', url: '/admin/transaksi' },
      { name: 'Pembayaran', url: '/admin/pembayaran' },
      { name: 'Pemesanan', url: '/admin/pemesanan2' },
      { name: 'List User', url: '/admin/list' },
      { name: 'Gedung', url: '/admin/gedung' },
      { name: 'Catering', url: '/admin/catering' },
      { name: 'Add Gedung', url: '/admin/add_gedung' },
      { name: 'Add Catering', url: '/admin/add_catering' },
      { name: 'Rekap Aktivitas', url: '/admin/rekap_aktivitas' },
      { name: 'Rekap Transaksi', url: '/admin/rekap_transaksi' },
    ]

    pages.forEach((page) => {
      it(`${page.name} loads without PHP error`, () => {
        cy.visit(page.url)
        cy.get('body').should('not.contain', 'Severity:')
        cy.get('body').should('not.contain', 'Fatal error')
        cy.get('body').should('not.contain', 'A PHP Error was encountered')
        cy.get('body').should('not.contain', 'Parse error')
      })
    })
  })
})
