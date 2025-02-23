describe('Carrito', () => {
    it('Debería añadir un producto al carrito', () => {

        /*
         ### AÑADIR AL CARRITO ###
         */

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

        /*
        ### BORRAR ###
        */

        // Esperar a que se agregue el producto a la cesta
        cy.visit('http://localhost/carrito');

        // Hacer clic para borrar el producto de la cesta
        cy.get('[id^=decrement-button-for-]').click();

        // Esperar a que se elimine el producto de la cesta
        cy.wait(1000);

        // Asegurarse que el carrito está vacío
        cy.contains('No hay productos en el carrito').should('be.visible');

        // Asegurase que el precio total es 0
        cy.get('#totalPrice').invoke('text')
           .should('equal', '0.00 €');
        cy.get('#finalTotal').invoke('text')
            .should('equal', '0.00 €');

        /*
         ### AÑADIR UNO ###
         */

        // Ir a la página principal
        cy.visit('http://localhost');
        cy.contains('Ver detalles').click();
        cy.contains('Agregar a Cesta').click();
        cy.wait(1000);

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


        /*
        ### BORRAR UNO CUANDO LA CANTIDAD ES MAS DE UNO ###
        */

        // Hacer clic para borrar el producto de la cesta
        cy.get('[id^=decrement-button-for-]').click();

        // Asegurarse que pone 1 de ese elemento
        cy.get('[id^=amount_of_items_for_]')
            .invoke('val')
            .should('contain', '1');

        // Asegurarse que pone 1 en el carrito
        cy.get('#itemCount')
            .invoke('text')
            .should('include', '1');

        /*
        ### BORRAR EL PRODUCTO DE LA CESTA ###
        */

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
