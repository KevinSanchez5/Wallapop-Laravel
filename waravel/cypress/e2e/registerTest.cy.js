describe('Test de registro de cuenta', () => {
    it('Debería completar el registro con éxito', () => {
      // Abre la página principal
      cy.visit('http://localhost');
  
      // Hacer clic en el enlace "Account"
      cy.contains('Account').click();
  
      // Verifica que redirige a la página de inicio de sesión
      cy.url().should('include', '/login');
  
      // Hacer clic en el enlace "Registrarse"
      cy.contains('Regístrate').click();
  
      // Verifica que está en la página de registro
      cy.url().should('include', '/register');
  
      // Completar la primera parte del formulario (Datos personales)
      cy.get('input[name="email"]').type('test@example.com'); // Campo de email
      cy.get('input[name="telefono"]').type('123456789'); // Campo de teléfono
      cy.get('input[name="nombre"]').type('Juan'); // Campo de nombre
      cy.get('input[name="apellidos"]').type('Pérez'); // Campo de apellidos
  
      // Hacer clic en el botón "Siguiente"
      cy.contains('Siguiente').click();
  
      // Completar la segunda parte del formulario (Dirección)
      cy.get('input[name="direccion[calle]"]').type('Calle Falsa'); // Campo de calle
      cy.get('input[name="direccion[numero]"]').type('123');
      cy.get('input[name="direccion[piso]"]').type('1');
      cy.get('input[name="direccion[letra]"]').type('a');
      cy.get('input[name="direccion[codigoPostal]"]').type('28001'); // Campo de código postal
  
      // Hacer clic en el botón "Siguiente"
      cy.contains('Siguiente').click();
  
      // Completar la tercera parte del formulario (Contraseña)
      cy.get('input[name="password"]').type('contraseña123'); // Campo de contraseña
      cy.get('input[name="password_confirmation"]').type('contraseña123'); // Campo de repetir contraseña
  
      // Hacer clic en el botón "Registrarse"
      cy.contains('Registrarse').click();
  
      // Verificar que el registro fue exitoso, por ejemplo, que redirige a la página de inicio
      cy.url().should('include', '/');
      cy.contains('Waravel').should('be.visible');
    });
  });
  