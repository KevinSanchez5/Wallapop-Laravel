describe('Iniciar Sesión', () => {
    beforeEach(() => {
        cy.visit('http://localhost/login');
    });

    it('Debe cargar correctamente los elementos principales', () => {
        cy.title().should('include', 'Iniciar Sesión');
        cy.get('input[name="email"]').should('be.visible');
        cy.get('input[name="password"]').should('be.visible');
        cy.get('input[name="remember"]').should('be.visible');
        cy.get('button[type="submit"]').should('be.visible');
    });

    it('Debe iniciar sesión correctamente', () => {
        cy.get('input[name="email"]').type('juan@example.com');
        cy.get('input[name="password"]').type('Password123?');
        cy.get('input[name="remember"]').check();
        cy.get('button[type="submit"]').click();

        cy.url().should('include', '/');
        cy.get(".container").contains("Juan Pérez").should("be.visible");
    })

    it("Debe mostrar error si las credenciales son incorrectas", () => {
        cy.get("input[name='email']").type("juan@example.com");
        cy.get("input[name='password']").type("incorrecto");
        cy.get("button[type='submit']").click();

        cy.contains("auth.failed").should("be.visible");
    });


    it("Debe permitir cerrar sesión", () => {
        cy.get('input[name="email"]').type('juan@example.com');
        cy.get('input[name="password"]').type('Password123?');
        cy.get('input[name="remember"]').check();
        cy.get('button[type="submit"]').click();

        cy.url().should('include', '/');
        cy.get(".container").contains("Juan Pérez").should("be.visible").click();
        cy.contains("Cerrar sesión").click();

        cy.get('a[href="http://localhost/login"]').should("be.visible");
    });

});
