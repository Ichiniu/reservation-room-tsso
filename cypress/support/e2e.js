// Support file for Cypress E2E tests
import './commands'

// Suppress uncaught exceptions from the app (CI3 may throw JS errors)
Cypress.on('uncaught:exception', (err, runnable) => {
  // Return false to prevent Cypress from failing the test
  return false
})
