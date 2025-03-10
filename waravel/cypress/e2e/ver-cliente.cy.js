describe('Perfil del Cliente', () => {
    it('Debería entrar al perfil', () => {
        cy.visit('http://localhost');
        cy.get('a[href="http://localhost/login"]').click();
        cy.url().should('include', '/login');
        cy.wait(1000);
        cy.contains('Iniciar Sesión').should('be.visible');
        cy.get('input[name="email"]').type('juan@example.com');
        cy.get('input[name="password"]').type('Password123?');
        cy.get('button[type="submit"]').click();

        cy.url().should('include', '/');

        cy.contains('Juan Pérez', { timeout: 10000 }).should('be.visible').click();
        cy.contains('Perfil').click();
    });

    it('Debería mostrar la información del cliente', () => {
        cy.visit('http://localhost/login');
        cy.get('input[name="email"]').type('juan@example.com');
        cy.get('input[name="password"]').type('Password123?');
        cy.get('button[type="submit"]').click();

        cy.url().should('include', '/');

        cy.contains('Juan Pérez', { timeout: 10000 }).should('be.visible').click();
        cy.contains('Perfil').click();

        cy.get('h2.text-2xl').should('be.visible');
        cy.get('p').contains('📞').should('be.visible');
    });

    it('Debería mostrar la imagen del cliente', () => {
        cy.visit('http://localhost/login');
        cy.get('input[name="email"]').type('juan@example.com');
        cy.get('input[name="password"]').type('Password123?');
        cy.get('button[type="submit"]').click();

        cy.url().should('include', '/');

        cy.contains('Juan Pérez', { timeout: 10000 }).should('be.visible').click();
        cy.contains('Perfil').click();

        cy.get('img').should('have.attr', 'src').and('include', 'storage');
    });

    it('Debería mostrar la calificación promedio con estrellas', () => {
        cy.visit('http://localhost/login');
        cy.get('input[name="email"]').type('juan@example.com');
        cy.get('input[name="password"]').type('Password123?');
        cy.get('button[type="submit"]').click();

        cy.url().should('include', '/');

        cy.contains('Juan Pérez', { timeout: 10000 }).should('be.visible').click();
        cy.contains('Perfil').click();

        cy.get('svg.text-yellow-500').should('exist');
    });

    it('Debería cambiar entre secciones Productos y Valoraciones', () => {
        cy.visit('http://localhost/login');
        cy.get('input[name="email"]').type('juan@example.com');
        cy.get('input[name="password"]').type('Password123?');
        cy.get('button[type="submit"]').click();

        cy.url().should('include', '/');

        cy.contains('Juan Pérez', { timeout: 10000 }).should('be.visible').click();
        cy.contains('Perfil').click();

        cy.contains('Productos').click();
        cy.url().should('include', '/profile');

        cy.contains('Valoraciones').click();
        cy.url().should('include', '/profile/myReviews');
    });

    it('Debería mostrar productos si existen', () => {
        cy.visit('http://localhost/login');
        cy.get('input[name="email"]').type('juan@example.com');
        cy.get('input[name="password"]').type('Password123?');
        cy.get('button[type="submit"]').click();

        cy.url().should('include', '/');

        cy.contains('Juan Pérez', { timeout: 10000 }).should('be.visible').click();

        cy.visit('/profile');
        cy.get('#productos ul li').should('exist');
    });

    it('Debería mostrar valoraciones si existen', () => {
        cy.visit('http://localhost/login');
        cy.get('input[name="email"]').type('juan@example.com');
        cy.get('input[name="password"]').type('Password123?');
        cy.get('button[type="submit"]').click();

        cy.url().should('include', '/');

        cy.contains('Juan Pérez', { timeout: 10000 }).should('be.visible').click();

        cy.visit('/profile/myReviews');
        cy.get('#valoraciones ul li').should('exist');
    });
});
