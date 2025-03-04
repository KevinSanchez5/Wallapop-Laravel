describe('Página de Inicio', () => {
    beforeEach(() => {
        cy.visit('/');
    });

    it('Debe cargar correctamente los elementos principales', () => {
        cy.title().should('include', 'Página de Inicio');
        cy.get('header').should('exist');
        cy.get('input[name="search"]').should('be.visible');

        const categorias = ['Todos', 'Tecnologia', 'Musica', 'Ropa'];
        categorias.forEach((categoria) => {
          cy.contains(categoria).should('exist');
        });
    });

    it('Debe interactuar con el menú de categorías', () => {
        cy.get('button[name="categoria"]').should('be.visible');
    });

    it('Debe realizar una búsqueda y mostrar resultados', () => {
        cy.get('input[name="search"]')
          .type('portatil')
          .blur()
          .type('{enter}');

    cy.url().should('include', 'search=portatil');
        cy.get('#productos-container').find('div').should('have.length.at.least', 1);
    });

    it('Debe permitir ver detalles de un producto', () => {
        cy.get('#productos-container a').first().click();
        cy.url().should('include', '/producto/');
    });

    it('Debe permitir ver vendedor de un producto', () => {
        cy.get('#productos-container a').first().click();
        cy.url().should('include', '/producto/');

        cy.contains('Juan Perez').click();
        cy.url().should('include', '/cliente/');
    });
});
