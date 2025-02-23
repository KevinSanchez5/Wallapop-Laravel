describe('Carrito', () => {
    it('Añadir al carrito', () => {

        // Abre la página principal
        cy.visit('http://localhost');

        // Hacer clic en el enlace "Ver detalles"
        cy.contains('Ver detalles').click();

        // Hacer clic para añadir el producto a la cesta
        cy.contains('Agregar a Cesta').click();

        // Asegurarse que ha cambiado el logo del carrito
        cy.get('#itemCount')
            .invoke('text')
            .should('include', '1');

        // Hacer clic en el enlace "Cesta"
        cy.get('#cartButton').click();

        // Contiene todos los botones
        cy.contains('Continuar explorando');
        cy.contains('Eliminar');
    })
    it('Quitar uno del carrito cuando la cantidad es uno lo borra', () => {

        // Añadir un producto
        cy.visit('http://localhost');
        cy.contains('Ver detalles').click();
        cy.contains('Agregar a Cesta').click();
        cy.wait(1000);

        // Ir al carrito
        cy.visit('http://localhost/carrito');

        // Hacer clic para borrar el producto de la cesta
        cy.get('[id^=decrement-button-for-]').click();


        // Asegurarse que el carrito está vacío
        cy.contains('No hay productos en el carrito').should('be.visible');

        // Asegurase que el precio total es 0
        cy.get('#totalPrice').invoke('text')
           .should('equal', '0.00 €');
        cy.get('#finalTotal').invoke('text')
            .should('equal', '0.00 €');

    })

    it('Añadir uno de un producto en la cesta', () => {

        // Añadir un producto
        cy.visit('http://localhost');
        cy.contains('Ver detalles').click();
        cy.contains('Agregar a Cesta').click();

        // Ir al carrito
        cy.visit('http://localhost/carrito');

        // Hacer clic para añadir uno más
        cy.get('[id^=increment-button-for-]').click();

        // Hacer clic para añadir uno
        cy.get('[id^=amount_of_items_for_]')
            .invoke('val')
            .should('contain', '2');

        cy.get('#itemCount')
            .invoke('text')
            .should('include', '2');

    })

    it('Borrar uno de un producto en la cesta cuando la cantidad es más de uno', () => {

        // Añadir un producto
        cy.visit('http://localhost');
        cy.contains('Ver detalles').click();
        cy.contains('Agregar a Cesta').click();
        cy.contains('Agregar a Cesta').click();

        // Ir al carrito
        cy.visit('http://localhost/carrito');

        // Hacer clic para borrar uno
        cy.get('[id^=decrement-button-for-]').click();

        // Asegurarse que pone 1 de ese elemento
        cy.get('[id^=amount_of_items_for_]')
            .invoke('val')
            .should('contain', '1');

        // Asegurarse que pone 1 en el carrito
        cy.get('#itemCount')
            .invoke('text')
            .should('include', '1');

    })

    it('Borrar el producto de la cesta', () => {

        // Añadir un producto
        cy.visit('http://localhost');
        cy.contains('Ver detalles').click();
        cy.contains('Agregar a Cesta').click();

        // Ir al carrito
        cy.visit('http://localhost/carrito');

        // Hacer clic para borrar el producto de la cesta
        cy.get('[id^=delete-button-for-]').click();

        //Asegurarse que pone 0 en el carrito
        cy.get('#itemCount')
            .invoke('text')
            .should('include', '0');

        // Asegurarse que el carrito está vacío
        cy.contains('No hay productos en el carrito').should('be.visible');

        // Asegurase que el precio total es 0
        cy.get('#totalPrice').invoke('text')
            .should('equal', '0.00 €');
        cy.get('#finalTotal').invoke('text')
            .should('equal', '0.00 €');

    })

})
