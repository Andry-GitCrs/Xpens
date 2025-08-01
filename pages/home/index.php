<?php 
  require_once __DIR__ . '/../../config/db.php';
  $button = [
    'label' => 'Sing in',
    'link' => '/pages/login'
  ];
  if(isset($_SESSION['user'])) {
    $button['label'] = 'Mine';
    $button['link'] = '/pages/dashboard';
  }
?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
  <meta charset="UTF-8"/>
  <title>Xpens – Track Your Expenses Effortlessly</title>
  <meta name="viewport" content="width=device-width, initial-scale=1"/>

  <!-- Tailwind CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
  <!-- Font Awesome 6 CDN -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" integrity="sha512-..." crossorigin="anonymous" referrerpolicy="no-referrer"/>
  <link rel="stylesheet" href="../../assets/css/style.css">
  <script src="../../assets/js/tailwind.js"></script>
  <script>
    tailwind.config = { darkMode: 'class' };
  </script>
</head>

<body class="bg-slate-50 dark:bg-slate-900 text-slate-800 dark:text-slate-100 font-sans">

  <!-- Header -->
  <header class="sticky top-0 z-40 bg-white/70 dark:bg-slate-900/70 backdrop-blur-md border-b border-slate-200 dark:border-slate-700">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 py-4 flex justify-between items-center">
      <a href="#" class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">
        <i class="fa-solid fa-wallet mr-2"></i><span class="text-yellow-500">X</span>pens
      </a>
      <div class="flex items-center justify-between gap-8">
        <nav class="hidden md:flex items-center space-x-6">
          <a href="#features" class="hover:text-indigo-600 dark:hover:text-indigo-400">Features</a>
          <a href="#how" class="hover:text-indigo-600 dark:hover:text-indigo-400">How it works</a>
          <a href="#pricing" class="hover:text-indigo-600 dark:hover:text-indigo-400">Pricing</a>
        </nav>
        <div class="flex items-center space-x-3">
          <a href="<?php echo $button['link'] ?>"
            class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-indigo-700">
            <?php echo $button['label'] ?>
          </a>
          <a href="/pages/register"
            class="bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-green-700">
            Get Started
          </a>
        </div>
      </div>
    </div>
  </header>

  <!-- Hero -->
  <section class="max-w-6xl h-[70vh] mx-auto px-4 sm:px-6 py-24 text-center">
    <h1 class="text-4xl md:text-6xl font-extrabold">
      Track expenses <span class="text-indigo-600 dark:text-indigo-400">without the hassle</span>
    </h1>
    <p class="mt-6 max-w-2xl mx-auto text-lg text-slate-600 dark:text-slate-400">
      Create shopping lists, add products, and monitor every purchase in one beautiful dashboard.
    </p>
    <a href="/pages/register"
       class="mt-8 inline-flex items-center px-6 py-3 bg-indigo-600 text-white rounded-lg font-semibold hover:bg-indigo-700">
      <i class="fa-solid fa-rocket mr-2"></i>Start for Free
    </a>
  </section>

  <!-- Features -->
  <section id="features" class="bg-slate-100 dark:bg-slate-800/50">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 py-16">
      <h2 class="text-3xl font-bold text-center mb-12">Everything you need in one place</h2>
      <div class="grid md:grid-cols-3 gap-8 text-center">
        <!-- Card 1 -->
        <div class="p-6">
          <i class="fa-solid fa-list-check text-4xl text-indigo-600 dark:text-indigo-400 mb-4"></i>
          <h3 class="font-semibold text-xl mb-2">Smart Lists</h3>
          <p class="text-slate-600 dark:text-slate-400">Create and manage multiple shopping lists with ease.</p>
        </div>
        <!-- Card 2 -->
        <div class="p-6">
          <i class="fa-solid fa-barcode text-4xl text-indigo-600 dark:text-indigo-400 mb-4"></i>
          <h3 class="font-semibold text-xl mb-2">Product Catalog</h3>
          <p class="text-slate-600 dark:text-slate-400">Add, edit and remove products on the fly.</p>
        </div>
        <!-- Card 3 -->
        <div class="p-6">
          <i class="fa-solid fa-chart-line text-4xl text-indigo-600 dark:text-indigo-400 mb-4"></i>
          <h3 class="font-semibold text-xl mb-2">Real-time Charts</h3>
          <p class="text-slate-600 dark:text-slate-400">Visualize your spending by day, product or list.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- How it works -->
  <section id="how" class="max-w-6xl mx-auto px-4 sm:px-6 py-16">
    <h2 class="text-3xl font-bold text-center mb-12">How it works</h2>
    <div class="grid md:grid-cols-3 gap-8 text-center">
      <div>
        <i class="fa-solid fa-user-plus fa-3x text-indigo-600 dark:text-indigo-400 mb-3"></i>
        <h3 class="font-semibold mb-1">1. Sign up</h3>
        <p class="text-sm text-slate-600 dark:text-slate-400">Create an account in 10 seconds.</p>
      </div>
      <div>
        <i class="fa-solid fa-cart-plus fa-3x text-indigo-600 dark:text-indigo-400 mb-3"></i>
        <h3 class="font-semibold mb-1">2. Add purchases</h3>
        <p class="text-sm text-slate-600 dark:text-slate-400">Log every item you buy.</p>
      </div>
      <div>
        <i class="fa-solid fa-eye fa-3x text-indigo-600 dark:text-indigo-400 mb-3"></i>
        <h3 class="font-semibold mb-1">3. Watch the insights</h3>
        <p class="text-sm text-slate-600 dark:text-slate-400">Know exactly where your money goes.</p>
      </div>
    </div>
  </section>

  <!-- Pricing -->
  <section id="pricing" class="bg-slate-100 dark:bg-slate-800/50">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 py-16">
      <h2 class="text-3xl font-bold text-center mb-12">Pricing</h2>
      <div class="grid md:grid-cols-2 gap-8">
        <!-- Free -->
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow p-6">
          <h3 class="text-xl font-semibold">Free</h3>
          <p class="text-3xl font-bold my-2">Ar 0<span class="text-sm text-slate-500 dark:text-slate-400"> / month</span></p>
          <ul class="space-y-2 mb-6 text-slate-600 dark:text-slate-400">
            <li><i class="fa-solid fa-check text-green-500 mr-2"></i>Unlimited lists</li>
            <li><i class="fa-solid fa-check text-green-500 mr-2"></i>Up to 100 products</li>
            <li><i class="fa-solid fa-check text-green-500 mr-2"></i>Basic charts</li>
          </ul>
          <a href="/pages/register"
             class="w-full block text-center bg-green-100 dark:bg-green-500 text-indigo-700 dark:text-white px-4 py-2 rounded-lg font-semibold hover:bg-indigo-200 dark:hover:bg-green-800">
            Get Started
          </a>
        </div>
        <!-- Pro -->
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow p-6 border-2 border-indigo-600">
          <span class="text-xs bg-indigo-100 dark:bg-indigo-900 text-indigo-600 dark:text-indigo-400 font-semibold px-2 py-1 rounded-full">Most popular</span>
          <h3 class="text-xl font-semibold mt-2">Pro</h3>
          <p class="text-3xl font-bold my-2">Ar 1.6000<span class="text-sm text-slate-500 dark:text-slate-400"> / month</span></p>
          <ul class="space-y-2 mb-6 text-slate-600 dark:text-slate-400">
            <li><i class="fa-solid fa-check text-green-500 mr-2"></i>Unlimited everything</li>
            <li><i class="fa-solid fa-check text-green-500 mr-2"></i>Advanced analytics</li>
            <li><i class="fa-solid fa-check text-green-500 mr-2"></i>Priority support</li>
          </ul>
          <a href="#"
             class="w-full block text-center bg-yellow-600 text-white px-4 py-2 rounded-lg font-semibold hover:bg-yellow-700">
            Upgrade now
          </a>
        </div>
      </div>
    </div>
  </section>

  <!-- Footer -->
  <footer class="bg-slate-100 dark:bg-slate-800 border-t border-slate-200 dark:border-slate-700">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 py-8 text-center text-sm text-slate-500 dark:text-slate-400">
      <p>&copy; 2025 Xpens. Built with <i class="fa-solid fa-heart text-red-500"></i> , PHP, JS, TailwindCss + Font Awesome. By Andry</p>
      <div class="flex justify-center space-x-4 mt-4">
        <a href="#" class="hover:text-indigo-600 dark:hover:text-indigo-400"><i class="fa-brands fa-twitter"></i></a>
        <a href="https://github.com/Andry-GitCrs/Xpens" class="hover:text-indigo-600 dark:hover:text-indigo-400"><i class="fa-brands fa-github"></i></a>
        <a href="#" class="hover:text-indigo-600 dark:hover:text-indigo-400"><i class="fa-brands fa-linkedin"></i></a>
      </div>
    </div>
  </footer>
  <button id="themeToggle" class="fixed bottom-4 right-4 bg-slate-200 dark:bg-slate-700 p-3 rounded-[50%] w-[50px] h-[50px] shadow-lg">
    <i class="fa-solid fa-moon dark:hidden"></i>
    <i class="fa-solid fa-sun hidden dark:inline"></i>
  </button>

  <script>
    /* Dark/light toggle */
    document.getElementById('themeToggle').addEventListener('click', () => {
      document.documentElement.classList.toggle('dark');
      localStorage.theme = document.documentElement.classList.contains('dark') ? 'dark' : 'light';
    });
    if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
      document.documentElement.classList.add('dark');
    }
  </script>
</body>
</html>