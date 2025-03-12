describe('Usuario ya tiene cuenta', () => {
    beforeEach(() => {
        cy.visit('/login');
    });

    it('Debe ver perfil correctamente', () => {
        cy.get('input[name="email"]').type('juan@example.com');
        cy.get('input[name="password"]').type('Password123?');
        cy.get('input[name="remember"]').check();
        cy.get('button[type="submit"]').click();

        cy.url().should('include', '/');
        cy.get(".container").contains("Juan Pérez").should("be.visible").click();
        cy.contains("Perfil").click();

        cy.url().should('include', '/profile');
    })

    it('Debe ver producto subido correctamente', () => {
        cy.get('input[name="email"]').type('juan@example.com');
        cy.get('input[name="password"]').type('Password123?');
        cy.get('input[name="remember"]').check();
        cy.get('button[type="submit"]').click();

        cy.url().should('include', '/');
        cy.get(".container").contains("Juan Pérez").should("be.visible").click();
        cy.contains("Perfil").click();

        cy.url().should('include', '/profile');
        cy.contains('Ver más').first().click();

        cy.url().should('include', '/producto');
        cy.contains('Producto de Prueba');
    })
});
