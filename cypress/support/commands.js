// ***********************************************
// Custom commands untuk Booking Smart Office
// ***********************************************

/**
 * Login sebagai user biasa
 * @param {string} username
 * @param {string} password
 */
Cypress.Commands.add('loginUser', (username, password) => {
  cy.visit('/login')
  cy.get('input[name="username"]').clear().type(username)
  cy.get('input[name="password"]').clear().type(password)
  cy.get('form').submit()
  cy.url().should('include', 'home')
})

/**
 * Login sebagai admin
 * @param {string} username
 * @param {string} password
 */
Cypress.Commands.add('loginAdmin', (username, password) => {
  cy.visit('/admin/login')
  cy.get('input[name="username"]').clear().type(username)
  cy.get('input[name="password"]').clear().type(password)
  cy.get('form').submit()
  cy.url().should('include', 'admin')
})

/**
 * Logout user
 */
Cypress.Commands.add('logoutUser', () => {
  cy.visit('/home/home/logout')
})
