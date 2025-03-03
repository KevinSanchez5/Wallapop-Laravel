describe('Backup', () => {
    it('Debe exportar backup correctamente', () => {
        cy.visit('http://localhost/login');
        cy.get('input[name="email"]').type('admin@example.com');
        cy.get('input[name="password"]').type('adminPassword123?');
        cy.get('button[type="submit"]').click();

        cy.url().should('include', '/admin/dashboard');
        cy.get('a[id="backupMenuButton"]').click();
        cy.get('a[id="exportBackupButton"]').click();
    })

    it('Debe importar backup correctamente', () => {
        cy.visit('http://localhost/login');
        cy.get('input[name="email"]').type('admin@example.com');
        cy.get('input[name="password"]').type('adminPassword123?');
        cy.get('button[type="submit"]').click();

        cy.url().should('include', '/admin/dashboard');
        cy.get('a[id="backupMenuButton"]').click();
        cy.get('a[id="importBackupButton"]').click();
        cy.get('div[id="backupModal"]', { timeout: 10000 }).should('be.visible');
    })
});
