import 'cypress-file-upload';

describe('Test de crear producto', () => {
    it('Debería crear un producto', () => {
        cy.visit('http://localhost');
        cy.contains('Account').click();
        cy.url().should('include', '/login');
        cy.wait(1000);
        cy.contains('Iniciar Sesión').should('be.visible');        
        cy.url().should('include', '/login');
        cy.get('input[name="email"]').type('juan@example.com');
        cy.get('input[name="password"]').type('Password123?');
        cy.get('button[type="submit"]').click();
        cy.url().should('include', '/');
        cy.contains('Juan Pérez').click();
        cy.contains('Perfil').click();
        cy.contains('Productos').click();
        cy.contains('Añadir Producto').click();

        // Subir imagen
        cy.get('input[type="file"]').attachFile('imagen-prueba.jpg');

        // Llenar formulario
        cy.get('input[name="nombre"]').type('Producto de Prueba');
        cy.get('textarea[name="descripcion"]').type('Descripción del producto de prueba');
        cy.contains('Nuevo').click(); 
        cy.get('input[name="stock"]').type('100');
        cy.get('input[name="precio"]').type('10.5');

        // Seleccionar categoría (asumiendo que es un <select>)
        cy.get('select[name="categoria"]').select('Tecnología');

        // Enviar formulario
        cy.url().should('include', '/producto/add');
        cy.get('button[type="submit"]').click();

        // Verificar que el producto aparece en la lista
        cy.contains('Producto de Prueba').should('be.visible');
    });
});
