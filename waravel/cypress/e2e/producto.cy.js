describe('Tests de producto', () => {
    it('Debería editar un producto', () => {
        cy.visit('http://localhost/login');
        cy.get('input[name="email"]').type('maria@example.com');
        cy.get('input[name="password"]').type('Password123?');
        cy.get('button[type="submit"]').click();

        cy.url().should('include', '/');
        cy.contains('María García').click();
        cy.contains('Perfil').click();

        cy.url().should('include', '/profile');
        cy.get('a[href="http://localhost/producto/Z8K3VLYTQ72/edit"]').click();

        cy.url().should('include', '/producto/Z8K3VLYTQ72/edit');
        cy.get('input[name="nombre"]').type('Chaqueta de Cuero Actualizada');
        cy.get('button[type="button"]').contains('Actualizar Producto').click();
        cy.get('#toast-confirm').contains('Sí').click();
    });

    it('Debería desactivar un producto', () => {
        cy.visit('http://localhost/login');
        cy.get('input[name="email"]').type('maria@example.com');
        cy.get('input[name="password"]').type('Password123?');
        cy.get('button[type="submit"]').click();

        cy.url().should('include', '/');
        cy.contains('María García').click();
        cy.contains('Perfil').click();

        cy.url().should('include', '/profile');
        cy.get('#deactivateForm').click();
        cy.get('#toast-confirm-deactivate').contains('Sí').click();
    });

    it('Debería eliminar un producto', () => {
        cy.visit('http://localhost/login');
        cy.get('input[name="email"]').type('maria@example.com');
        cy.get('input[name="password"]').type('Password123?');
        cy.get('button[type="submit"]').click();

        cy.url().should('include', '/');
        cy.contains('María García').click();
        cy.contains('Perfil').click();

        cy.url().should('include', '/profile');
        cy.get('#deleteForm').click();
        cy.get('#toast-confirm-delete').contains('Sí').click();
    });
});
