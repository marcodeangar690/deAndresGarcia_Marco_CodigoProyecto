<!DOCTYPE html>
<html lang="es">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Contacto | Eventium</title>
    <link
      rel="icon"
      href="data:image/svg+xml,%3Csvg%20xmlns='http://www.w3.org/2000/svg'%20width='32'%20height='32'%3E%3Crect%20width='100%25'%20height='100%25'%20fill='black'/%3E%3Ctext%20x='50%25'%20y='50%25'%20font-family='Arial,Helvetica,sans-serif'%20font-size='20'%20fill='white'%20text-anchor='middle'%20dominant-baseline='central'%3EE%3C/text%3E%3C/svg%3E">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <style>
      body {
        font-family: 'Segoe UI', sans-serif;
        background: linear-gradient(135deg, #e0e7ff, #fce7f3);
      }
      .glass {
        background: rgba(255, 255, 255, 0.65);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
      }
      .icon-box:hover {
        transform: translateY(-5px);
        transition: all 0.3s ease-in-out;
      }
    </style>
  </head>
  <body class="min-h-screen flex flex-col">
    <?php include 'includes/headerContacto.php'; ?>

    <main class="flex-grow">
      <section
        class="text-center py-12 px-4 sm:py-16 sm:px-6 bg-gradient-to-r from-blue-600 to-purple-600 text-white shadow-lg">
        <h1 class="text-3xl sm:text-5xl font-extrabold mb-4">¿Hablamos?</h1>
        <p class="text-base sm:text-lg max-w-xl mx-auto">Cuéntanos tu consulta o duda y nuestro equipo te responderá lo antes posible.</p>
      </section>
      <section class="max-w-6xl mx-auto grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6 py-16 px-4 sm:px-6">
        <div class="glass text-center rounded-xl p-6 sm:p-8 shadow-lg transition-transform transform hover:-translate-y-2 hover:shadow-2xl duration-300 cursor-pointer icon-box">
          <i class="ri-mail-line text-4xl sm:text-5xl text-blue-600 mb-4 animate-bounce"></i>
          <h3 class="text-xl font-semibold text-gray-800 mb-2">Correo</h3>
          <p class="text-gray-600 text-sm sm:text-base">contacto.evenly@gmail.com</p>
        </div>

        <div class="glass text-center rounded-xl p-6 sm:p-8 shadow-lg transition-transform transform hover:-translate-y-2 hover:shadow-2xl duration-300 cursor-pointer icon-box">
          <i class="ri-phone-line text-4xl sm:text-5xl text-green-600 mb-4 animate-bounce"></i>
          <h3 class="text-xl font-semibold text-gray-800 mb-2">Teléfono</h3>
          <p class="text-gray-600 text-sm sm:text-base">+34 644 758 006</p>
        </div>

        <div class="glass text-center rounded-xl p-6 sm:p-8 shadow-lg transition-transform transform hover:-translate-y-2 hover:shadow-2xl duration-300 cursor-pointer icon-box">
          <i class="ri-time-line text-4xl sm:text-5xl text-purple-600 mb-4 animate-bounce"></i>
          <h3 class="text-xl font-semibold text-gray-800 mb-2">Horario</h3>
          <p class="text-gray-600 text-sm sm:text-base">Lunes a Viernes<br />9:00 - 15:00</p>
        </div>
      </section>

      <section class="bg-white py-12 sm:py-16 px-4 sm:px-6">
        <h2 class="text-2xl sm:text-4xl font-bold text-center text-gray-800 mb-8">Preguntas Frecuentes</h2>
        <div class="max-w-4xl mx-auto space-y-4">
          <?php
            $faq = [
              "¿Cómo me registro como usuario?" => "Para poder registrarte tienes que ir al apartado de registro situado en la parte superior derecha y rellenar el formulario.",
              "¿Cómo funciona el panel de empresas?" => "Las empresas pueden crear, editar o eliminar eventos desde su propio panel y esos eventos luego serán visibles para los usuarios en el apartado de eventos.",
              "¿Puedo inscribirme a eventos gratuitos?" => "Sí, puedes comprar entradas tanto gratuitas como con coste.",
              "¿Los pagos cómo se gestionan?" => "Los pagos se realizarán por medio de PayPal o tarjeta bancaria para una mayor seguridad.",
              "¿Qué pasa si olvido mi contraseña?" => "Una de las implementaciones futuras será una opción para poder recuperar tu contraseña por medio del correo electrónico.",
              "¿Qué hago si tengo un problema técnico?" => "Para cualquier tipo de problema te puedes poner en contacto a través del correo contacto.evenly@gmail.com."
            ];

            $i = 0;
            foreach ($faq as $pregunta => $respuesta):
              $id = "faq$i";
          ?>
          <div class="border border-blue-100 rounded-lg shadow hover:shadow-md transition">
            <button onclick="document.getElementById('<?php echo $id; ?>').classList.toggle('hidden')" class="w-full flex justify-between items-center px-4 sm:px-6 py-3 sm:py-4 text-left text-blue-700 font-semibold focus:outline-none">
              <span class="text-sm sm:text-base"><?php echo $pregunta; ?></span>
              <i class="ri-arrow-down-s-line text-xl sm:text-2xl"></i>
            </button>
            <div id="<?php echo $id; ?>" class="hidden px-4 sm:px-6 pb-4 text-gray-700 text-sm sm:text-base">
              <?php echo $respuesta; ?>
            </div>
          </div>
          <?php $i++; endforeach; ?>
        </div>
      </section>

      <section class="py-12 sm:py-16 px-4 sm:px-6 bg-gradient-to-r from-blue-100 to-purple-100">
        <div class="max-w-4xl mx-auto bg-white p-6 sm:p-10 rounded-xl shadow-2xl" data-aos="zoom-in">
          <h2 class="text-2xl sm:text-3xl font-bold text-blue-700 mb-6 text-center">Contáctanos</h2>
          <form class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
            <input type="text" placeholder="Nombre completo" required class="px-4 py-2 sm:px-5 sm:py-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-400 focus:outline-none text-sm sm:text-base">
            <input type="email" placeholder="Correo electrónico" required class="px-4 py-2 sm:px-5 sm:py-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-400 focus:outline-none text-sm sm:text-base">
            <select required class="md:col-span-2 px-4 py-2 sm:px-5 sm:py-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-400 focus:outline-none text-sm sm:text-base">
              <option value="">Motivo de contacto</option>
              <option>Consultas generales</option>
              <option>Soporte técnico</option>
              <option>Problemas de acceso</option>
              <option>Empresas colaboradoras</option>
            </select>
            <textarea placeholder="Escribe tu mensaje..." rows="5" required class="md:col-span-2 px-4 py-2 sm:px-5 sm:py-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-400 focus:outline-none text-sm sm:text-base"></textarea>
            <button type="submit" class="md:col-span-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 sm:py-3 rounded-lg transition text-sm sm:text-base">Enviar Mensaje</button>
          </form>
        </div>
      </section>
    </main>

    <?php include 'includes/footerContacto.php'; ?>

    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
      AOS.init({ once: true });
    </script>
  </body>
</html>
