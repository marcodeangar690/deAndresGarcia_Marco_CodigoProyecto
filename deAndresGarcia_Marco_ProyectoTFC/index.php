<?php
require_once "conexion.php";
  //top 3 eventos
  $topStmt = $conexion->query("
    SELECT * 
    FROM eventos 
    ORDER BY aforo DESC 
    LIMIT 3
  ");
  $topEventos = $topStmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="icon" href="data:image/svg+xml,%3Csvg%20xmlns='http://www.w3.org/2000/svg'%20width='32'%20height='32'%3E%3Crect%20width='100%25'%20height='100%25'%20fill='black'/%3E%3Ctext%20x='50%25'%20y='50%25'%20font-family='Arial,Helvetica,sans-serif'%20font-size='20'%20fill='white'%20text-anchor='middle'%20dominant-baseline='central'%3EE%3C/text%3E%3C/svg%3E">
  <title>Bienvenido a Evenly</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    /*animaciones*/
    .fade-in { animation: fadeIn 1s ease-out forwards; opacity: 0; }
    @keyframes fadeIn { to { opacity: 1; } }

    .perspective { perspective: 1000px; }
    .transform-style-preserve-3d { transform-style: preserve-3d; }
    .backface-hidden { backface-visibility: hidden; }
    .rotate-y-180 { transform: rotateY(180deg); }

    /*cartas de testimonios*/
      .testimonial-card {
        background-color: rgba(255,255,255,0.8);
        backdrop-filter: blur(10px);
        border-radius: 1rem;
        padding: 2rem;
        box-shadow: 0 10px 20px rgba(0,0,0,0.05);
        transition: transform 0.4s ease, box-shadow 0.4s ease;
      }
      .testimonial-card:hover {
        transform: translateY(-8px) scale(1.02);
        box-shadow: 0 15px 25px rgba(0,0,0,0.1);
      }
      /*estilo para cartas*/
      .testimonial-card blockquote {
        position: relative;
        font-style: italic;
        color: #374151;
        margin-bottom: 1.5rem;
        padding-left: 1.5rem;
        border-left: 4px solid #3b82f6;
      }
      .testimonial-card blockquote::before {
        content: '“';
        position: absolute;
        top: -0.5rem;
        left: -0.8rem;
        font-size: 4rem;
        color: rgba(59,130,246,0.2);
      }
      /*estilo autor*/
      .testimonial-author {
        font-weight: 600;
        color: #1f2937;
      }
      .testimonial-role {
        font-size: 0.875rem;
        color: #6b7280;
      }
      /*splide arrows*/
      .splide__arrow {
        background: #ffffff;
        color: #3b82f6;
        width: 2.5rem;
        height: 2.5rem;
        border-radius: 9999px;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        transition: background 0.3s ease;
      }
      .splide__arrow:hover {
        background: #3b82f6;
        color: #ffffff;
      }
      .splide__pagination__page {
        background: #cbd5e1;
        width: 0.75rem;
        height: 0.75rem;
        opacity: 1;
      }
      .splide__pagination__page.is-active {
        background: #3b82f6;
      }

      @keyframes gradientBG {
      0% { background-position: 0% 50%; }
      50% { background-position: 100% 50%; }
      100% { background-position: 0% 50%; }
    }
    /*estilo secciones*/
    section {
      background-size: 200% 200%;
      animation: gradientBG 10s ease infinite;
    }
    @keyframes bounce-alt {
      0%,100% { transform: translateY(0); }
      50% { transform: translateY(-6px); }
    }
    .animate-bounce-alternate { animation: bounce-alt 3s infinite; }
  </style>
</head>
<body class="bg-gray-50 text-gray-800">

  <?php include 'includes/headerIndex.php'; ?>

  <!-- seccion hero -->
  <section class="relative h-screen bg-gradient-to-br from-blue-600 to-purple-600 flex items-center">
    <img src="images/evento.webp" alt="Eventos" class="absolute inset-0 w-full h-full object-cover opacity-30">
    <div class="relative container mx-auto px-6 lg:px-0 text-center text-white fade-in">
      <h1 class="text-5xl lg:text-6xl font-extrabold leading-tight">
        Organiza y Vive <span class="text-yellow-300">Eventos Únicos</span>
      </h1>
      <p class="mt-4 text-lg lg:text-xl max-w-2xl mx-auto">
        Desde festivales hasta obras de teatro, encuentra y gestiona experiencias inolvidables.
      </p>
      <div class="mt-8 space-x-4">
        <a href="registro.php"
           class="px-8 py-4 bg-yellow-300 text-gray-800 font-semibold rounded-full shadow-lg hover:bg-yellow-400 transition">
          Regístrate Gratis
        </a>
        <a href="eventos.php"
           class="px-8 py-4 border-2 border-white rounded-full hover:bg-white hover:text-gray-800 transition">
          Ver Eventos
        </a>
      </div>
    </div>
  </section>

<!-- seccion caracteristicas -->
<section class="py-24 bg-gray-100 relative overflow-hidden">
  <div class="blob-1 absolute top-0 -left-16 w-64 h-64 bg-blue-300 rounded-full mix-blend-multiply filter blur-3xl opacity-30"></div>
  <div class="blob-2 absolute bottom-0 -right-16 w-72 h-72 bg-purple-300 rounded-full mix-blend-multiply filter blur-3xl opacity-30"></div>

  <div class="container mx-auto px-6 lg:px-0 relative z-10 text-center">
    <h2 class="text-5xl font-extrabold text-gray-900 mb-4">Descubre lo que hace <span class="text-blue-600">único</span> a Evenly</h2>
    <p class="text-lg text-gray-700 mb-12">Funciones diseñadas para conseguir llevar tu experiencia de eventos al siguiente nivel de una manera 
        sencilla y optima.</p>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
      <!-- carta 1 -->
      <div class="feature-card bg-white bg-opacity-80 backdrop-blur-md rounded-3xl p-8 shadow-xl transform transition-all duration-500 opacity-0 translate-y-8" data-tilt data-tilt-max="10" data-tilt-speed="400">
        <div class="icon-container w-16 h-16 mx-auto flex items-center justify-center bg-gradient-to-tr from-blue-400 to-blue-600 text-white rounded-full mb-4 shadow-lg">
          <i class="ri-rocket-line text-3xl"></i>
        </div>
        <h3 class="text-2xl font-semibold text-gray-900 mb-2">Lanzamiento Instantáneo</h3>
        <p class="text-gray-600">Publica tu evento en segundos con nuestra interfaz ultra-rápida y sencilla de utilizar, en minutos tienes tu evento publicado.</p>
        <div class="mt-4">
          <span class="counter text-3xl font-bold" data-target="250">0</span>+
        </div>
      </div>

      <!-- carta 2 -->
      <div class="feature-card bg-white bg-opacity-80 backdrop-blur-md rounded-3xl p-8 shadow-xl transform transition-all duration-500 opacity-0 translate-y-8" data-tilt data-tilt-max="10" data-tilt-speed="400">
        <div class="icon-container w-16 h-16 mx-auto flex items-center justify-center bg-gradient-to-tr from-purple-400 to-purple-600 text-white rounded-full mb-4 shadow-lg">
          <i class="ri-calendar-check-line text-3xl"></i>
        </div>
        <h3 class="text-2xl font-semibold text-gray-900 mb-2">Simpleza y robusted </h3>
        <p class="text-gray-600">Tus eventos se registraran y guardaran de manera robusta y eficaz hazta el dia que decidas eliminarlos.</p>
        <div class="mt-4">
        </div>
      </div>

      <!-- carta 3 -->
      <div class="feature-card bg-white bg-opacity-80 backdrop-blur-md rounded-3xl p-8 shadow-xl transform transition-all duration-500 opacity-0 translate-y-8" data-tilt data-tilt-max="10" data-tilt-speed="400">
        <div class="icon-container w-16 h-16 mx-auto flex items-center justify-center bg-gradient-to-tr from-green-400 to-green-600 text-white rounded-full mb-4 shadow-lg">
          <i class="ri-bar-chart-line text-3xl"></i>
        </div>
        <h3 class="text-2xl font-semibold text-gray-900 mb-2">Usuarios</h3>
        <p class="text-gray-600">Contamos con mas de 100 usuarios que usan la página a diario y mas de 50 empresas organizadoras de eventos.</p>
        <div class="mt-4">
          <span class="counter text-3xl font-bold" data-target="150">0</span>+
        </div>
      </div>

      <!-- carta 4 -->
      <div class="feature-card bg-white bg-opacity-80 backdrop-blur-md rounded-3xl p-8 shadow-xl transform transition-all duration-500 opacity-0 translate-y-8" data-tilt data-tilt-max="10" data-tilt-speed="400">
        <div class="icon-container w-16 h-16 mx-auto flex items-center justify-center bg-gradient-to-tr from-yellow-400 to-yellow-600 text-white rounded-full mb-4 shadow-lg">
          <i class="ri-global-line text-3xl"></i>
        </div>
        <h3 class="text-2xl font-semibold text-gray-900 mb-2">Pago efectivo</h3>
        <p class="text-gray-600">Realiza los pagos de las entradas mediante PayPal y tarjeta de crédito para una mayor confianza y seguridad.</p>
        <div class="mt-4">
        </div>
      </div>
    </div>
  </div>

  <!-- estilo para los blob y para la hora de pulsar el icono -->
  <style>
    @keyframes blob-move { 0%,100% { transform: translate(0,0) scale(1);} 33%{transform:translate(30px,-50px) scale(1.1);}66%{transform:translate(-20px,20px) scale(0.9);} }
    .blob-1 { animation: blob-move 8s infinite; }
    .blob-2 { animation: blob-move 8s infinite 2s; }
    @keyframes pulse-slow { 0%,100% { transform: scale(1);} 50% { transform: scale(1.1);} }
    .icon-container:hover { animation: pulse-slow 1s infinite; }
  </style>

</section>


  <!-- Seccion eventos -->
<section class="py-16 bg-gray-50">
  <div class="container mx-auto px-6 lg:px-0 text-center fade-in">
    <h2 class="text-5xl font-extrabold text-gray-900 mb-4">Eventos <span class="text-blue-600">destacados</span> disponibles</h2><br>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
      <?php foreach($topEventos as $evento): ?>
      <div class="bg-white rounded-xl overflow-hidden shadow hover:shadow-lg transition">
        <?php if($evento['imagen']): ?>
        <div class="relative w-full pb-[56.25%] overflow-hidden">
          <img
            src="uploads_eventos/<?= htmlspecialchars($evento['imagen']) ?>"
            alt="<?= htmlspecialchars($evento['nombre']) ?>"
            class="absolute inset-0 w-full h-full object-cover group-hover:scale-110 transition-transform duration-300"
          />
        </div>
        <?php endif; ?>

        <div class="p-4 space-y-2">
          <h3 class="text-xl font-semibold text-gray-800 truncate">
            <?= htmlspecialchars($evento['nombre']) ?>
          </h3>
          <div class="flex items-center text-sm text-gray-500 gap-2">
            <i class="ri-map-pin-line"></i>
            <span><?= htmlspecialchars($evento['ubicacion']) ?></span>
          </div>
          <div class="flex items-center text-sm text-gray-500 gap-2">
            <i class="ri-calendar-line"></i>
            <span><?= date('d M Y', strtotime($evento['fecha'])) ?></span>
          </div>
          <div class="flex items-center justify-between mt-3">
            <span class="text-lg font-bold text-blue-600">
              <?= htmlspecialchars($evento['precio']) ?> €
            </span>
            <a href="eventos.php"
               class="text-sm bg-blue-600 text-white px-3 py-1 rounded-full hover:bg-blue-700 transition">
              Ver más
            </a>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- carrusel de testimonios -->
<section class="py-20 bg-gray-50">
  <div class="container mx-auto px-6 lg:px-0">
    <h2 class="text-4xl font-extrabold text-center text-gray-900 mb-12">Lo que dicen nuestros usuarios</h2>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@splidejs/splide@latest/dist/css/themes/splide-default.min.css">
    <div id="testimonial-slider" class="splide" aria-label="Testimonios">
      <div class="splide__track">
        <ul class="splide__list">
          <!-- 1º testimonio -->
          <li class="splide__slide flex justify-center">
            <div class="testimonial-card max-w-md text-center">
              <blockquote>
                Gracias a Evenly encontre la entrada del evento al que queria asistir sin tener que buscar en muchas páginas. ¡Increíble!
              </blockquote>
              <div class="flex justify-center mb-4">
                <i class="ri-star-fill text-yellow-400"></i>
                <i class="ri-star-fill text-yellow-400"></i>
                <i class="ri-star-fill text-yellow-400"></i>
                <i class="ri-star-fill text-yellow-400"></i>
                <i class="ri-star-fill text-yellow-400"></i>
              </div>
              <div class="testimonial-author">Raquel García Rodríguez</div>
              <div class="testimonial-role">Amante de la música</div>
            </div>
          </li>
          <!-- 2º testimonio -->
          <li class="splide__slide flex justify-center">
            <div class="testimonial-card max-w-md text-center">
              <blockquote>
                La plataforma es muy intuitiva y sencilla para poder registrar los eventos.
              </blockquote>
              <div class="flex justify-center mb-4">
                <i class="ri-star-fill text-yellow-400"></i>
                <i class="ri-star-fill text-yellow-400"></i>
                <i class="ri-star-fill text-yellow-400"></i>
                <i class="ri-star-fill text-yellow-400"></i>
                <i class="ri-star-fill text-yellow-400"></i>
              </div>
              <div class="testimonial-author">Carlos Gómez</div>
              <div class="testimonial-role">Coordinador de Eventos</div>
            </div>
          </li>
          <!-- 3º testimonio -->
          <li class="splide__slide flex justify-center">
            <div class="testimonial-card max-w-md text-center">
              <blockquote>
                Gran cantidad de eventos y de diversos tipos, muy recomendada si no quieres tener que navegar por muchas paginas para encontrar el evento que quieres.
              </blockquote>
              <div class="flex justify-center mb-4">
                <i class="ri-star-fill text-yellow-400"></i>
                <i class="ri-star-fill text-yellow-400"></i>
                <i class="ri-star-fill text-yellow-400"></i>
                <i class="ri-star-fill text-yellow-400"></i>
                <i class="ri-star-fill text-yellow-400"></i>
              </div>
              <div class="testimonial-author">Ana Ruiz Matamoros</div>
              <div class="testimonial-role">Organizadora Social</div>
            </div>
          </li>
          <!-- 4º testimonio -->
          <li class="splide__slide flex justify-center">
            <div class="testimonial-card max-w-md text-center">
              <blockquote>
                El soporte de la página me ayudo mucho durante la hora del registro de mi evento, me sentí acompañado en todo momento.
              </blockquote>
              <div class="flex justify-center mb-4">
                <i class="ri-star-fill text-yellow-400"></i>
                <i class="ri-star-fill text-yellow-400"></i>
                <i class="ri-star-fill text-yellow-400"></i>
                <i class="ri-star-fill text-yellow-400"></i>
                <i class="ri-star-fill text-yellow-400"></i>
              </div>
              <div class="testimonial-author">Raúl Rernández</div>
              <div class="testimonial-role">Creador de eventos sociales</div>
            </div>
          </li>
        </ul>
      </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/@splidejs/splide@latest/dist/js/splide.min.js"></script>
    <script>
      document.addEventListener('DOMContentLoaded', () => {
        new Splide('#testimonial-slider', {
          type      : 'loop',
          perPage   : 1,
          autoplay  : true,
          interval  : 5000,
          speed     : 700,
          pagination: true,
          arrows    : true,
        }).mount();
      });
    </script>
  </div>
</section>

<!-- particulas y contador del final -->
<section class="relative py-24 bg-gradient-to-r from-purple-600 to-blue-600 text-white overflow-hidden">
  <canvas id="particles-canvas" class="absolute inset-0 w-full h-full pointer-events-none"></canvas>

  <div class="relative z-10 container mx-auto px-6 lg:px-0 text-center">
    <h2 class="text-5xl font-extrabold mb-4">¡Haz realidad tu evento hoy!</h2>
    <p class="text-lg mb-8">
      Ya son más de <span id="userCount" class="font-semibold">0</span> los eventos que tenemos. <br>
      ¡Si eres una empresa no dudes en unirte!
    </p>
    <a href="registro_empresa.php"
       class="inline-flex items-center px-10 py-4 bg-yellow-300 text-gray-800 font-semibold rounded-full shadow-lg hover:bg-yellow-400 focus:outline-none transform transition hover:scale-105 animate-bounce-alternate">
      Unirme a Evenly&nbsp;<i class="ri-rocket-line text-2xl"></i>
    </a>
  </div>

  <!-- contador -->
  <script src="https://cdn.jsdelivr.net/npm/tsparticles@2/tsparticles.bundle.min.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      //particulas 
      tsParticles.load('particles-canvas', {
        fullScreen: { enable: false },
        particles: {
          number: { value: 40, density: { enable: false } },
          size: { value: 3 },
          move: { enable: true, speed: 0.5, outModes: { default: 'out' } },
          opacity: { value: 0.6 },
        },
        interactivity: {
          detectsOn: 'canvas',
          events: {
            onhover: { enable: true, mode: 'repulse' },
            resize: true,
          },
          modes: { repulse: { distance: 100, speed: 0.5 } },
        },
      });

      //contador
      (function countUp(el, target) {
        let count = 0, step = target / 200;
        function update() {
          count += step;
          el.textContent = Math.floor(count).toLocaleString();
          if (count < target) requestAnimationFrame(update);
        }
        update();
      })(document.getElementById('userCount'), 100);
    });
    
    document.addEventListener('DOMContentLoaded', () => {
      //scroll
      const cards = document.querySelectorAll('.feature-card');
      const observer = new IntersectionObserver((entries, obs) => {
        entries.forEach(entry => {
          if (entry.isIntersecting) {
            entry.target.classList.add('opacity-100', 'translate-y-0');
            obs.unobserve(entry.target);
          }
        });
      }, { threshold: 0.2 });
      cards.forEach(c => observer.observe(c));

      VanillaTilt.init(cards, { max: 10, speed: 400, glare: true, 'max-glare': 0.2 });

      //contador
      const counters = document.querySelectorAll('.counter');
      const countObserver = new IntersectionObserver((entries, obs2) => {
        entries.forEach(entry => {
          if (entry.isIntersecting) {
            const el = entry.target;
            const target = +el.dataset.target;
            let count = 0;
            const step = target / 200;
            const update = () => {
              count += step;
              el.textContent = Math.floor(count).toLocaleString();
              if (count < target) requestAnimationFrame(update);
            };
            update();
            obs2.unobserve(el);
          }
        });
      }, { threshold: 0.5 });
      counters.forEach(c => countObserver.observe(c));

      const blob1 = document.querySelector('.blob-1');
      const blob2 = document.querySelector('.blob-2');
      window.addEventListener('scroll', () => {
        const y = window.scrollY;
        blob1.style.transform = `translateY(${y * 0.2}px)`;
        blob2.style.transform = `translateY(-${y * 0.15}px)`;
      });
    });
    
  </script>
</section>

  <?php include 'includes/footerIndex.php'; ?>

</body>
</html>
<script src="https://unpkg.com/vanilla-tilt@1.7.0/dist/vanilla-tilt.min.js"></script>
