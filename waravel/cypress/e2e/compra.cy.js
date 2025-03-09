describe('Hacer compra', () => {
    it('Debe realizar una compra correctamente', () => {
        cy.visit('http://localhost/login');
        cy.get('input[name="email"]').type('maria@example.com');
        cy.get('input[name="password"]').type('Password123?');
        cy.get('button[type="submit"]').click();

        cy.url().should('include', '/');
        cy.get("a[href=\"http://localhost/producto/QX9T7LK5VY3\"]").contains("Ver detalles").click();

        cy.url().should('include', '/producto/QX9T7LK5VY3');
        cy.get("#addLink").contains("Agregar a Cesta").click();
        cy.get("#cartButton").click();

        cy.url().should('include', '/carrito');
        cy.contains('Guitarra Eléctrica').should('be.visible');
        cy.get('#finalTotal').contains('300 €');
        cy.get('a[href="http://localhost/pedido/overview"]').contains('Continuar').click();

        cy.url().should('include', '/pedido/overview');
        cy.contains('Maria Garcia');
        cy.contains('maria@example.com');
        cy.contains('987654321');
        cy.contains('Calle de las Nubes, 123, 3ºC, 28971');
        cy.get('input[type="checkbox"]').click();
        cy.get('button[type="submit"]').contains('Continuar').click();

        cy.origin('https://checkout.stripe.com', () => {
            cy.get('input[name="email"]').type('maria@example.com');
            cy.get('#card-accordion-item-button').click();
            cy.get('input[name="cardNumber"]').type('4242424242424242');
            cy.get('input[name="cardExpiry"]').type('1230');
            cy.get('input[name="cardCvc"]').type('123');
            cy.get('input[name="billingName"]').type('maria');
            cy.get('#submit-button-lock-icon').click();
        });

        cy.url().should('include', '/pago/success');
        cy.contains('¡Gracias por tu compra!');
    })
});
